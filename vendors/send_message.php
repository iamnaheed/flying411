<?php
session_start();
include "config.php"; // Database connection

if (!isset($_SESSION['vendor_id'])) {
   header("Location: ../login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $query = $pdo->prepare("
            INSERT INTO support_messages (vendor_id, message, sender, status) 
            VALUES (?, ?, 'vendor', 'unread')
        ");
        $query->execute([$vendor_id, $message]);

        $_SESSION['success'] = "Message sent successfully!";
    } else {
        $_SESSION['error'] = "Message cannot be empty.";
    }
}

header("Location: messages-support.php");
exit;
?>
