<?php
session_start();
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['part_id'], $data['event'])) exit;

$allowed = ['call','email','website'];
if (!in_array($data['event'], $allowed)) exit;

$stmt = $pdo->prepare("
  INSERT INTO parts_analytics
  (part_id, event_type, session_id, user_ip)
  VALUES (:pid, :evt, :sid, :ip)
");

$stmt->execute([
  ':pid' => (int)$data['part_id'],
  ':evt' => $data['event'],
  ':sid' => session_id(),
  ':ip'  => $_SERVER['REMOTE_ADDR']
]);
