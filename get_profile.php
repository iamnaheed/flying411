<?php
// Start the session to get the user_id from the session
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
require 'db/connection.php';  // Include the connection file

header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in."]);
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

try {
    // Prepare the query to fetch profile details
    $query = "SELECT name, email, phone, address, created_at FROM clients WHERE client_id = :user_id";
    $stmt = $pdo->prepare($query);
    
    // Bind the user_id parameter to the query
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Check if the user exists and return the profile data
    if ($stmt->rowCount() > 0) {
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($profile);  // Return profile as JSON
    } else {
        echo json_encode(["error" => "User not found."]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Error fetching profile: " . $e->getMessage()]);
}
?>
