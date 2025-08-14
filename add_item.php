<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) { echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit; }

require 'db.php';
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$category = trim($_POST['category'] ?? '');

if($name==='' || $price<=0 || $category===''){
  echo json_encode(['success'=>false,'message'=>'Invalid data']); exit;
}

$stmt = $conn->prepare("INSERT INTO menu_items (name, description, price, category) VALUES (?,?,?,?)");
$stmt->bind_param("ssds", $name, $description, $price, $category);
$ok = $stmt->execute();
echo json_encode(['success'=>$ok]);
