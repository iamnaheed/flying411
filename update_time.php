<?php
require_once 'config.php';

// Debug log (remove after testing)
file_put_contents(
    __DIR__ . "/time_debug.log",
    date("Y-m-d H:i:s") . " CALLED\n",
    FILE_APPEND
);

$data = json_decode(file_get_contents("php://input"), true);
file_put_contents(__DIR__ . "/time_debug.log", print_r($data, true), FILE_APPEND);

if (!isset($data['time'], $data['token'])) {
    exit;
}

$time  = (int)$data['time'];
$token = $data['token'];

if ($time <= 0) exit;

$stmt = $pdo->prepare("
    UPDATE page_analytics
    SET time_spent = time_spent + :t
    WHERE page_token = :pt
    LIMIT 1
");
$stmt->execute([
    ':t'  => $time,
    ':pt' => $token
]);
