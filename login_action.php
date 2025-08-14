<?php
session_start();
require 'db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT id, username, password_hash FROM admins WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
  if (password_verify($password, $row['password_hash'])) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $row['username'];
    header("Location: admin.php"); exit;
  }
}
header("Location: login.php?error=1"); exit;
