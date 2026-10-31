<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['aircraft_type']) && isset($_POST['manufacturer'])) {
    $aircraft_type = $_POST['aircraft_type'];
    $manufacturer = $_POST['manufacturer'];

    $stmt = $pdo->prepare("SELECT model FROM aircraft_models WHERE aircraft_type = ? AND manufacturer = ?");
    $stmt->execute([$aircraft_type, $manufacturer]);
    $models = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($models);
}
?>
