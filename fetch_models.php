<?php
include 'config.php'; // Include the database connection

$manufacturer = $_GET['manufacturer'] ?? '';

if ($manufacturer) {
    try {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("SELECT DISTINCT engine_model FROM engines_details WHERE manufacturer = :manufacturer");
        $stmt->bindParam(":manufacturer", $manufacturer, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch results
        $models = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return JSON response
        echo json_encode($models);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
}
?>
