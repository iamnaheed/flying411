<?php
require_once 'config.php'; // Adjust path if needed

try {
    $sql = "
        UPDATE aircrafts a
        JOIN aircraft_models am ON a.model = am.model
        SET a.type_designer = am.type_designator
        WHERE a.type_designer IS NULL OR a.type_designer = ''
    ";

    $affected = $pdo->exec($sql);
    $timestamp = date('Y-m-d H:i:s');
    echo "[$timestamp] Updated $affected rows.\n";

} catch (PDOException $e) {
    error_log("Cron Error: " . $e->getMessage());
    echo "Error updating aircraft table: " . $e->getMessage();
}
?>
