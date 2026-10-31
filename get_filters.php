<?php
header("Content-Type: application/json");
require "config.php";

// Get POST data
$type = $_POST['type'] ?? null;
$manufacturer = $_POST['make'] ?? null; // ❤️ JS sends "make", not "manufacturer"

// Prepare response structure
$response = [
    "makes"  => [],
    "models" => []
];

/* ============================================================
   1. LOAD MAKES (Manufacturer)
============================================================ */
if (!$type) {
    // Load ALL manufacturers
    $stmt = $pdo->query("
        SELECT manufacturer AS name, COUNT(*) AS count
        FROM aircrafts
        WHERE status='approved'
        GROUP BY manufacturer
        ORDER BY manufacturer ASC
    ");
} else {
    // Load manufacturers for selected type
    $stmt = $pdo->prepare("
        SELECT manufacturer AS name, COUNT(*) AS count
        FROM aircrafts
        WHERE status='approved' AND aircraft_type = ?
        GROUP BY manufacturer
        ORDER BY manufacturer ASC
    ");
    $stmt->execute([$type]);
}

$response["makes"] = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ============================================================
   2. LOAD MODELS
============================================================ */
if ($manufacturer) {
    // Models for selected manufacturer
    $stmt = $pdo->prepare("
        SELECT model AS name, COUNT(*) AS count
        FROM aircrafts
        WHERE status='approved' AND manufacturer = ?
        GROUP BY model
        ORDER BY model ASC
    ");
    $stmt->execute([$manufacturer]);

} else {
    // Load ALL models
    $stmt = $pdo->query("
        SELECT model AS name, COUNT(*) AS count
        FROM aircrafts
        WHERE status='approved'
        GROUP BY model
        ORDER BY model ASC
    ");
}

$response["models"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return JSON
echo json_encode($response);
?>