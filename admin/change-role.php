<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_id = $_POST['vendor_id'] ?? null;
    $user_role = $_POST['user_role'] ?? null;

    if ($vendor_id && in_array($user_role, ['client', 'vendor'])) {
        $stmt = $pdo->prepare("UPDATE vendors SET user_role = :role WHERE vendor_id = :id");
        $stmt->execute([
            ':role' => $user_role,
            ':id' => $vendor_id
        ]);
        $_SESSION['message'] = "Role updated successfully.";
    } else {
        $_SESSION['message'] = "Invalid data provided.";
    }
}

header("Location: admin-clients.php");
exit;
