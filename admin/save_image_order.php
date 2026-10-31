<?php

<?php
session_start();
require "config.php";

// Check admin
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['order']) || !is_array($data['order'])) {
    http_response_code(400);
    echo "Invalid request.";
    exit;
}

$order = $data['order'];
$type  = $data['type'] ?? 'aircraft'; // default to aircraft if not provided

try {
    $pdo->beginTransaction();

    foreach ($order as $sort => $id) {
        // Ignore new files (they have IDs like new-...)
        if (strpos($id, "new-") === 0) {
            continue;
        }

        $stmt = $pdo->prepare("
            UPDATE product_images
            SET sort_order = ?
            WHERE image_id = ? AND product_type = ?
        ");
        $stmt->execute([$sort, $id, $type]);
    }

    $pdo->commit();
    echo ucfirst($type) . " image order updated successfully!";
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo "Error saving order: " . $e->getMessage();
}

?>