<?php
require 'config.php';

$aircraftId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$response = [];

if ($aircraftId > 0) {
    $stmt = $pdo->prepare("
        SELECT image_url FROM product_images 
        WHERE product_type = 'aircraft' AND product_id = ? 
        ORDER BY sort_order ASC
    ");
    $stmt->execute([$aircraftId]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Remove first image (already loaded in aircraft.php)
    if (!empty($images)) {
        array_shift($images);
    }

    $response = $images;
}

header('Content-Type: application/json');
echo json_encode($response);
