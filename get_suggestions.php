<?php
header("Content-Type: application/json");
require_once "config.php";

$search_query = isset($_GET["query"]) ? trim($_GET["query"]) : "";

if (empty($search_query)) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT DISTINCT manufacturer FROM engines WHERE manufacturer LIKE :search 
        UNION 
        SELECT DISTINCT model FROM engines WHERE model LIKE :search 
        UNION 
        SELECT DISTINCT engine_type FROM engines WHERE engine_type LIKE :search";

$stmt = $pdo->prepare($sql);
$stmt->execute([":search" => "%$search_query%"]);

$results = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($results);
?>
