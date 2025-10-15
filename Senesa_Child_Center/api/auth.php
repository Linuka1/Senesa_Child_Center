<?php
// /api/auth.php
session_start();
require __DIR__ . '/../db.php'; // uses your existing DB connection

function out($arr,$code=200){
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($arr); exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'me') {
  if (!isset($_SESSION['user_id'])) out(['ok'=>true,'loggedIn'=>false]);
  out([
    'ok'=>true,'loggedIn'=>true,
    'user'=>[
      'id'=>(int)$_SESSION['user_id'],
      'name'=>$_SESSION['name'] ?? '',
      'email'=>$_SESSION['email'] ?? '',
      'role'=>$_SESSION['role'] ?? 'Parent'
    ]
  ]);
}

if ($action === 'logout') {
  session_unset(); session_destroy();
  out(['ok'=>true]);
}

if ($action === 'login') {
  $email = trim($_POST['email'] ?? '');
  $password = (string)($_POST['password'] ?? '');
  if ($email==='' || $password==='') out(['ok'=>false,'error'=>'Email and password required'],400);

  $stmt = $conn->prepare("SELECT id,name,email,password,role,status FROM users WHERE email=? LIMIT 1");
  $stmt->bind_param('s',$email); $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc(); $stmt->close();

  if (!$row || !password_verify($password, $row['password'])) out(['ok'=>false,'error'=>'Invalid credentials'],401);
  if ($row['status'] !== 'Active') out(['ok'=>false,'error'=>'Account is blocked'],403);

  $_SESSION['user_id'] = (int)$row['id'];
  $_SESSION['name']    = $row['name'];
  $_SESSION['email']   = $row['email'];
  $_SESSION['role']    = $row['role'];

  out(['ok'=>true,'user'=>['id'=>(int)$row['id'],'name'=>$row['name'],'email'=>$row['email'],'role'=>$row['role']]]);
}

if ($action === 'register') {
  $name  = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass  = (string)($_POST['password'] ?? '');

  if ($name==='' || $email==='' || $pass==='') out(['ok'=>false,'error'=>'All fields are required'],400);
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) out(['ok'=>false,'error'=>'Invalid email'],400);

  // unique email
  $s = $conn->prepare("SELECT COUNT(*) FROM users WHERE email=?"); $s->bind_param('s',$email); $s->execute();
  $exists = (int)$s->get_result()->fetch_row()[0]; $s->close();
  if ($exists>0) out(['ok'=>false,'error'=>'Email already in use'],409);

  $hash = password_hash($pass, PASSWORD_DEFAULT);
  $role = 'Parent';      // normal users
  $status = 'Active';    // you can change to 'Active' or 'Blocked' by policy

  $i = $conn->prepare("INSERT INTO users(name,email,password,role,status) VALUES(?,?,?,?,?)");
  $i->bind_param('sssss',$name,$email,$hash,$role,$status);
  $ok = $i->execute();
  $newId = $ok ? $i->insert_id : 0;
  $i->close();

  if (!$ok) out(['ok'=>false,'error'=>'Registration failed'],500);

  // Auto-login after registration
  $_SESSION['user_id'] = (int)$newId;
  $_SESSION['name']    = $name;
  $_SESSION['email']   = $email;
  $_SESSION['role']    = $role;

  out(['ok'=>true,'user'=>['id'=>(int)$newId,'name'=>$name,'email'=>$email,'role'=>$role]]);
}

out(['ok'=>false,'error'=>'Unknown action'],400);
