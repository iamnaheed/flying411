<?php
session_start();
include "db.php"; // connect to DB

$username = $_POST['username'];
$password = $_POST['password'];
$action = $_POST['action']; // 'login' or 'register'

if ($action == 'login') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid login credentials"]);
    }

} elseif ($action == 'register') {
    // Check if user exists
    $check = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
    $check->execute([$username, $username]);
    if ($check->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "User already exists"]);
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $insert->execute([$username, $hashed]);
        $_SESSION['user_id'] = $conn->lastInsertId();
        echo json_encode(["status" => "success"]);
    }
}
?>
