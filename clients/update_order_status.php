<?php
include "config.php";

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    die("Order ID and status are required.");
}

$order_id = $_GET['id'];
$status = $_GET['status'];

// Update order status
$query = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
$query->execute([$status, $order_id]);

header("Location: orders-management.php");
exit;
?>
