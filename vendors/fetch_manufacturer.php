<?php
include 'config.php'; // your PDO connection

$query = $_GET['q'] ?? '';
$query = trim($query);

if ($query === '') {
    echo json_encode([]);
    exit;
}

// Fetch distinct manufacturers from aircrafts table
$stmt = $pdo->prepare("
    SELECT DISTINCT manufacturer 
    FROM aircrafts 
    WHERE manufacturer LIKE :q
    ORDER BY manufacturer ASC
    LIMIT 20
");
$stmt->execute([':q' => "%$query%"]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
