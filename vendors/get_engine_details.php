<?php
require 'config.php';

if (isset($_POST['model'])) {
    $model = $_POST['model'];

    $query = "SELECT engine_type, power_thrust FROM engines_details WHERE engine_model = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$model]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($result);
}
?>
