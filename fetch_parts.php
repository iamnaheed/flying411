<?php
require 'config.php'; // Include your database connection

$conditions = isset($_POST['conditions']) ? $_POST['conditions'] : [];

$query = "SELECT * FROM parts WHERE status = 'approved'";

// Apply condition filter if selected
if (!empty($conditions)) {
    $placeholders = implode(',', array_fill(0, count($conditions), '?'));
    $query .= " AND `condition` IN ($placeholders)";
}

$stmt = $pdo->prepare($query);
$stmt->execute($conditions);
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($parts as &$part) {
$imgQuery = $pdo->prepare("SELECT image_id, image_url FROM product_images WHERE product_id = ? AND product_type = 'part' ORDER BY sort_order ASC");
    $imgQuery->execute([$part['part_id']]);
    $part['images'] = array_column($imgQuery->fetchAll(PDO::FETCH_ASSOC), 'image_url');
}

echo json_encode($parts);
?>
