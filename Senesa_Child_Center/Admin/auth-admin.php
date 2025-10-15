<?php
// auth-admin.php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: admin-login.html');
  exit;
}
if (!in_array($_SESSION['role'], ['Admin','Super Admin'])) {
  header('Location: admin-login.html');
  exit;
}
