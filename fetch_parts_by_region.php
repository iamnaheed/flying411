<?php
require 'config.php';

$part_number = isset($_POST['part_number']) ? $_POST['part_number'] : '';

// Base query
$query = "SELECT * FROM parts WHERE status = 'approved'";
$params = [];

// Filter by part number
if (!empty($part_number)) {
    $query .= " AND part_number LIKE ?";
    $params[] = "%$part_number%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch images for each part
foreach ($parts as &$part) {
    $imgQuery = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND product_type = 'part' ORDER BY created_at ASC");
    $imgQuery->execute([$part['part_id']]);
    $part['images'] = array_column($imgQuery->fetchAll(PDO::FETCH_ASSOC), 'image_url');
}

echo json_encode($parts);
?>
