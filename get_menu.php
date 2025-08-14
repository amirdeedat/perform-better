<?php
header('Content-Type: application/json');
require 'db.php';
$res = $conn->query("SELECT id, name, description, price, category FROM menu_items ORDER BY created_at DESC");
$menu = [];
while($row = $res->fetch_assoc()){ $menu[] = $row; }
echo json_encode(['menu'=>$menu]);
