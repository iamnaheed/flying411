<?php
session_start();
require_once 'config.php';

if (!isset($_GET['action'], $_GET['id']) || !in_array($_GET['action'], ['approve', 'deny'])) {
    header('Location: admin-withdrawals.php');
    exit();
}

$withdrawal_id = intval($_GET['id']);
$action = $_GET['action'];
$status = ($action == 'approve') ? 'approved' : 'denied';

// Update withdrawal status
$query = "UPDATE vendor_withdrawals SET status = :status, approval_date = NOW() WHERE withdrawal_id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['status' => $status, 'id' => $withdrawal_id]);

$_SESSION['message'] = "Withdrawal request has been {$status}.";
header('Location: admin-withdrawals.php');
exit();
