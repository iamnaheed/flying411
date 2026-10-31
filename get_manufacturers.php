<?php
include 'config.php';

try {
    $stmt = $pdo->query("SELECT DISTINCT manufacturer FROM engines_details ORDER BY manufacturer");
    $manufacturers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($manufacturers);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
