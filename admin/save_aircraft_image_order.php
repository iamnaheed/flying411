<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

include 'config.php';

// read JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit;
}

$order = $data['order'] ?? null;
$aircraft_id = $data['aircraft_id'] ?? null;

if (!is_array($order) || empty($aircraft_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

try {
    $pdo->beginTransaction();

    $pos = 0;
    foreach ($order as $id) {
        // only update numeric (existing DB rows). Skip 'new-...' items.
        if (!is_numeric($id)) continue;

        $stmt = $pdo->prepare("
            UPDATE product_images
            SET sort_order = ?
            WHERE image_id = ? AND product_id = ? AND product_type = 'aircraft'
        ");
        $stmt->execute([$pos, $id, $aircraft_id]);
        $pos++;
    }

    // If there are DB images not included in the order (rare), ensure they get appended
    $stmt = $pdo->prepare("
        SELECT image_id FROM product_images 
        WHERE product_id = ? AND product_type = 'aircraft' 
        ORDER BY sort_order ASC, created_at ASC
    ");
    $stmt->execute([$aircraft_id]);
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Re-sequence from 0 to avoid gaps
    $i = 0;
    foreach ($existing as $imgId) {
        $upd = $pdo->prepare("UPDATE product_images SET sort_order = ? WHERE image_id = ?");
        $upd->execute([$i, $imgId]);
        $i++;
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Order saved']);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Save order error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
