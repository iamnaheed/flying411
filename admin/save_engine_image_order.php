<?php
include "config.php";

$data = json_decode(file_get_contents("php://input"), true);
$order = $data['order'] ?? [];
$type = $data['type'] ?? '';

if (empty($order) || !in_array($type, ['aircraft', 'engine'])) {
    http_response_code(400);
    echo "Invalid request.";
    exit;
}

try {
    foreach ($order as $index => $id) {
        $stmt = $pdo->prepare("UPDATE product_images SET sort_order = ? WHERE image_id = ? AND product_type = ?");
        $stmt->execute([$index, $id, $type]);
    }
    echo "Image order saved successfully.";
} catch (Exception $e) {
    http_response_code(500);
    echo "Failed to save image order.";
}
?>
