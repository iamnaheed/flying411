<?php
header("Content-Type: application/json");
require_once "config.php";

$category = trim($_POST["category"] ?? '');
$model    = trim($_POST["model"] ?? '');

// Normalize input (remove - _ / spaces + lowercase)
$normalizedInput = strtolower(str_replace(['-', '_', '/', ' '], '', $model));
$params = [];

$sql = "SELECT * FROM aircrafts WHERE status='approved' AND deleted_at IS NULL";

// -------------------------------------------------
// 1️⃣ Filter by category
// -------------------------------------------------
if (!empty($category)) {
    $sql .= " AND LOWER(aircraft_type) = LOWER(:category)";
    $params[':category'] = $category;
}

// -------------------------------------------------
// 2️⃣ FULL normalized search for model/manufacturer/type_designer/aircraft_type
// -------------------------------------------------
if (!empty($model)) {
    $sql .= " AND (
        REPLACE(REPLACE(REPLACE(REPLACE(LOWER(model), '-', ''), '_', ''), '/', ''), ' ', '') LIKE :norm
        OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(manufacturer), '-', ''), '_', ''), '/', ''), ' ', '') LIKE :norm
        OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(type_designer), '-', ''), '_', ''), '/', ''), ' ', '') LIKE :norm
        OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(aircraft_type), '-', ''), '_', ''), '/', ''), ' ', '') LIKE :norm
    )";
    $params[':norm'] = "%$normalizedInput%";
}


$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$aircrafts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Enhanced URL + image logic
foreach ($aircrafts as &$aircraft) {

    // ----  Fetch first image ----
    $img_stmt = $pdo->prepare("
        SELECT image_url 
        FROM product_images 
        WHERE product_id=? AND product_type='aircraft'
        ORDER BY sort_order ASC, image_id ASC 
        LIMIT 1
    ");
    $img_stmt->execute([$aircraft['aircraft_id']]);
    $aircraft['first_image'] = $img_stmt->fetchColumn();

    // Normalize designer for checking match
    $typeDesigner = $aircraft['type_designer'] ?? '';
    $normalizedDesigner = strtolower(str_replace(['-', '_', '/', ' '], '', $typeDesigner));

    // ----  URL Building Logic ----

    // 1️⃣ If input matches designer pattern → numeric only URL (ex: C-172 → 172)
    if (!empty($typeDesigner) && strpos($normalizedDesigner, $normalizedInput) !== false) {
        $numericPart = preg_replace('/\D/', '', $typeDesigner);
        $aircraft['url'] = "/aircraft?model=" . urlencode($numericPart);
    }

    // 2️⃣ Exact match on aircraft_type → category URL
    elseif (!empty($aircraft['aircraft_type']) &&
            strtolower($aircraft['aircraft_type']) === strtolower($model)) {
        $aircraft['url'] = "/aircraft?category=" . urlencode($aircraft['aircraft_type']);
    }

    // 3️⃣ Default model URL
    else {
        $aircraft['url'] = "/aircraft?model=" . urlencode($aircraft['model']);
    }
}

echo json_encode($aircrafts, JSON_PRETTY_PRINT);
?>
