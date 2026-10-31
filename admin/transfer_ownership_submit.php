<?php
session_start();
include "config.php"; // PDO connection

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Get POST data
$service_id = isset($_POST['service_id']) ? $_POST['service_id'] : '';
$new_vendor_id = isset($_POST['new_vendor_id']) ? $_POST['new_vendor_id'] : '';

if (empty($service_id) || empty($new_vendor_id)) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Please select both service and vendor.'];
    header("Location: transfer_ownership.php");
    exit;
}

// Update service vendor
try {
    $stmt = $pdo->prepare("UPDATE services SET vendor_id = :vendor_id WHERE id = :service_id");
    $stmt->execute([
        ':vendor_id' => $new_vendor_id,
        ':service_id' => $service_id
    ]);

    $_SESSION['message'] = ['type' => 'success', 'text' => 'Service transferred successfully!'];
} catch (PDOException $e) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Transfer failed: ' . $e->getMessage()];
}

// Redirect back to main page
header("Location: transfer_ownership.php");
exit;
