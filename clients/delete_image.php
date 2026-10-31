<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include "config.php";
session_start();

if (!isset($_SESSION['vendor_id'])) {
    http_response_code(403);
    echo "Unauthorized (no vendor session)";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_id = intval($_POST['image_id'] ?? 0);

    if (!$image_id) {
        echo "No image_id provided";
        exit();
    }

    // Check ownership
    $stmt = $pdo->prepare("
        SELECT pi.image_url, pi.product_id 
        FROM product_images pi
        JOIN aircrafts a ON a.aircraft_id = pi.product_id
        WHERE pi.image_id = ? AND pi.product_type = 'aircraft' AND a.vendor_id = ?
    ");
    $stmt->execute([$image_id, $_SESSION['vendor_id']]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$image) {
        echo "Image not found or not owned by this vendor";
        exit();
    }

    // Delete from DB
    $del = $pdo->prepare("DELETE FROM product_images WHERE image_id = ?");
    $del->execute([$image_id]);

    // Delete file from server (if exists)
    $file_path = $image['image_url'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    echo "Deleted";
}
?>