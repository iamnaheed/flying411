<?php
session_start();
require_once "config.php";

// Check login
if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $vendor_id = $_SESSION['vendor_id'];

    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // -----------------------
    // Basic validation
    // -----------------------
    if (empty($current) || empty($new) || empty($confirm)) {
        $_SESSION['error'] = "All password fields are required";
        header("Location: vendor-profile.php");
        exit;
    }

    if ($new !== $confirm) {
        $_SESSION['error'] = "New password and confirm password do not match";
        header("Location: vendor-profile.php");
        exit;
    }

    if (strlen($new) < 8) {
        $_SESSION['error'] = "Password must be at least 8 characters long";
        header("Location: vendor-profile.php");
        exit;
    }

    // -----------------------
    // Get existing password
    // -----------------------
    $stmt = $pdo->prepare("SELECT password FROM vendors WHERE vendor_id = ?");
    $stmt->execute([$vendor_id]);
    $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vendor) {
        $_SESSION['error'] = "Vendor not found";
        header("Location: vendor-profile.php");
        exit;
    }

    // -----------------------
    // Verify current password
    // -----------------------
    if (!password_verify($current, $vendor['password'])) {
        $_SESSION['error'] = "Current password is incorrect";
        header("Location: vendor-profile.php");
        exit;
    }

    // -----------------------
    // Update password
    // -----------------------
    $newHash = password_hash($new, PASSWORD_DEFAULT);

    $update = $pdo->prepare(
        "UPDATE vendors SET password = ? WHERE vendor_id = ?"
    );
    $update->execute([$newHash, $vendor_id]);

    $_SESSION['success'] = "Password updated successfully";
    header("Location: vendor-profile.php");
    exit;
}
?>