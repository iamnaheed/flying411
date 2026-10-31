<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "config.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type: application/json"); // Ensure JSON response

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

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
        $stmt = $pdo->prepare("SELECT admin_id, name, password, status FROM admins WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($password, $admin['password'])) {
            echo json_encode(["success" => false, "message" => "Invalid credentials."]);
            exit;
        }

        if ($admin['status'] !== 'active') {
            echo json_encode(["success" => false, "message" => "Your account is inactive. Contact support."]);
            exit;
        }

        // Secure the session
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = htmlspecialchars($admin['name']);

        echo json_encode(["success" => true, "message" => "Login successful."]);
        exit;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage(), 3, "errors.log");
        echo json_encode(["success" => false, "message" => "Database error. Try again later."]);
        exit;
    }
}
?>
