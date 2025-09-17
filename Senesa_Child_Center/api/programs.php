<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require_once __DIR__ . '/../db.php'; // defines $conn (mysqli)

function ok($data){ echo json_encode(['ok'=>true,'data'=>$data]); exit; }
function bad($msg, $code=400){ http_response_code($code); echo json_encode(['ok'=>false,'error'=>$msg]); exit; }

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

/* ---------- helpers ---------- */
function ensure_upload_dir($dir){
  if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
}
function is_image_upload($file){
  if (empty($file['name'])) return false;
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mime  = finfo_file($finfo, $file['tmp_name']);
  finfo_close($finfo);
  return in_array($mime, ['image/jpeg','image/png','image/gif','image/webp']);
}
/* upload file and return RELATIVE path like "uploads/programs/xxxx.jpg" */
function handle_image_upload($field = 'file'){
  if (empty($_FILES[$field]['name'])) return null;
  if (!is_image_upload($_FILES[$field])) bad("Invalid image file", 400);

  $clean = preg_replace('/[^a-zA-Z0-9._-]/','', $_FILES[$field]['name']);
  $fn    = time() . '_' . $clean;
  $relDir = "uploads/programs";
  $absDir = __DIR__ . "/../" . $relDir;
  ensure_upload_dir($absDir);
  $dest = $absDir . "/" . $fn;

  if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
    bad("Upload failed", 500);
  }
  return $relDir . "/" . $fn; // RELATIVE path stored in DB
}

/* ---------- actions ---------- */
if ($action === 'count') {
  $sql = "SELECT COUNT(*) AS c FROM programs WHERE status <> 'Inactive'";
  $res = $conn->query($sql);
  if (!$res) bad("DB error: ".$conn->error, 500);
  $row = $res->fetch_assoc() ?: ['c'=>0];
  ok(['count' => (int)$row['c']]);
}

if ($action === 'list') {
  $sql = "SELECT id, image, name, age_group AS age, summary, status, DATE(created_at) AS createdAt
          FROM programs ORDER BY id ASC";
  $res = $conn->query($sql);
  if (!$res) bad("DB error: ".$conn->error, 500);
  $rows = [];
  while($r = $res->fetch_assoc()) { $rows[] = $r; }
  ok($rows);
}

if ($action === 'create' || $action === 'update') {
  // accept form-data or raw JSON
  $payload = $_POST;
  if (empty($_POST) && ($raw = file_get_contents('php://input'))) {
    $payload = json_decode($raw, true) ?: [];
  }

  $id      = isset($payload['id']) ? (int)$payload['id'] : null;
  $name    = trim($payload['name'] ?? '');
  $age     = trim($payload['age'] ?? '');
  $summary = trim($payload['summary'] ?? '');
  $status = in_array(($payload['status'] ?? 'Active'), ['Active','Hidden'])
          ? $payload['status'] : 'Active';
  $image   = trim($payload['image'] ?? ''); // optional URL/path

  if ($name === '') bad("Program name required", 422);

  // If a new file was uploaded, it wins.
  $uploadedRelPath = handle_image_upload('file');
  if ($uploadedRelPath) {
    $image = $uploadedRelPath;
  }

  if ($action === 'create') {
    $stmt = $conn->prepare("INSERT INTO programs (name, summary, age_group, image, status) VALUES (?,?,?,?,?)");
    if (!$stmt) bad("DB error: ".$conn->error, 500);
    $stmt->bind_param("sssss", $name, $summary, $age, $image, $status);
    if (!$stmt->execute()) bad("DB error: ".$stmt->error, 500);
    ok(['id'=>$conn->insert_id]);
  } else { // update
    if (!$id) bad("Missing id", 422);

    // If image is still empty (no URL and no uploaded file), keep the existing one.
    if ($image === '') {
      $get = $conn->prepare("SELECT image FROM programs WHERE id=?");
      if (!$get) bad("DB error: ".$conn->error, 500);
      $get->bind_param("i", $id);
      if (!$get->execute()) bad("DB error: ".$get->error, 500);
      $cur = $get->get_result()->fetch_assoc();
      $image = $cur ? ($cur['image'] ?? '') : '';
    }

    $stmt = $conn->prepare("UPDATE programs SET name=?, summary=?, age_group=?, image=?, status=? WHERE id=?");
    if (!$stmt) bad("DB error: ".$conn->error, 500);
    $stmt->bind_param("sssssi", $name, $summary, $age, $image, $status, $id);
    if (!$stmt->execute()) bad("DB error: ".$stmt->error, 500);
    ok(['updated'=> true]);
  }
}

if ($action === 'toggle') {
  $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  if (!$id) bad("Missing id", 422);

  // read current status
  $get = $conn->prepare("SELECT status FROM programs WHERE id=?");
  if (!$get) bad("DB error: ".$conn->error, 500);
  $get->bind_param("i", $id);
  if (!$get->execute()) bad("DB error: ".$get->error, 500);
  $cur = $get->get_result()->fetch_assoc();
  if (!$cur) bad("Program not found", 404);

  $new = ($cur['status'] === 'Active') ? 'Hidden' : 'Active';

  $upd = $conn->prepare("UPDATE programs SET status=? WHERE id=?");
  if (!$upd) bad("DB error: ".$conn->error, 500);
  $upd->bind_param("si", $new, $id);
  if (!$upd->execute()) bad("DB error: ".$upd->error, 500);

  ok(['status'=>$new]);
}

if ($action === 'delete') {
  $id = isset($_POST['id']) ? (int)$_POST['id'] : (int)($_GET['id'] ?? 0);
  if (!$id) bad("Missing id", 422);

  $stmt = $conn->prepare("DELETE FROM programs WHERE id=?");
  if (!$stmt) bad("DB error: ".$conn->error, 500);
  $stmt->bind_param("i", $id);
  if (!$stmt->execute()) bad("DB error: ".$stmt->error, 500);
  ok(['deleted'=>true]);
}

bad("Unknown action", 404);
