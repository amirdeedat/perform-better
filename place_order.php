<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$studentName = $data['studentName'];
$total = $data['total'];
$items = $data['items'];

$conn->query("INSERT INTO orders (student_name, total) VALUES ('$studentName', '$total')");
$orderId = $conn->insert_id;

foreach ($items as $item) {
    $menuId = $item['id'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    $subtotal = $item['subtotal'];
    $conn->query("INSERT INTO order_items (order_id, menu_id, quantity, price, subtotal) VALUES ('$orderId', '$menuId', '$quantity', '$price', '$subtotal')");
}

echo json_encode(["orderId" => $orderId]);
?>
