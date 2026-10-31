<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "config.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type: application/json"); // Ensure JSON response

    // Read JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Please enter email and password."]);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email format."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT vendor_id, business_name, password, status FROM vendors WHERE business_email = :email");
        $stmt->execute(['email' => $email]);
        $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vendor || !password_verify($password, $vendor['password'])) {
            echo json_encode(["success" => false, "message" => "Invalid credentials."]);
            exit;
        }

        if ($vendor['status'] !== 'active') {
            echo json_encode(["success" => false, "message" => "Your account is not approved yet. Please wait for admin approval."]);
            exit;
        }

        // Secure the session
        session_regenerate_id(true);
        $_SESSION['vendor_id'] = $vendor['vendor_id'];
        $_SESSION['business_name'] = htmlspecialchars($vendor['business_name']);

        echo json_encode(["success" => true, "message" => "Login successful."]);
        exit;
    } catch (PDOException $e) {
        // Log the error instead of exposing it
        error_log("Database error: " . $e->getMessage(), 3, "errors.log");
        echo json_encode(["success" => false, "message" => "Database error. Please try again later."]);
        exit;
    }
}
