<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

include 'config.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$id = $data['id'] ?? null;
$aircraft_id = $data['aircraft_id'] ?? null;

if (!is_numeric($id) || empty($aircraft_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    // fetch image path
    $stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE image_id = ? AND product_id = ? AND product_type = 'aircraft'");
    $stmt->execute([$id, $aircraft_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Image not found']);
        exit;
    }

    $image_url = $row['image_url'];

    // try delete physical file from vendors/ or clients/
    $deletedFile = false;
    $candidates = [
        __DIR__ . "/../vendors/" . $image_url,
        __DIR__ . "/../clients/" . $image_url,
        __DIR__ . "/" . $image_url, // fallback
    ];

    foreach ($candidates as $path) {
        if (file_exists($path) && is_file($path)) {
            @unlink($path);
            $deletedFile = true;
            break;
        }
    }

    // delete DB row
    $del = $pdo->prepare("DELETE FROM product_images WHERE image_id = ? AND product_id = ? AND product_type = 'aircraft'");
    $del->execute([$id, $aircraft_id]);

    // resequence remaining images
    $pdo->beginTransaction();
    $sel = $pdo->prepare("SELECT image_id FROM product_images WHERE product_id = ? AND product_type = 'aircraft' ORDER BY sort_order ASC, created_at ASC");
    $sel->execute([$aircraft_id]);
    $i = 0;
    while ($r = $sel->fetch(PDO::FETCH_ASSOC)) {
        $upd = $pdo->prepare("UPDATE product_images SET sort_order = ? WHERE image_id = ?");
        $upd->execute([$i, $r['image_id']]);
        $i++;
    }
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Image deleted']);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log("Delete image error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
