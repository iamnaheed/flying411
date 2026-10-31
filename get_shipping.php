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
    // Prepare the query to fetch shipping details for the logged-in client
    $query = "
        SELECT s.shipping_id, c.address AS shipping_address, 
               s.shipping_status, s.tracking_number, s.shipping_date, s.delivered_date, s.shipping_company
        FROM shipping s
        JOIN orders o ON s.order_id = o.order_id
        JOIN clients c ON o.client_id = c.client_id
        WHERE o.client_id = :client_id
    ";

    // Prepare and execute the query
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':client_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Fetch the shipping details
    $shipping = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if shipping data is found
    if ($shipping) {
        echo json_encode($shipping); // Return shipping details as JSON
    } else {
        echo json_encode(["error" => "No shipping data found."]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Error fetching shipping data: " . $e->getMessage()]);
}
?>
