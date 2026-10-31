<?php
session_start();
include "config.php"; // Database connection

// Check if vendor_id is provided
if (!isset($_GET['vendor_id']) || empty($_GET['vendor_id'])) {
    echo "<script>alert('Invalid vendor ID.'); window.location.href='admin-vendors.php';</script>";
    exit;
}

$vendor_id = $_GET['vendor_id'];

// Delete vendor
$stmt = $pdo->prepare("DELETE FROM vendors WHERE vendor_id = ?");
if ($stmt->execute([$vendor_id])) {
    echo "<script>alert('Vendor deleted successfully!'); window.location.href='admin-vendors.php';</script>";
} else {
    echo "<script>alert('Error deleting vendor.'); window.location.href='admin-vendors.php';</script>";
}
?>
