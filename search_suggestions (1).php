<?php
header('X-Robots-Tag: noindex, nofollow', true);
require_once 'config.php';

$query = strtolower(trim($_GET['q'] ?? ''));
if (!$query) {
    echo json_encode([]);
    exit;
}

$results = [];
$seen = [];

// Search all columns in aircraft_models
$stmt = $pdo->prepare("
    SELECT grouped_model_display_names, model, manufacturer, aircraft_type, type_designator, model_types
    FROM aircraft_models
    WHERE LOWER(grouped_model_display_names) LIKE ?
       OR LOWER(model) LIKE ?
       OR LOWER(model_types) LIKE ?
       OR LOWER(manufacturer) LIKE ?
       OR LOWER(aircraft_type) LIKE ?
       OR LOWER(type_designator) LIKE ?
    ORDER BY grouped_model_display_names ASC, manufacturer ASC, model ASC
");
$stmt->execute([
    "%$query%", "%$query%", "%$query%",
    "%$query%", "%$query%", "%$query%"
]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $category = trim($row['grouped_model_display_names']);
    $model = trim($row['model']);
    $manufacturer = trim($row['manufacturer']);
    $designator = trim($row['type_designator']);

    // Category
    if ($category && !isset($seen['cat_'.$category])) {
        $seen['cat_'.$category] = true;
        $results[] = [
            'name' => ucwords($category),
            'url'  => "/aircraft?category=" . urlencode($category),
            'type' => 'category'
        ];
    }

    // Manufacturer
    if ($manufacturer && !isset($seen['man_'.$manufacturer])) {
        $seen['man_'.$manufacturer] = true;
        $results[] = [
            'name' => ucwords($manufacturer),
            'url'  => "/aircraft?manufacture=" . urlencode($manufacturer),
            'type' => 'manufacturer'
        ];
    }

    // Type Designator
    if ($designator && !isset($seen['des_'.$designator])) {
        $seen['des_'.$designator] = true;
        $results[] = [
            'name' => strtoupper($designator),
            'url'  => "/aircraft?type_designator=" . urlencode($designator),
            'type' => 'type_designator'
        ];
    }

    // Model
    $modelName = $manufacturer . ' ' . $model;
    if ($model && !isset($seen['model_'.$modelName])) {
        $seen['model_'.$modelName] = true;
        $results[] = [
            'name' => ucwords($modelName),
            'url'  => "/aircraft?model=" . urlencode($model),
            'type' => 'model'
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($results);
