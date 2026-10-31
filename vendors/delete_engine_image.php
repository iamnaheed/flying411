<?php
include "config.php";

// Possibly check session / vendor permissions

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['image_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing image_id.']);
    exit;
}

$image_id = (int)$data['image_id'];

// Optionally fetch the image_url so you can delete the file from disk
$stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE image_id = ?");
$stmt->execute([$image_id]);
$image = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$image) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Image not found.']);
    exit;
}

$delete_stmt = $pdo->prepare("DELETE FROM product_images WHERE image_id = ?");
$success = $delete_stmt->execute([$image_id]);

if ($success) {
    // Optionally delete the file on disk
    $file_path = $image['image_url'];
    if (file_exists($file_path)) {
        @unlink($file_path);
    }
    echo json_encode(['success' => true, 'message' => 'Image deleted successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete image.']);
}
