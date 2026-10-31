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
    // Prepare the query to fetch order details for the logged-in client
    $query = "
        SELECT o.order_id, oi.product_id, oi.quantity, oi.price, o.total_amount AS total, o.status, o.created_at AS date, 
               CASE 
                   WHEN oi.product_type = 'aircraft' THEN a.model
                   WHEN oi.product_type = 'part' THEN p.part_number
                   WHEN oi.product_type = 'engine' THEN e.model
                   ELSE 'Unknown Product'
               END AS product_name
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        LEFT JOIN aircrafts a ON oi.product_type = 'aircraft' AND oi.product_id = a.aircraft_id
        LEFT JOIN parts p ON oi.product_type = 'part' AND oi.product_id = p.part_id
        LEFT JOIN engines e ON oi.product_type = 'engine' AND oi.product_id = e.engine_id
        WHERE o.client_id = :client_id
    ";

    // Prepare the query and bind the client_id parameter
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':client_id', $user_id, PDO::PARAM_INT);
    
    // Execute the query
    if ($stmt->execute()) {
        // Fetch the orders
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if orders are found
        if ($orders) {
            echo json_encode($orders); // Return orders details as JSON
        } else {
            echo json_encode(["error" => "No orders found."]);
        }
    } else {
        echo json_encode(["error" => "Failed to execute query."]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Error fetching orders: " . $e->getMessage()]);
}
?>
