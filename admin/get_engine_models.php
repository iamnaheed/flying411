<?php
require 'config.php';

if (isset($_POST['manufacturer'])) {
    $manufacturer = $_POST['manufacturer'];

    $query = "SELECT DISTINCT engine_model FROM engines_details WHERE manufacturer = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$manufacturer]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='".htmlspecialchars($row['engine_model'])."'>".htmlspecialchars($row['engine_model'])."</option>";
    }
}
?>
