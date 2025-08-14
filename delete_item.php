<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) { echo json_encode(['success'=>false]); exit; }
require 'db.php';
$id = intval($_GET['id'] ?? 0);
if($id<=0){ echo json_encode(['success'=>false]); exit; }
$stmt = $conn->prepare("DELETE FROM menu_items WHERE id=?");
$stmt->bind_param("i", $id);
$ok = $stmt->execute();
echo json_encode(['success'=>$ok]);
