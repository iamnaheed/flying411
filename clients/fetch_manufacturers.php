<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['aircraft_type'])) {
    $aircraft_type = $_POST['aircraft_type'];

    $stmt = $pdo->prepare("SELECT DISTINCT manufacturer FROM aircraft_models WHERE aircraft_type = ?");
    $stmt->execute([$aircraft_type]);
    $manufacturers = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($manufacturers);
}
?>
