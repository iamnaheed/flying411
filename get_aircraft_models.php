<?php
include "config.php";
header('Content-Type: application/json');
$aircraft_type = $_POST['category'] ?? '';
$offset = isset($_POST['offset']) ? (int) $_POST['offset'] : 0;
$limit = isset($_POST['limit']) ? (int) $_POST['limit'] : 100;

if (empty($aircraft_type)) {
    echo json_encode([]);
    exit;
}

$query = "SELECT DISTINCT model FROM aircraft_models WHERE aircraft_type = :aircraft_type LIMIT :limit OFFSET :offset ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':aircraft_type', $aircraft_type, PDO::PARAM_STR);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$models = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($models);
?>
