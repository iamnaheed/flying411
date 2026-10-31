<?php
require 'config.php'; // make sure you have a DB connection in this file

// Read and decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['order']) || !is_array($data['order'])) {
    http_response_code(400);
    echo "Invalid data.";
    exit;
}

$imageOrder = $data['order'];

try {
    // Prepare update query
    $stmt = $pdo->prepare("UPDATE product_images SET sort_order = ? WHERE image_id = ? AND product_type = 'part'");

    // Loop through ordered IDs and update
    foreach ($imageOrder as $index => $imageId) {
        $stmt->execute([$index, $imageId]);
    }

    echo "Part image order updated successfully.";
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
}
