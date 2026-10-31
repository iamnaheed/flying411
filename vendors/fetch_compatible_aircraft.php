<?php
include 'config.php';

$q = $_GET['q'] ?? '';

$stmt = $pdo->prepare("
    SELECT aircraft_id, manufacturer, model
    FROM aircrafts
    WHERE model LIKE ? OR manufacturer LIKE ?
    AND status = 'active' AND deleted_at IS NULL
    LIMIT 10
");
$stmt->execute(["%$q%", "%$q%"]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
