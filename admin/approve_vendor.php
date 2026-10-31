<?php
include "config.php"; // Database connection

if (isset($_GET['vendor_id'])) {
    $vendor_id = $_GET['vendor_id'];

    // Update vendor status
    $query = $pdo->prepare("UPDATE vendors SET status = 'active' WHERE vendor_id = ?");
    $query->execute([$vendor_id]);

    header("Location: admin-vendors.php?success=1");
    exit;
}
?>
