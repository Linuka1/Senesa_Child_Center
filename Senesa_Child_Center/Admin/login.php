<?php
// login.php
session_start();
require __DIR__ . '/../db.php'; // make sure db.php creates $conn = new mysqli(...)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: admin-login.html');
  exit;
}

$email         = trim($_POST['email'] ?? '');
$passwordPlain = $_POST['password'] ?? '';
$intendedRole  = trim($_POST['intended_role'] ?? ''); // "Admin" from hidden field

if ($email === '' || $passwordPlain === '') {
  fail('Please enter email and password.');
}

$stmt = $conn->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user)               fail('Invalid credentials.');
if ($user['status'] !== 'Active') fail('Your account is not active.');
if (!password_verify($passwordPlain, $user['password'])) fail('Invalid credentials.');

// If this page is for admins, block Parents (except Super Admin)
if ($intendedRole === 'Admin' && !in_array($user['role'], ['Admin','Super Admin'])) {
  fail('You do not have permission to access the admin area.');
}

// OK
$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['name']    = $user['name'];
$_SESSION['email']   = $user['email'];
$_SESSION['role']    = $user['role'];

header('Location: dashboard.php'); // your admin dashboard (rename from .html to .php with guard)
exit;

function fail($msg){
  echo "<script>alert(".json_encode($msg).");history.back();</script>";
  exit;
}
