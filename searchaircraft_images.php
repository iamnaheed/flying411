<?php
header("Content-Type: application/json");
require_once "config.php"; 

$aircraft_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($aircraft_id > 0) {
    $stmt = $pdo->prepare("SELECT image_url 
                           FROM product_images 
                           WHERE product_id = ? 
                           AND product_type = 'aircraft' 
                           ORDER BY sort_order ASC, image_id ASC");
    $stmt->execute([$aircraft_id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        "aircraft_id" => $aircraft_id,
        "images" => $images
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode(["error" => "Invalid aircraft ID"]);
}
?>
