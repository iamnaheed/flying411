<?php
session_start();
include "config.php";

// Ensure user is logged in
if (!isset($_SESSION['vendor_id'])) {
    die("Unauthorized access.");
}

if (isset($_GET['id'])) {
    $notification_id = intval($_GET['id']);
    $vendor_id = $_SESSION['vendor_id'];

    $query = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND vendor_id = ?");
    $query->execute([$notification_id, $vendor_id]);

    header("Location: notifications.php");
    exit;
} else {
    die("Invalid request.");
}
?>
