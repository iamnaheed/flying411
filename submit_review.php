<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "You must be logged in to submit a review."]);
        exit;
    }

    $client_id = $_SESSION['user_id']; // Changed from user_id to client_id
    $product_id = $_POST['product_id'] ?? null;
    $product_type = $_POST['product_type'] ?? null;
    $rating = $_POST['rating'] ?? 4; // Default rating is 4 if not set
    $review_text = trim($_POST['review_text'] ?? '');

    // Validate inputs
    if (empty($product_id) || empty($product_type) || empty($review_text) || $rating < 0 || $rating > 5) {
        echo json_encode(["status" => "error", "message" => "All fields are required and rating must be between 0 and 5."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO reviews (client_id, product_id, product_type, rating, review_text, created_at) 
                               VALUES (:client_id, :product_id, :product_type, :rating, :review_text, NOW())");
        $stmt->execute([
            ':client_id' => $client_id,
            ':product_id' => $product_id,
            ':product_type' => $product_type,
            ':rating' => $rating,
            ':review_text' => $review_text
        ]);

        echo json_encode(["status" => "success"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
