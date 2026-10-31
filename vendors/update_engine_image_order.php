<?php
include "config.php";

// Possibly check session / vendor permissions

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['order']) || !is_array($data['order'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    exit;
}

$order = $data['order'];  // array of image_id in the new order

// Update each image's sort_order
$pdo->beginTransaction();
try {
    foreach ($order as $position => $image_id) {
        $stmt = $pdo->prepare("UPDATE product_images SET sort_order = ? WHERE image_id = ?");
        $stmt->execute([$position, $image_id]);
    }
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Order updated successfully.']);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update order.']);
}
