<?php
// api/messages.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../db.php';  // your db.php with mysqli $conn

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

function json_out($a){ echo json_encode($a, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function read_json_body(){
  $raw = file_get_contents('php://input');
  if (!$raw) return [];
  $j = json_decode($raw, true);
  return is_array($j) ? $j : [];
}

if ($action === 'create' && $method === 'POST') {
  // Accept JSON or form
  $p = read_json_body();
  if (!$p) $p = $_POST;

  $name    = trim($p['name']    ?? '');
  $email   = trim($p['email']   ?? '');
  $phone   = trim($p['phone']   ?? '');
  $subject = trim($p['subject'] ?? '');
  $message = trim($p['message'] ?? '');

  if ($name === '' || $email === '' || $subject === '' || $message === '') {
    http_response_code(400);
    json_out(['ok'=>false, 'error'=>'Missing required fields']);
  }

  $stmt = $conn->prepare("INSERT INTO messages (name,email,phone,subject,message,status) VALUES (?,?,?,?,?,'Unread')");
  $stmt->bind_param('sssss', $name, $email, $phone, $subject, $message);
  if (!$stmt->execute()) { http_response_code(500); json_out(['ok'=>false,'error'=>$stmt->error]); }
  $id = $stmt->insert_id;
  $stmt->close();

  json_out(['ok'=>true, 'id'=>$id]);
}

if ($action === 'list') {
  $q      = trim($_GET['q'] ?? '');
  $status = trim($_GET['status'] ?? '');
  $where  = "WHERE 1=1";
  $args   = [];
  $types  = '';

  if ($q !== '') {
    $like = "%$q%";
    $where .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ?)";
    $types .= 'sss';
    $args[] = &$like; $args[] = &$like; $args[] = &$like;
  }
  if ($status !== '') {
    $where .= " AND status = ?";
    $types .= 's';
    $args[] = &$status;
  }

  $sql = "SELECT id,name,email,phone,subject,message,status,DATE_FORMAT(received_at,'%Y-%m-%d %H:%i') AS received_at
          FROM messages $where
          ORDER BY received_at DESC, id DESC";
  if ($types) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$args);
    $stmt->execute();
    $res = $stmt->get_result();
  } else {
    $res = $conn->query($sql);
  }

  $rows = [];
  while ($r = $res->fetch_assoc()) $rows[] = $r;

  json_out(['ok'=>true, 'rows'=>$rows]);
}

if ($action === 'toggle' && $method === 'POST') {
  $id = intval($_POST['id'] ?? 0);
  if ($id <= 0) { http_response_code(400); json_out(['ok'=>false,'error'=>'Invalid id']); }

  // flip Unread <-> Read
  $conn->query("UPDATE messages SET status = IF(status='Unread','Read','Unread') WHERE id=$id");
  if ($conn->affected_rows < 0) { http_response_code(500); json_out(['ok'=>false,'error'=>$conn->error]); }
  json_out(['ok'=>true]);
}

if ($action === 'delete' && $method === 'POST') {
  $id = intval($_POST['id'] ?? 0);
  if ($id <= 0) { http_response_code(400); json_out(['ok'=>false,'error'=>'Invalid id']); }
  $conn->query("DELETE FROM messages WHERE id=$id");
  if ($conn->affected_rows < 1) { http_response_code(404); json_out(['ok'=>false,'error'=>'Not found']); }
  json_out(['ok'=>true]);
}

http_response_code(404);
json_out(['ok'=>false, 'error'=>'Unknown action']);
