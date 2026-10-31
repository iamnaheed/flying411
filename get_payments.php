<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $query = "
        SELECT p.payment_id, p.payment_method, p.payment_status, p.payment_amount, p.transaction_id, p.payment_date,
               p.escrow_release_date, p.is_new_vendor
        FROM payments p
        JOIN orders o ON p.order_id = o.order_id
        WHERE o.client_id = :client_id
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':client_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($payments) {
        echo json_encode($payments);
    } else {
        echo json_encode(["error" => "No payments found."]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Error fetching payments: " . $e->getMessage()]);
}
?>
