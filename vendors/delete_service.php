<?php
session_start();
require 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

// Ensure service ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: list_of_services.php?error=Invalid request");
    exit;
}

$service_id = $_GET['id'];

try {
    // Check if the service belongs to the vendor
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = :service_id AND vendor_id = :vendor_id");
    $stmt->execute([':service_id' => $service_id, ':vendor_id' => $vendor_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        header("Location: list_of_services.php?error=Service not found or unauthorized");
        exit;
    }

    // Delete the service
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = :service_id AND vendor_id = :vendor_id");
    $stmt->execute([':service_id' => $service_id, ':vendor_id' => $vendor_id]);

    // Redirect with success message
    header("Location: list_of_services.php?success=Service deleted successfully");
    exit;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
