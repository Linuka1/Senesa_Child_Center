<?php
// api/enrollment.php
// One-file API for create/list/update_status using MySQLi ($conn).
// Place this inside /api and make sure ../db.php exists and sets $conn (mysqli).

header('Content-Type: application/json; charset=utf-8');

// OPTIONAL CORS (uncomment if you POST from a different origin)
// header('Access-Control-Allow-Origin: https://your-site.com');
// header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type, Authorization');
// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require __DIR__ . '/../db.php'; // provides $conn (MySQLi)

// OPTIONAL: protect admin-only actions (list/update_status)
// If you have an admin auth gate, uncomment:
// if (($_GET['action'] ?? $_POST['action'] ?? '') !== 'create') {
//   require __DIR__ . '/../auth-admin.php';
// }

function json_ok($data = []) { echo json_encode(['ok' => true] + $data); exit; }
function json_err($msg, $code = 400) { http_response_code($code); echo json_encode(['ok' => false, 'error' => $msg]); exit; }

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Parse JSON body if present
$raw = file_get_contents('php://input');
$asJson = null;
if ($raw && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
  $asJson = json_decode($raw, true);
}

// ---------- ACTION: CREATE (public form) ----------
if ($action === 'create') {
  if ($method !== 'POST') json_err('Method not allowed', 405);
  $p = $asJson ?: $_POST;
  if (!$p) json_err('Invalid or empty payload');

  // Map nursery-plan to DB enum
  $nur = $p['nursery-plan'] ?? '';
  $nursery_choice =
    ($nur === 'only-nursery') ? 'Only Nursery' :
    (($nur === 'nursery-and-daycare' || $nur === 'only-daycare') ? 'Nursery + Daycare' : null);

  // For daycare choices we expect a selected plan
  $selected_plan = ($nursery_choice === 'Nursery + Daycare') ? ($p['plan'] ?? '') : 'Only Nursery';

  // Extras array -> CSV
  $extras = '';
  if (isset($p['extras'])) {
    if (is_array($p['extras']))       $extras = implode(', ', $p['extras']);
    else if (is_string($p['extras'])) $extras = $p['extras'];
  }

  // Type fixes
  $dob         = $p['dob']        ?? null;
  $age         = $p['age']        ?? ($p['ageComputedYears'] ?? null);
  $start_date  = $p['startDate']  ?? null;
  $daysPerWeek = $p['daysPerWeek'] ?? null;
  $daysPerWeek = ($daysPerWeek === '' ? null : $daysPerWeek);

  // Booleans
  $consent_medical = !empty($p['consentMedical']) ? 1 : 0;
  $consent_policy  = !empty($p['consentPolicy'])  ? 1 : 0;
  $consent_photo   = !empty($p['consentPhoto'])   ? 1 : 0;

  // NOTE: signature fields removed from SQL and bind params
  $sql = "INSERT INTO enrollments (
    parent_first, parent_last, parent_email, parent_phone, address, city,
    child_first, child_last, dob, age, gender, allergies, special_needs,
    nursery_choice, plan, start_date, days_per_week, schedule, extras,
    emergency_name, emergency_phone, physician, physician_phone,
    authorized_pickup, notes, consent_medical, consent_policy, consent_photo,
    status
  ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, 'Pending')";

  $stmt = $conn->prepare($sql);
  if (!$stmt) json_err('SQL prepare failed: ' . $conn->error, 500);

  // 28 params total: 24 strings + 1 int (daysPerWeek) + 3 ints (consents)
  // types: 16 's' + 1 'i' + 8 's' + 3 'i'  => 'ssssssssssssssssissssssssiii'
  $stmt->bind_param(
    'ssssssssssssssssissssssssiii',
    $p['parentFirst'], $p['parentLast'], $p['parentEmail'], $p['parentPhone'], $p['address'], $p['city'],
    $p['childFirst'], $p['childLast'], $dob, $age, $p['gender'], $p['allergies'], $p['specialNeeds'],
    $nursery_choice, $selected_plan, $start_date, $daysPerWeek, $p['schedule'], $extras,
    $p['emergencyName'], $p['emergencyPhone'], $p['physician'], $p['physicianPhone'],
    $p['authorizedPickup'], $p['notes'],
    $consent_medical, $consent_policy, $consent_photo
  );

  if (!$stmt->execute()) json_err('Insert failed: ' . $stmt->error, 500);
  json_ok(['id' => $stmt->insert_id]);
}


// ---------- ACTION: LIST (admin table) ----------
if ($action === 'list') {
  if ($method !== 'GET') json_err('Method not allowed', 405);

  $q  = isset($_GET['q']) ? trim($_GET['q']) : '';
  $st = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;

  $base = "SELECT * FROM enrollments";
  $where = [];
  $types = '';
  $args  = [];

  if ($st) { $where[] = "status = ?"; $types .= 's'; $args[] = $st; }
  if ($q !== '') {
    $where[] = "(child_first LIKE ? OR child_last LIKE ? OR parent_first LIKE ? OR parent_last LIKE ? OR plan LIKE ? OR nursery_choice LIKE ?)";
    $types .= 'ssssss';
    $like = "%$q%";
    array_push($args, $like,$like,$like,$like,$like,$like);
  }
  $sql = $base . ( $where ? " WHERE ".implode(" AND ", $where) : "" ) . " ORDER BY submitted_at DESC, id DESC";

  $stmt = $conn->prepare($sql);
  if (!$stmt) json_err('SQL prepare failed: ' . $conn->error, 500);
  if ($types !== '') $stmt->bind_param($types, ...$args);
  $stmt->execute();
  $res = $stmt->get_result();
  $rows = $res->fetch_all(MYSQLI_ASSOC);

  json_ok(['rows' => $rows]);
}

// ---------- ACTION: UPDATE STATUS (admin) ----------
if ($action === 'update_status') {
  if ($method !== 'POST') json_err('Method not allowed', 405);

  // Accept either form-encoded or JSON
  $id     = isset($_POST['id']) ? (int)$_POST['id'] : (int)($asJson['id'] ?? 0);
  $status = $_POST['status']    ?? ($asJson['status'] ?? '');

  if (!$id || !in_array($status, ['Pending','Approved','Rejected'], true)) {
    json_err('Bad input');
  }

  $stmt = $conn->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
  if (!$stmt) json_err('SQL prepare failed: ' . $conn->error, 500);
  $stmt->bind_param('si', $status, $id);
  if (!$stmt->execute()) json_err('Update failed: ' . $stmt->error, 500);

  json_ok();
}

// ---------- Unknown ----------
json_err('Unknown action', 400);
