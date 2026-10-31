<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['location'])) {
    $location = trim($_POST['location']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE country LIKE :location");
        $stmt->execute(['location' => "%$location%"]);
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($services);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
}
?>
