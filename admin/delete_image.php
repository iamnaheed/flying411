<?php
session_start();
include "config.php"; // Database connection

if (!isset($_GET['id']) || !isset($_GET['engine_id'])) {
    die("Invalid request");
}

$image_id  = intval($_GET['id']);   // image_id from product_images
$engine_id = intval($_GET['engine_id']);  // engine_id as product_id
$type      = "engine"; // fixed product_type

try {
    $pdo->beginTransaction();

    // Get image path
    $stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE image_id = ? AND product_id = ? AND product_type = ?");
    $stmt->execute([$image_id, $engine_id, $type]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($image) {
        // Build file path
        $file_path = __DIR__ . "/" . $image['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path); // remove file from server
        }

        // Delete record from DB
        $stmt = $pdo->prepare("DELETE FROM product_images WHERE image_id = ? AND product_id = ? AND product_type = ?");
        $stmt->execute([$image_id, $engine_id, $type]);
    }

    $pdo->commit();

    // Redirect back to edit engine page
    header("Location: edit-engine.php?engine_id=" . $engine_id . "&message=Image deleted successfully");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error deleting image: " . $e->getMessage());
}
?>
