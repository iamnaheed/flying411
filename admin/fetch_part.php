<?php
header("Content-Type: application/json");
require 'config.php'; // PDO $pdo

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q === '') {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM parts_master
        WHERE status = 'active' 
          AND (part_number LIKE :q OR part_name LIKE :q)
        ORDER BY part_number ASC
        LIMIT 10
    ");
    $search = "%$q%";
    $stmt->bindParam(':q', $search, PDO::PARAM_STR);
    $stmt->execute();

    $parts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($parts);

} catch (PDOException $e) {
    echo json_encode([]);
}
