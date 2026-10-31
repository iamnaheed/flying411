<?php
include "config.php";
session_start();

if (!isset($_SESSION['vendor_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['order']) || !is_array($data['order'])) {
    http_response_code(400);
    echo "Invalid data";
    exit();
}

$order = $data['order'];
foreach ($order as $sort => $image_id) {
    $stmt = $pdo->prepare("
        UPDATE product_images pi 
        JOIN aircrafts a ON a.aircraft_id = pi.product_id
        SET pi.sort_order = ?
        WHERE pi.image_id = ? AND pi.product_type = 'aircraft' AND a.vendor_id = ?
    ");
    $stmt->execute([$sort, $image_id, $_SESSION['vendor_id']]);
}

echo "Order updated successfully";
