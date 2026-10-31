<?php
include "config.php"; // DB connection

if (isset($_POST['category'])) {
    $category = $_POST['category'];
    $products = [];

    if ($category == 'aircraft') {
        $stmt = $pdo->query("SELECT aircraft_id AS id, model AS name FROM aircrafts");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($category == 'engine') {
        $stmt = $pdo->query("SELECT engine_id AS id, model AS name FROM engines");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($category == 'parts') {
        $stmt = $pdo->query("SELECT part_id AS id, part_name AS name FROM parts");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Return the product data as JSON
    echo json_encode($products);
}
?>
