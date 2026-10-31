<?php
session_start();
require_once "config.php";

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) exit;

$engine_id = (int)$data['engine_id'];
$type = $data['event_type'];

$allowed = ['call','email','website'];
if (!in_array($type, $allowed)) exit;

$stmt = $pdo->prepare("
INSERT INTO engine_analytics
(engine_id, event_type, user_ip, session_id)
VALUES (?, ?, ?, ?)
");
$stmt->execute([
    $engine_id,
    $type,
    $_SERVER['REMOTE_ADDR'],
    session_id()
]);
