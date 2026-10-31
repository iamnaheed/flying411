<?php
session_start();
require_once 'config.php';

$page_url  = $_SERVER['REQUEST_URI'];
$user_ip   = $_SERVER['REMOTE_ADDR'];
$sessionId = session_id();
$pageToken = uniqid('pg_', true);

/* Prevent duplicate refresh within 5 seconds */
$stmt = $pdo->prepare("
    SELECT created_at 
    FROM page_analytics
    WHERE session_id = :sid AND page_url = :url
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([
    ':sid' => $sessionId,
    ':url' => $page_url
]);

$insert = true;
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (time() - strtotime($row['created_at']) < 5) {
        $insert = false;
    }
}

if ($insert) {
    $stmt = $pdo->prepare("
        INSERT INTO page_analytics 
        (page_url, user_ip, session_id, page_token, time_spent)
        VALUES (:url, :ip, :sid, :pt, 0)
    ");
    $stmt->execute([
        ':url' => $page_url,
        ':ip'  => $user_ip,
        ':sid' => $sessionId,
        ':pt'  => $pageToken
    ]);
}

/* 🔥 IMPORTANT: expose token to JS */
echo "<script>window.PAGE_TOKEN = '{$pageToken}';</script>";
