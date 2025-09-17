<?php
// /api/orders.php (mysqli version)
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db.php';   // must define $conn = new mysqli(...)

if (!($conn instanceof mysqli)) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'DB connection not found']);
  exit;
}
$conn->set_charset('utf8mb4');

function bad($m,$c=400){ http_response_code($c); echo json_encode(['ok'=>false,'error'=>$m]); exit; }

$action = strtolower($_GET['action'] ?? 'list');

function map_pay($s){ $m=['paid'=>'Paid','pending'=>'Pending','failed'=>'Failed']; return $m[strtolower($s??'pending')]??'Pending'; }
function map_ful($s){ $m=['new'=>'New','packed'=>'Packed','shipped'=>'Shipped','complete'=>'Complete']; return $m[strtolower($s??'new')]??'New'; }
function order_code_or_new($code){ $code=trim((string)$code); return $code!==''?$code:'ORD-'.date('Y').'-'.strtoupper(bin2hex(random_bytes(3))); }
function parse_client_dt($s){ if(!$s) return null; $t=strtotime($s); return $t?date('Y-m-d H:i:s',$t):null; }

/* ---------------- LIST ---------------- */
if ($action === 'list') {
  $orders=[];
  $q=$conn->query("SELECT * FROM orders ORDER BY created_at DESC");
  while($o=$q->fetch_assoc()){
    $oid=(int)$o['id'];
    $it=$conn->query("SELECT oi.product_id, oi.qty, oi.unit_price, b.title
                      FROM order_items oi
                      LEFT JOIN books b ON b.id=oi.product_id
                      WHERE oi.order_id={$oid}");
    $items=[];
    while($r=$it->fetch_assoc()) $items[]=$r;
    $o['items']=$items;
    $orders[]=$o;
  }
  echo json_encode($orders);
  exit;
}

/* --------- CREATE & UPDATE --------- */
if ($action==='create' || $action==='update') {
  $input = json_decode(file_get_contents('php://input'), true);
  if (!$input) bad('Invalid JSON body');

  $order_code = order_code_or_new($input['orderId'] ?? '');
  $buyer_name = trim($input['buyer']['name']  ?? '');
  $buyer_email= trim($input['buyer']['email'] ?? '');
  $payment_status     = map_pay($input['paymentStatus'] ?? 'pending');
  $fulfillment_status = map_ful($input['fulfillmentStatus'] ?? 'new');
  $payment_ref = trim($input['paymentRef'] ?? '');
  $notes       = trim($input['notes'] ?? '');
  $created_at  = parse_client_dt($input['createdAt'] ?? null);

  $items = is_array($input['items'] ?? null)? $input['items']: [];
  if (!$items) bad('Order must contain at least one item');

  $total_amount = 0.0;
  foreach ($items as $it) {
    $total_amount += ((int)($it['qty'] ?? 0)) * ((float)($it['unitPrice'] ?? 0));
  }

  try {
    $conn->begin_transaction();

    if ($action==='create') {
      if ($created_at) {
        $stmt=$conn->prepare("INSERT INTO orders
          (order_code,buyer_name,buyer_email,total_amount,payment_status,fulfillment_status,payment_ref,notes,created_at)
          VALUES (?,?,?,?,?,?,?,?,?)");
        //  s          s          s          d            s               s                 s           s     s
        $stmt->bind_param("sssdsssss",
          $order_code,$buyer_name,$buyer_email,$total_amount,$payment_status,$fulfillment_status,$payment_ref,$notes,$created_at);
      } else {
        $stmt=$conn->prepare("INSERT INTO orders
          (order_code,buyer_name,buyer_email,total_amount,payment_status,fulfillment_status,payment_ref,notes)
          VALUES (?,?,?,?,?,?,?,?)");
        //  s          s          s          d            s               s                 s           s
        $stmt->bind_param("sssdssss",
          $order_code,$buyer_name,$buyer_email,$total_amount,$payment_status,$fulfillment_status,$payment_ref,$notes);
      }
      if(!$stmt->execute()) throw new Exception($stmt->error);
      $order_id = $conn->insert_id;
      $prev_status = null;
      $stmt->close();
    } else { // UPDATE
      $esc = $conn->real_escape_string($order_code);
      $res = $conn->query("SELECT id, payment_status FROM orders WHERE order_code='{$esc}' LIMIT 1");
      if(!$res || !$res->num_rows) throw new Exception('Order not found');
      $row=$res->fetch_assoc();
      $order_id=(int)$row['id'];
      $prev_status=$row['payment_status'];

      if ($created_at) {
        $stmt=$conn->prepare("UPDATE orders
            SET buyer_name=?, buyer_email=?, total_amount=?, payment_status=?, fulfillment_status=?, payment_ref=?, notes=?, created_at=?
            WHERE id=?");
        //   s            s            d            s              s                 s           s      s           i
        $stmt->bind_param("ssdsssssi",
          $buyer_name,$buyer_email,$total_amount,$payment_status,$fulfillment_status,$payment_ref,$notes,$created_at,$order_id);
      } else {
        $stmt=$conn->prepare("UPDATE orders
            SET buyer_name=?, buyer_email=?, total_amount=?, payment_status=?, fulfillment_status=?, payment_ref=?, notes=?
            WHERE id=?");
        //   s            s            d            s              s                 s           s       i
        $stmt->bind_param("ssdssssi",
          $buyer_name,$buyer_email,$total_amount,$payment_status,$fulfillment_status,$payment_ref,$notes,$order_id);
      }
      if(!$stmt->execute()) throw new Exception($stmt->error);
      $stmt->close();

      if(!$conn->query("DELETE FROM order_items WHERE order_id={$order_id}")) throw new Exception($conn->error);
    }

    // Insert items
    $itStmt   = $conn->prepare("INSERT INTO order_items (order_id, product_type, product_id, qty, unit_price) VALUES (?,?,?,?,?)");
    $stockUpd = $conn->prepare("UPDATE books SET stock = GREATEST(0, stock - ?) WHERE id=?");

    foreach ($items as $it) {
      $pid=(int)$it['bookId']; $qty=(int)$it['qty']; $price=(float)$it['unitPrice']; $type='book';
      // types: i s i i d
      $itStmt->bind_param("isiid", $order_id, $type, $pid, $qty, $price);
      if(!$itStmt->execute()) throw new Exception($itStmt->error);
    }

    // Decrement stock only when paid (or when updating from non-paid → paid)
    $shouldDec = ($action==='create' && $payment_status==='Paid') ||
                 ($action==='update' && $prev_status!=='Paid' && $payment_status==='Paid');
    if ($shouldDec) {
      foreach ($items as $it) {
        $qty=(int)$it['qty']; $pid=(int)$it['bookId'];
        $stockUpd->bind_param("ii", $qty, $pid);
        if(!$stockUpd->execute()) throw new Exception($stockUpd->error);
      }
      $conn->query("UPDATE books SET status='Out of Stock' WHERE stock<=0");
    }

    $itStmt->close(); $stockUpd->close();
    $conn->commit();

    echo json_encode(['ok'=>true,'order_id'=>$order_id,'order_code'=>$order_code,'total'=>$total_amount]);
    exit;
  } catch (Throwable $e) {
    $conn->rollback();
    bad('Failed to save order: '.$e->getMessage(), 500);
  }
}

/* ---------------- DELETE ---------------- */
if ($action === 'delete') {
  $input=json_decode(file_get_contents('php://input'),true);
  $code=trim($input['orderId'] ?? '');
  if($code==='') bad('Missing orderId');
  $stmt=$conn->prepare("DELETE FROM orders WHERE order_code=? LIMIT 1");
  $stmt->bind_param("s",$code);
  if(!$stmt->execute()) bad($stmt->error,500);
  echo json_encode(['ok'=>true]);
  exit;
}

bad('Unknown action');
