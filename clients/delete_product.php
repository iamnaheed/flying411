<?php
session_start();
include "config.php"; // Include database connection

if (!isset($_GET['type']) || !isset($_GET['id'])) {
    die("Invalid request");
}

$type = trim(strtolower($_GET['type'])); // Normalize input
$id = intval($_GET['id']); // Ensure ID is an integer

// Debugging: Check what value is being passed
// var_dump($type); exit;

// Mapping singular and plural types to the correct table
$table_map = [
    'aircraft' => ['table' => 'aircrafts', 'id_column' => 'aircraft_id'],
    'aircrafts' => ['table' => 'aircrafts', 'id_column' => 'aircraft_id'],
    'part' => ['table' => 'parts', 'id_column' => 'part_id'],
    'parts' => ['table' => 'parts', 'id_column' => 'part_id'],
    'engine' => ['table' => 'engines', 'id_column' => 'engine_id'],
    'engines' => ['table' => 'engines', 'id_column' => 'engine_id'],
];

// Ensure valid type
if (!array_key_exists($type, $table_map)) {
    die("Invalid product type: " . htmlspecialchars($type));
}

$table = $table_map[$type]['table'];
$id_column = $table_map[$type]['id_column'];

try {
    $pdo->beginTransaction();

    // Fetch images before deletion
    $stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND product_type = ?");
    $stmt->execute([$id, $type]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Delete images from the database
    $stmt = $pdo->prepare("DELETE FROM product_images WHERE product_id = ? AND product_type = ?");
    $stmt->execute([$id, $type]);

    // Delete images from the server
    foreach ($images as $image) {
        $image_path = __DIR__ . "/uploads/" . $image['image_url']; // Adjust path as needed
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Delete the product
    $stmt = $pdo->prepare("DELETE FROM $table WHERE $id_column = ?");
    $stmt->execute([$id]);

    $pdo->commit();
    header("Location: manage_products.php?message=Product deleted successfully");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error deleting product: " . $e->getMessage());
}
?>
