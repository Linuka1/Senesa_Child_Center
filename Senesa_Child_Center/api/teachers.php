<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require_once __DIR__ . '/../db.php'; // defines $conn (mysqli)

function ok($data){ 
  echo json_encode(['ok'=>true,'data'=>$data]); 
  exit; 
}

function bad($msg, $code=400){ 
  http_response_code($code); 
  echo json_encode(['ok'=>false,'error'=>$msg]); 
  exit; 
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';  // Default to 'list' if no action specified

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

function handle_image_upload($field = 'file'){
  if (empty($_FILES[$field]['name'])) return null;
  if (!is_image_upload($_FILES[$field])) bad("Invalid image file", 400);

  $clean = preg_replace('/[^a-zA-Z0-9._-]/','', $_FILES[$field]['name']);
  $fn    = time() . '_' . $clean;  // Unique filename with timestamp
  $relDir = "uploads/teachers";  // Directory for storing images
  $absDir = __DIR__ . "/../" . $relDir;  // Absolute path
  ensure_upload_dir($absDir);
  $dest = $absDir . "/" . $fn;

  if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
    bad("Upload failed", 500);
  }

  return $relDir . "/" . $fn; // Return the relative path to store in DB
}

/* ---------- actions ---------- */
if ($action === 'create' || $action === 'update') {
  $payload = $_POST;
  if (empty($_POST) && ($raw = file_get_contents('php://input'))) {
    $payload = json_decode($raw, true) ?: [];
  }

  $id      = isset($payload['id']) ? (int)$payload['id'] : null;
  $firstName = trim($payload['first_name'] ?? '');
  $lastName = trim($payload['last_name'] ?? '');
  $role = trim($payload['role'] ?? '');
  $qualifications = trim($payload['qualifications'] ?? '');
  $experience = (int)($payload['experience'] ?? 0);
  $phone = trim($payload['phone'] ?? '');
  $email = trim($payload['email'] ?? '');
  $whatsapp = trim($payload['whatsapp'] ?? '');
  $availability = trim($payload['availability'] ?? '');
  $status = isset($payload['status']) ? $payload['status'] : 'Active';
  $photo = trim($payload['photo'] ?? ''); // optional URL/path

  // Basic validation
  if ($firstName === '' || $lastName === '') bad("First and Last Name are required", 422);

  // If a new file was uploaded, it wins.
  $uploadedRelPath = handle_image_upload('file');
  if ($uploadedRelPath) {
    $photo = $uploadedRelPath;
  }

  if ($action === 'create') {
    $stmt = $conn->prepare("INSERT INTO teachers (first_name, last_name, role, qualifications, experience_years, phone, email, whatsapp, availability, status, photo) 
                            VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    if (!$stmt) bad("DB error: ".$conn->error, 500);
    $stmt->bind_param("ssssiisssss", $firstName, $lastName, $role, $qualifications, $experience, $phone, $email, $whatsapp, $availability, $status, $photo);
    if (!$stmt->execute()) bad("DB error: ".$stmt->error, 500);
    ok(['id'=>$conn->insert_id]);
  } else { // update
    $id = isset($payload['id']) ? (int)$payload['id'] : null;
    if (!$id) bad("Missing id", 422);

    // If image is still empty (no URL and no uploaded file), keep the existing one.
    if ($photo === '') {
      $get = $conn->prepare("SELECT photo FROM teachers WHERE id=?");
      if (!$get) bad("DB error: ".$conn->error, 500);
      $get->bind_param("i", $id);
      if (!$get->execute()) bad("DB error: ".$get->error, 500);
      $cur = $get->get_result()->fetch_assoc();
      $photo = $cur ? ($cur['photo'] ?? '') : '';
    }

    $stmt = $conn->prepare("UPDATE teachers SET first_name=?, last_name=?, role=?, qualifications=?, experience_years=?, phone=?, email=?, whatsapp=?, availability=?, status=?, photo=? WHERE id=?");
    if (!$stmt) bad("DB error: ".$conn->error, 500);
    $stmt->bind_param("ssssiisssssi", $firstName, $lastName, $role, $qualifications, $experience, $phone, $email, $whatsapp, $availability, $status, $photo, $id);
    if (!$stmt->execute()) bad("DB error: ".$stmt->error, 500);
    ok(['updated'=> true]);
  }
}

