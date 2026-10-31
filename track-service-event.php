<?php
session_start();
require_once "config.php";

$data = json_decode(file_get_contents("php://input"),true);
if(!isset($data['event'],$data['service_id'])) exit;

$stmt = $pdo->prepare("
  INSERT INTO service_analytics
  (service_id,event_type,user_ip,session_id)
  VALUES (:sid,:type,:ip,:sess)
");
$stmt->execute([
  ':sid'=>(int)$data['service_id'],
  ':type'=>$data['event'],
  ':ip'=>$_SERVER['REMOTE_ADDR'],
  ':sess'=>session_id()
]);
