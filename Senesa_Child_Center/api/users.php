<?php
// /api/users.php  (root-level api folder)
require __DIR__ . '/../Admin/auth-admin.php';
require __DIR__ . '/../db.php';

function json($arr, $code=200){
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($arr);
  exit;
}
function ok($msg='OK', $extra=[]){ json(array_merge(['ok'=>true,'msg'=>$msg], $extra)); }
function bad($msg='Bad request', $code=400){ json(['ok'=>false,'msg'=>$msg], $code); }

$me     = (int)($_SESSION['user_id'] ?? 0);
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

switch ($action) {

  // ---------- LIST (GET) ----------
  case 'list': {
    header('Content-Type: application/json; charset=utf-8');
    $q = trim($_GET['q'] ?? '');
    if ($q !== '') {
      $sql = "SELECT id,name,email,role,status FROM users
              WHERE name LIKE CONCAT('%',?,'%')
                 OR email LIKE CONCAT('%',?,'%')
                 OR role = ?
              ORDER BY id DESC";
      $s = $conn->prepare($sql);
      $s->bind_param('sss', $q, $q, $q);
      $s->execute();
      $rows = $s->get_result()->fetch_all(MYSQLI_ASSOC);
      $s->close();
    } else {
      $rows = $conn->query("SELECT id,name,email,role,status FROM users ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
    }
    foreach ($rows as &$r) { $r['can_modify'] = ($me !== (int)$r['id']); }
    echo json_encode(['ok'=>true,'data'=>$rows]); exit;
  }

  // ---------- SAVE (POST: add or edit) ----------
  case 'save': {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') bad();
    $id    = (int)($_POST['id'] ?? 0);
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role  = trim($_POST['role'] ?? 'Parent');
    $pass  = trim($_POST['password'] ?? '');

    if ($name==='' || $email==='' || !filter_var($email, FILTER_VALIDATE_EMAIL)) bad('Name and valid Email required');
    if (!in_array($role, ['Parent','Admin','Super Admin'], true)) $role = 'Parent';

    // unique email
    if ($id>0) { $s=$conn->prepare("SELECT COUNT(*) FROM users WHERE email=? AND id<>?"); $s->bind_param('si',$email,$id); }
    else       { $s=$conn->prepare("SELECT COUNT(*) FROM users WHERE email=?");           $s->bind_param('s',$email); }
    $s->execute(); $exists=(int)$s->get_result()->fetch_row()[0]; $s->close();
    if ($exists>0) bad('Email already exists');

    if ($id>0) {
      if ($pass!=='') {
        $hash=password_hash($pass,PASSWORD_DEFAULT);
        $u=$conn->prepare("UPDATE users SET name=?, email=?, role=?, password=? WHERE id=?");
        $u->bind_param('ssssi',$name,$email,$role,$hash,$id);
      } else {
        $u=$conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
        $u->bind_param('sssi',$name,$email,$role,$id);
      }
      $ok=$u->execute(); $u->close();
      $ok ? ok('Updated') : bad('Update failed');
    } else {
      // create new, default Active
      $hash = $pass!=='' ? password_hash($pass,PASSWORD_DEFAULT) : password_hash(bin2hex(random_bytes(4)), PASSWORD_DEFAULT);
      $i=$conn->prepare("INSERT INTO users(name,email,password,role,status) VALUES(?,?,?,?, 'Active')");
      $i->bind_param('ssss',$name,$email,$hash,$role);
      $ok=$i->execute(); $i->close();
      $ok ? ok('Created') : bad('Create failed');
    }
  }

  // ---------- SET STATUS (POST) ----------
    case 'set_status': {
    header('Content-Type: application/json; charset=utf-8');

    // Read both JSON and x-www-form-urlencoded
    $raw = file_get_contents('php://input');
    $in  = json_decode($raw, true);
    if (!is_array($in)) { $in = []; }

    $id     = (int)($_POST['id']     ?? $in['id']     ?? 0);
    $status = trim($_POST['status']  ?? $in['status'] ?? '');

    // normalize common synonyms
    if ($status === 'unblock' || $status === 'activate') $status = 'Active';
    if ($status === 'block') $status = 'Blocked';

    if ($id <= 0 || !in_array($status, ['Active','Blocked'], true)) {
        http_response_code(400);
        echo json_encode(['ok'=>false, 'error'=>'Bad request: require id and status Active|Blocked']); exit;
    }

    // protect self from being blocked/deleted if you want
    $me = $_SESSION['user_id'] ?? 0;
    if ($me && $me === $id) {
        http_response_code(403);
        echo json_encode(['ok'=>false, 'error'=>"You can't change your own status."]); exit;
    }

    $s = $conn->prepare("UPDATE users SET status=? WHERE id=?");
    $s->bind_param('si', $status, $id);
    $ok = $s->execute();
    $s->close();

    if (!$ok) {
        http_response_code(500);
        echo json_encode(['ok'=>false, 'error'=>'Failed to update status']); exit;
    }
    echo json_encode(['ok'=>true]); exit;
    }


  // ---------- DELETE (POST) ----------
  case 'delete': {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') bad();
    $id = (int)($_POST['id'] ?? 0);
    if ($id<=0 || $id===$me) bad('Not allowed');
    $d=$conn->prepare("DELETE FROM users WHERE id=?");
    $d->bind_param('i',$id);
    $ok=$d->execute(); $d->close();
    $ok ? ok() : bad('Delete failed');
  }

  // ---------- EXPORT CSV (GET) ----------
  case 'export': {
    $q = trim($_GET['q'] ?? '');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="users.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID','Name','Email','Role','Status','Created']);
    if ($q!=='') {
      $sql="SELECT id,name,email,role,status,created_at FROM users
            WHERE name LIKE CONCAT('%',?,'%') OR email LIKE CONCAT('%',?,'%') OR role=? ORDER BY id DESC";
      $s=$conn->prepare($sql); $s->bind_param('sss',$q,$q,$q); $s->execute(); $rs=$s->get_result();
    } else {
      $rs=$conn->query("SELECT id,name,email,role,status,created_at FROM users ORDER BY id DESC");
    }
    while($row=$rs->fetch_assoc()){
      fputcsv($out, [$row['id'],$row['name'],$row['email'],$row['role'],$row['status'],$row['created_at']]);
    }
    fclose($out); exit;
  }

  default: bad('Unknown action');
}
