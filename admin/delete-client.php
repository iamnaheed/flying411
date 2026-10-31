<?php
session_start();
include "config.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['vendor_id'])) {
    $vendor_id = $_POST['vendor_id'];

    // Only delete if the role is 'client'
    $stmt = $pdo->prepare("DELETE FROM vendors WHERE vendor_id = ? AND user_role = 'client'");
    $stmt->execute([$vendor_id]);

    $_SESSION['success_message'] = "Client deleted successfully.";
    header("Location: admin-clients.php");
    exit();
}

header("Location: admin-clients.php");
exit();
?>
