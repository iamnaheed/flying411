<?php
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($login) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Email/Username and password are required."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT vendor_id, contact_name, password, user_role
            FROM vendors
            WHERE LOWER(contact_email) = LOWER(:login)
               OR LOWER(contact_name) = LOWER(:login)
            LIMIT 1
        ");

        $stmt->execute([':login' => $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['vendor_id'] = $user['vendor_id'];
            $_SESSION['user_role'] = $user['user_role'];
            $_SESSION['username']  = $user['contact_name'];

            echo json_encode([
                "success" => true,
                "role" => $user['user_role']
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Invalid email/username or password."
            ]);
        }

    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Server error."
        ]);
    }
    exit;
}

?>