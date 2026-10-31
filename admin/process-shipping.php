<?php
session_start();
require_once 'config.php';

if (!isset($_GET['action'], $_GET['id']) || !in_array($_GET['action'], ['shipped', 'delivered', 'cancelled'])) {
    header('Location: admin-shipping.php');
    exit();
}

$shipping_id = intval($_GET['id']);
$action = $_GET['action'];
$status = $action;
$date_column = ($action == 'delivered') ? 'delivered_date' : 'shipping_date';

// Update shipping status
$query = "UPDATE shipping SET shipping_status = :status, {$date_column} = NOW() WHERE shipping_id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['status' => $status, 'id' => $shipping_id]);

$_SESSION['message'] = "Shipping status has been updated to '{$status}'.";
header('Location: admin-shipping.php');
exit();
