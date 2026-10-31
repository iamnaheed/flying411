<?php
include "config.php"; // Database connection

if (isset($_GET['vendor_id']) && isset($_GET['status'])) {
    $vendor_id = $_GET['vendor_id'];
    $new_status = $_GET['status'];

    $stmt = $pdo->prepare("UPDATE vendors SET status = ? WHERE vendor_id = ?");
    $stmt->execute([$new_status, $vendor_id]);

    header("Location: admin-vendors.php?success=1");
    exit;
}
?>
