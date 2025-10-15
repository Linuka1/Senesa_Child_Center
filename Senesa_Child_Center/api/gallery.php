<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require_once __DIR__ . '/../db.php';   // must define $conn = new mysqli(...)

if (!($conn instanceof mysqli)) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'DB connection not found']);
  exit;
}
$conn->set_charset('utf8mb4');

/* =========================
   Portable upload paths  ✅
   ========================= */
$PROJECT_ROOT  = dirname(__DIR__); // e.g. C:/xampp/htdocs/Senesa_Child_Center
$UPLOAD_DIR_ABS = $PROJECT_ROOT . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'gallery' . DIRECTORY_SEPARATOR; // filesystem path
$UPLOAD_DIR_REL = 'uploads/gallery/';  // path saved in DB (web path – no "../")

// Upload helper (server decides final filename and writes it back to $src/$thumb)
function handle_upload_and_paths(&$src, &$thumb, $absDir, $relDir) {
  if (!isset($_FILES['uploadImage']) || $_FILES['uploadImage']['error'] !== UPLOAD_ERR_OK) return;

  if (!is_dir($absDir)) { @mkdir($absDir, 0775, true); }

  // Try to honor a hinted basename; otherwise use original
  $hint = basename(str_replace('\\','/', $src ?: $thumb ?: ''));
  $hint = preg_replace('/[^A-Za-z0-9._-]/', '-', strtolower($hint));

  $orig = $_FILES['uploadImage']['name'] ?? 'image.jpg';
  $orig = preg_replace('/[^A-Za-z0-9._-]/', '-', strtolower($orig));

  // Unique filename (microtime) to avoid collisions
  $uniq = str_replace('.', '', microtime(true));
  $base = $hint ?: $orig;
  $destName = $uniq . '-' . $base;

  $abs = $absDir . $destName;
  if (move_uploaded_file($_FILES['uploadImage']['tmp_name'], $abs)) {
    $rel = $relDir . $destName;

    // Always use the real saved path for both
    $src   = $rel;
    $thumb = $rel;
  }
}

$action = $_REQUEST['action'] ?? 'list';

/* Public endpoint: only Active items (for normal users page) */
if ($action === 'public') {
  $sql = "SELECT id, src, thumb, caption, status, created_at, eventName
          FROM gallery
          WHERE status='Active'
          ORDER BY id DESC";
  $res = $conn->query($sql);
  $rows = [];
  while ($r = $res->fetch_assoc()) { $rows[] = $r; }
  echo json_encode(["ok"=>true, "data"=>$rows]);
  exit;
}

/* Admin: list all */
if ($action === 'list') {
  $sql = "SELECT * FROM gallery ORDER BY id DESC";
  $res = $conn->query($sql);
  $rows = [];
  while ($r = $res->fetch_assoc()) { $rows[] = $r; }
  echo json_encode(["ok"=>true, "data"=>$rows]);
  exit;
}

/* Add */
if ($action === 'add') {
  $src       = $_POST['src'] ?? '';
  $thumb     = $_POST['thumb'] ?? '';
  $caption   = $_POST['caption'] ?? '';
  $status    = $_POST['status'] ?? 'Active';
  $eventName = trim($_POST['eventName'] ?? '');

  handle_upload_and_paths($src, $thumb, $UPLOAD_DIR_ABS, $UPLOAD_DIR_REL);

  $stmt = $conn->prepare("INSERT INTO gallery (src, thumb, caption, status, eventName) VALUES (?,?,?,?,?)");
  $stmt->bind_param("sssss", $src, $thumb, $caption, $status, $eventName);

  echo $stmt->execute()
    ? json_encode(["ok"=>true, "id"=>$conn->insert_id])
    : json_encode(["ok"=>false, "error"=>"Query execution failed: ".$stmt->error]);
  exit;
}


/* Edit */
if ($action === 'edit') {
  $id        = (int)($_POST['id'] ?? 0);
  $src       = $_POST['src'] ?? '';
  $thumb     = $_POST['thumb'] ?? '';
  $caption   = $_POST['caption'] ?? '';
  $status    = $_POST['status'] ?? 'Active';
  $eventName = trim($_POST['eventName'] ?? '');

  handle_upload_and_paths($src, $thumb, $UPLOAD_DIR_ABS, $UPLOAD_DIR_REL);

  $stmt = $conn->prepare("UPDATE gallery SET src=?, thumb=?, caption=?, status=?, eventName=? WHERE id=?");
  $stmt->bind_param("sssssi", $src, $thumb, $caption, $status, $eventName, $id);

  echo $stmt->execute()
    ? json_encode(["ok"=>true, "updated"=>true])
    : json_encode(["ok"=>false, "error"=>"Query execution failed: ".$stmt->error]);
  exit;
}


/* Delete */
if ($action === 'delete') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id <= 0) { echo json_encode(["ok"=>false,"error"=>"Invalid ID"]); exit; }

  $stmt = $conn->prepare("DELETE FROM gallery WHERE id=?");
  $stmt->bind_param("i", $id);
  echo $stmt->execute()
    ? json_encode(["ok"=>true, "deleted"=>true])
    : json_encode(["ok"=>false, "error"=>"Failed to delete"]);
  exit;
}

echo json_encode(["ok"=>false, "error"=>"Invalid action"]);
