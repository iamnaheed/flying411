<?php
header("Content-Type: application/json");
require_once "config.php"; 

$manufacturer = isset($_POST["manufacturer"]) ? trim($_POST["manufacturer"]) : "";
$model = isset($_POST["model"]) ? trim($_POST["model"]) : "";
$search_query = isset($_POST["search_query"]) ? trim($_POST["search_query"]) : "";

// Base Query
$sql = "SELECT * FROM engines WHERE status = 'approved'";
$params = [];

// Dynamic Filtering
if (!empty($manufacturer)) {
    $sql .= " AND manufacturer = :manufacturer";
    $params[":manufacturer"] = $manufacturer;
}

if (!empty($model)) {
    $sql .= " AND model = :model";
    $params[":model"] = $model;
}

if (!empty($search_query)) {
    $sql .= " AND (manufacturer LIKE :search OR model LIKE :search OR engine_type LIKE :search)";
    $params[":search"] = "%$search_query%";
}

// Execute Query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$engines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Images for Each Engine
foreach ($engines as &$engine) {
    $img_stmt = $pdo->prepare("SELECT image_url ORDER BY sort_order ASC FROM product_images WHERE product_id = ?");
    $img_stmt->execute([$engine["engine_id"]]);
    $engine["images"] = $img_stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
}

// Return JSON Response
echo json_encode($engines);
?>
