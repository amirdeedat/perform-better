<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) { echo json_encode(['success'=>false]); exit; }
require 'db.php';

$id = intval($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$allowed = ['pending','preparing','ready','completed'];
if($id<=0 || !in_array($status, $allowed)){ echo json_encode(['success'=>false]); exit; }

$stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $id);
$ok = $stmt->execute();
echo json_encode(['success'=>$ok]);
