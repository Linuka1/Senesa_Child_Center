<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../db.php'; // mysqli $conn
$conn->set_charset("utf8mb4");

function ok($d){ echo json_encode(['ok'=>true,'data'=>$d]); exit; }
function okmsg($d=[]){ echo json_encode(['ok'=>true]+$d); exit; }
function bad($m,$c=400){ http_response_code($c); echo json_encode(['ok'=>false,'error'=>$m]); exit; }

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

/* ---------- helpers ---------- */

function ensure_dir($dir){ if(!is_dir($dir)) @mkdir($dir,0777,true); }
function is_image($tmp){
  $f = finfo_open(FILEINFO_MIME_TYPE);
  $m = finfo_file($f,$tmp);
  finfo_close($f);
  return in_array($m,['image/jpeg','image/png','image/gif','image/webp']);
}
function handle_upload($field='file'){
  if (empty($_FILES[$field]['name'])) return null;
  if (!is_image($_FILES[$field]['tmp_name'])) bad("Invalid image",422);
  $clean = preg_replace('/[^a-zA-Z0-9._-]/','', $_FILES[$field]['name']);
  $name  = time().'_'.$clean;
  $rel   = 'uploads/teachers';
  $abs   = __DIR__ . '/../' . $rel;
  ensure_dir($abs);
  if (!move_uploaded_file($_FILES[$field]['tmp_name'], $abs.'/'.$name)) bad("Upload failed",500);
  return $rel.'/'.$name; // store this in DB
}

/* ---------- actions ---------- */
if ($action === 'list') {
  $sql = "SELECT id, photo, first_name, last_name, role, qualifications, experience_years,
                 phone, email, whatsapp, availability, status, created_at
          FROM teachers ORDER BY id DESC";
  $res = $conn->query($sql);
  if (!$res) bad("DB error: ".$conn->error,500);
  $rows = [];
  while ($r = $res->fetch_assoc()) $rows[] = $r;
  ok($rows);
}

if ($action === 'create' || $action === 'update') {
  // read from POST form-data or JSON
  $p = $_POST;
  if (empty($p) && ($raw=file_get_contents('php://input'))) $p = json_decode($raw,true) ?: [];

  $id    = isset($p['id']) ? (int)$p['id'] : null;
  $first = trim($p['first_name'] ?? '');
  $last  = trim($p['last_name'] ?? '');
  if ($first==='' || $last==='') bad("First and Last Name are required",422);

  $role  = trim($p['role'] ?? '');
  $qual  = trim($p['qualifications'] ?? '');
  $exp   = (int)($p['experience'] ?? $p['experience_years'] ?? 0);
  $phone = trim($p['phone'] ?? '');
  $email = trim($p['email'] ?? '');
  $wa    = trim($p['whatsapp'] ?? '');
  $avail = trim($p['availability'] ?? '');
  $status= trim($p['status'] ?? 'Active');
  $photo = trim($p['photo'] ?? ''); // may be a URL/path

  // uploaded file wins
  $uploaded = handle_upload('file');
  if ($uploaded) $photo = $uploaded;

  if ($action === 'create') {
    $stmt = $conn->prepare("INSERT INTO teachers
      (first_name,last_name,role,qualifications,experience_years,phone,email,whatsapp,availability,status,photo)
      VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    if(!$stmt) bad("DB error: ".$conn->error,500);
    $stmt->bind_param(
      "ssssissssss",   // ✅ s s s s i s s s s s s
      $first,$last,$role,$qual,$exp,$phone,$email,$wa,$avail,$status,$photo
    );
    if(!$stmt->execute()) bad("DB error: ".$stmt->error,500);
    okmsg(['id'=>$conn->insert_id]);
  } else { // update
    if (!$id) bad("Missing id",422);

    // keep old photo if still blank
    if ($photo==='') {
      $get = $conn->prepare("SELECT photo FROM teachers WHERE id=?");
      $get->bind_param("i",$id);
      $get->execute();
      $cur = $get->get_result()->fetch_assoc();
      $photo = $cur['photo'] ?? '';
    }

    $stmt = $conn->prepare("UPDATE teachers SET
      first_name=?, last_name=?, role=?, qualifications=?, experience_years=?,
      phone=?, email=?, whatsapp=?, availability=?, status=?, photo=? WHERE id=?");
    if(!$stmt) bad("DB error: ".$conn->error,500);
    $stmt->bind_param(
      "ssssissssssi",  // ✅ s s s s i s s s s s s i
      $first,$last,$role,$qual,$exp,$phone,$email,$wa,$avail,$status,$photo,$id
    );
    if(!$stmt->execute()) bad("DB error: ".$stmt->error,500);
    okmsg(['updated'=>true]);
  }
}

if ($action === 'toggle') {
  $id = (int)($_POST['id'] ?? 0);
  if (!$id) bad("Missing id",422);
  $conn->query("UPDATE teachers SET status = IF(status='Active','Inactive','Active') WHERE id=".$id);
  if ($conn->affected_rows<0) bad("DB error: ".$conn->error,500);
  okmsg(['toggled'=>true]);
}

if ($action === 'delete') {
  $id = (int)($_POST['id'] ?? 0);
  if (!$id) bad("Missing id",422);
  $stmt = $conn->prepare("DELETE FROM teachers WHERE id=?");
  $stmt->bind_param("i",$id);
  if(!$stmt->execute()) bad("DB error: ".$stmt->error,500);
  okmsg(['deleted'=>true]);
}

bad("Unknown action",404);
