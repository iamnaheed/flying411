<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_id = isset($_POST['vendor_id']) ? intval($_POST['vendor_id']) : 0;
    $user_role = isset($_POST['user_role']) ? trim($_POST['user_role']) : '';

    if ($vendor_id > 0 && in_array($user_role, ['client', 'vendor'])) {
        try {
            $stmt = $pdo->prepare("UPDATE vendors SET user_role = :role WHERE vendor_id = :id");
            $stmt->execute([
                ':role' => $user_role,
                ':id'   => $vendor_id
            ]);
            $_SESSION['message'] = "Role updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Database error: " . $e->getMessage();
        }
    } else {
        $_SESSION['message'] = "Invalid input data.";
    }
} else {
    $_SESSION['message'] = "Invalid request method.";
}

header("Location: admin-clients.php");
exit;
?>
