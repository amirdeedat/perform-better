<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$student = trim($data['student_name'] ?? '');
$items = $data['items'] ?? [];

if($student==='' || !is_array($items) || !count($items)){
  echo json_encode(['success'=>false,'message'=>'Invalid order']); exit;
}

$total = 0;
$normalized = [];
// Validate prices from DB to prevent tampering
$ids = array_map(fn($i)=>intval($i['id']), $items);
if (count($ids)) {
  $in = implode(',', array_fill(0,count($ids),'?'));
  $types = str_repeat('i', count($ids));
  $stmt = $conn->prepare("SELECT id, name, price FROM menu_items WHERE id IN ($in)");
  $stmt->bind_param($types, ...$ids);
  $stmt->execute();
  $res = $stmt->get_result();
  $priceMap = [];
  while($row=$res->fetch_assoc()){ $priceMap[$row['id']]=$row; }
  foreach($items as $it){
    $id = intval($it['id']); $qty = intval($it['quantity']??0);
    if($qty<=0 || !isset($priceMap[$id])) continue;
    $name = $priceMap[$id]['name'];
    $price = (float)$priceMap[$id]['price'];
    $subtotal = $price * $qty;
    $total += $subtotal;
    $normalized[] = ['id'=>$id,'name'=>$name,'price'=>$price,'quantity'=>$qty];
  }
}

if(!count($normalized)){ echo json_encode(['success'=>false,'message'=>'No valid items']); exit; }

$stmt = $conn->prepare("INSERT INTO orders (student_name, items_json, total) VALUES (?,?,?)");
$json = json_encode($normalized, JSON_UNESCAPED_UNICODE);
$stmt->bind_param("ssd", $student, $json, $total);
$ok = $stmt->execute();
if($ok){
  $id = $stmt->insert_id;
  echo json_encode([
    'success'=>true,
    'order_id'=>$id,
    'order'=>[
      'id'=>$id,'student_name'=>$student,'items'=>$normalized,
      'total'=>$total,'status'=>'pending','created_at'=>date('Y-m-d H:i:s')
    ]
  ]);
}else{
  echo json_encode(['success'=>false]);
}
