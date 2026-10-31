<?php
require 'config.php'; 

header("Content-Type: application/json");

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$suggestions = [];

if (!empty($query)) {
    try {
        if (!$pdo) {
            throw new Exception("Database connection error.");
        }

        // Debugging: Log the query
        file_put_contents("debug_log.txt", "Query: SELECT DISTINCT service_type FROM services WHERE service_type LIKE '%$query%'\n", FILE_APPEND);

        $stmt = $pdo->prepare("SELECT DISTINCT service_type FROM services WHERE service_type LIKE :search LIMIT 10");
        $search = "%$query%";
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Debugging: Log the database results
        file_put_contents("debug_log.txt", "Results: " . print_r($results, true) . "\n", FILE_APPEND);

        if (!empty($results)) {
            $suggestions = array_filter($results, fn($item) => is_string($item) && !empty(trim($item)));
        }

    } catch (Exception $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
        exit;
    }
}

echo json_encode($suggestions ?: []);

// Debugging: Log final JSON output
file_put_contents("debug_log.txt", "Final Output: " . json_encode($suggestions) . "\n", FILE_APPEND);
?>
