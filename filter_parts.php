<?php
require 'config.php';
header("Content-Type: application/json");

// helper to always return array
function arr($key) {
    return isset($_POST[$key]) ? (array)$_POST[$key] : [];
}

// these names must match JS: fd.append(type + "s[]", v)
$conditions         = arr('conditions');          // condition[]
$types              = arr('types');               // type[]
$manufacturers      = arr('manufacturers');       // manufacturer[]
$ownedTypes         = arr('owned_types');         // owned_type[]
$materials          = arr('materials');           // material[]
$compatibleAircraft = arr('compatible_aircrafts');// compatible_aircraft[]
$tags               = arr('tags');                // tag (tagged_with_easa_form_1)
$locations          = arr('locations');           // region[]
$dealers            = arr('dealers');             // vendor_id[]

$priceMin = $_POST['price_min'] ?? null;
$priceMax = $_POST['price_max'] ?? null;

$query = "
    SELECT 
        p.*,
        (
            SELECT GROUP_CONCAT(image_url ORDER BY sort_order ASC)
            FROM product_images
            WHERE product_id = p.part_id
              AND product_type = 'part'
        ) AS images
    FROM parts p
    WHERE p.status = 'approved'
";

$params = [];
$conditionsSql = [];

$addIn = function (&$conditionsSql, &$params, $column, $values) {
    if (!empty($values)) {
        $ph = implode(",", array_fill(0, count($values), "?"));
        $conditionsSql[] = "$column IN ($ph)";
        foreach ($values as $v) {
            $params[] = $v;
        }
    }
};

// apply IN filters
$addIn($conditionsSql, $params, "p.`condition`",          $conditions);
$addIn($conditionsSql, $params, "p.`type`",               $types);
$addIn($conditionsSql, $params, "p.manufacturer",         $manufacturers);
$addIn($conditionsSql, $params, "p.owned_type",           $ownedTypes);
$addIn($conditionsSql, $params, "p.material",             $materials);
$addIn($conditionsSql, $params, "p.compatible_aircraft",  $compatibleAircraft);
$addIn($conditionsSql, $params, "p.tagged_with_easa_form_1", $tags);
$addIn($conditionsSql, $params, "p.country",               $locations);
$addIn($conditionsSql, $params, "p.vendor_id",            $dealers);

// price range
if ($priceMin !== null && $priceMin !== '') {
    $conditionsSql[] = "p.price >= ?";
    $params[] = (float)$priceMin;
}
if ($priceMax !== null && $priceMax !== '') {
    $conditionsSql[] = "p.price <= ?";
    $params[] = (float)$priceMax;
}

if (!empty($conditionsSql)) {
    $query .= " AND " . implode(" AND ", $conditionsSql);
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
