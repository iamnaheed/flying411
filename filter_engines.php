<?php
require 'config.php';
header("Content-Type: application/json");

// helper to always get array
function arr($key) {
    return isset($_POST[$key]) ? (array)$_POST[$key] : [];
}

$conditions    = arr('condition');
$engineTypes   = arr('engine_type');
$manufacturers = arr('manufacturer');
$locations     = arr('location');
$listingTypes  = arr('listing_type');
$dealers       = arr('dealer');       // vendor_id array

$yearMin  = $_POST['year_min']  ?? null;
$yearMax  = $_POST['year_max']  ?? null;
$priceMin = $_POST['price_min'] ?? null;
$priceMax = $_POST['price_max'] ?? null;

$query = "
    SELECT e.*,
        (
            SELECT GROUP_CONCAT(image_url ORDER BY sort_order ASC)
            FROM product_images
            WHERE product_id = e.engine_id
              AND product_type = 'engine'
        ) AS images
    FROM engines e
    WHERE e.status = 'approved'
";

$params = [];
$conditionsSql = [];

$addIn = function (&$conditionsSql, &$params, $column, $values) {
    if (!empty($values)) {
        $placeholders = implode(",", array_fill(0, count($values), "?"));
        $conditionsSql[] = "$column IN ($placeholders)";
        foreach ($values as $v) {
            $params[] = $v;
        }
    }
};

// multi filters
$addIn($conditionsSql, $params, "e.`condition`", $conditions);
$addIn($conditionsSql, $params, "e.engine_type",  $engineTypes);
$addIn($conditionsSql, $params, "e.manufacturer", $manufacturers);
$addIn($conditionsSql, $params, "e.country",     $locations);
$addIn($conditionsSql, $params, "e.vendor_id",    $dealers);

// only if you REALLY have this column:
$addIn($conditionsSql, $params, "e.listing_type", $listingTypes);

// ranges
if ($yearMin !== null && $yearMin !== '') {
    $conditionsSql[] = "e.year >= ?";
    $params[] = (int)$yearMin;
}
if ($yearMax !== null && $yearMax !== '') {
    $conditionsSql[] = "e.year <= ?";
    $params[] = (int)$yearMax;
}
if ($priceMin !== null && $priceMin !== '') {
    $conditionsSql[] = "e.price >= ?";
    $params[] = (float)$priceMin;
}
if ($priceMax !== null && $priceMax !== '') {
    $conditionsSql[] = "e.price <= ?";
    $params[] = (float)$priceMax;
}

if (!empty($conditionsSql)) {
    $query .= " AND " . implode(" AND ", $conditionsSql);
}

$query .= " ORDER BY e.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
