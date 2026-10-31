<?php
header("Content-Type: application/json");
require "config.php";

/* ------------------------------------------------------------
   HELPER: Get POST array safely
------------------------------------------------------------ */
function arr($key) {
    return isset($_POST[$key]) ? (array)$_POST[$key] : [];
}

/* ------------------------------------------------------------
   READ FILTER VALUES
------------------------------------------------------------ */
$types      = arr('types');
$makes      = arr('makes');
$models     = arr('models');
$locations  = arr('locations');
$dealers    = arr('dealers');
$flags      = arr('flags');

$yearMin    = $_POST['year_min'] ?? null;
$yearMax    = $_POST['year_max'] ?? null;
$priceMin   = $_POST['price_min'] ?? null;
$priceMax   = $_POST['price_max'] ?? null;

/* ------------------------------------------------------------
   BASE QUERY
------------------------------------------------------------ */
$query = "
    SELECT 
        a.*,
        (
            SELECT GROUP_CONCAT(image_url ORDER BY sort_order ASC)
            FROM product_images
            WHERE product_id = a.aircraft_id 
              AND product_type = 'aircraft'
        ) AS images
    FROM aircrafts a
    WHERE a.status = 'approved'
";

$conditions = [];
$params     = [];

/* ------------------------------------------------------------
   HELPER: ADD IN() FILTER
------------------------------------------------------------ */
$addIn = function ($column, $values) use (&$conditions, &$params) {
    if (!empty($values)) {
        $placeholders = implode(",", array_fill(0, count($values), "?"));
        $conditions[] = "$column IN ($placeholders)";
        foreach ($values as $v) $params[] = $v;
    }
};

/* ------------------------------------------------------------
   APPLY FILTERS
------------------------------------------------------------ */
$addIn("a.aircraft_type", $types);
$addIn("a.manufacturer", $makes);
$addIn("a.model", $models);
$addIn("a.country", $locations);
$addIn("a.vendor_id", $dealers);

/* ------------------------------------------------------------
   RANGE FILTERS
------------------------------------------------------------ */
if ($yearMin !== null && $yearMin !== "") {
    $conditions[] = "a.year >= ?";
    $params[] = $yearMin;
}

if ($yearMax !== null && $yearMax !== "") {
    $conditions[] = "a.year <= ?";
    $params[] = $yearMax;
}

if ($priceMin !== null && $priceMin !== "") {
    $conditions[] = "a.price >= ?";
    $params[] = $priceMin;
}

if ($priceMax !== null && $priceMax !== "") {
    $conditions[] = "a.price <= ?";
    $params[] = $priceMax;
}

/* ------------------------------------------------------------
   COMBINE WHERE CONDITIONS
------------------------------------------------------------ */
if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}

$query .= " ORDER BY a.created_at DESC";

/* ------------------------------------------------------------
   EXECUTE QUERY
------------------------------------------------------------ */
$stmt = $pdo->prepare($query);
$stmt->execute($params);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ------------------------------------------------------------
   RETURN RESULT
------------------------------------------------------------ */
echo json_encode($rows);
exit;
