<?php
session_start();
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['aircraft_id'], $data['event'])) {
    exit;
}

$aircraftId = (int)$data['aircraft_id'];
$event      = $data['event'];

$allowed = ['call','email','website'];
if (!in_array($event, $allowed)) exit;

$stmt = $pdo->prepare("
  INSERT INTO aircraft_analytics
  (aircraft_id, event_type, session_id, user_ip)
  VALUES (:aid, :evt, :sid, :ip)
");
$stmt->execute([
  ':aid' => $aircraftId,
  ':evt' => $event,
  ':sid' => session_id(),
  ':ip'  => $_SERVER['REMOTE_ADDR']
]);
