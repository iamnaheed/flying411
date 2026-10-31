<?php
include 'config.php';

$manufacturer = $_GET['manufacturer'] ?? '';

if ($manufacturer) {
    try {
        $stmt = $pdo->prepare("SELECT DISTINCT engine_model FROM engines_details WHERE manufacturer = :manufacturer ORDER BY engine_model");
        $stmt->execute([':manufacturer' => $manufacturer]);
        $models = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($models);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
}
?>
