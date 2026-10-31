<?php
include "config.php";

$query = "SELECT DISTINCT aircraft_type FROM aircraft_models ";
$stmt = $pdo->prepare($query);
$stmt->execute();
$aircraftTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($aircraftTypes);
?>

