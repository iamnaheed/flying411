<?php
require_once 'config.php'; // adjust as needed

header('Content-Type: application/json');

if (empty($_GET['query'])) {
    echo json_encode([]);
    exit;
}

$query = trim($_GET['query']);
$results = [];
$seen = [];

try {
    // Case-insensitive safe search (uses COLLATE to ensure it works even if collation is case-sensitive)
    $sql = "
        SELECT part_id, part_name, part_number
        FROM parts
        WHERE (
            part_name LIKE :query COLLATE utf8mb4_general_ci
            OR part_number LIKE :query COLLATE utf8mb4_general_ci
        )
        AND status = 'approved'
        AND deleted_at IS NULL
        ORDER BY part_name ASC
        LIMIT 50
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':query' => "%$query%"]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $key = strtolower($row['part_name']) . '_' . strtolower($row['part_number']);
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $results[] = [
                'part_id' => $row['part_id'],
                'name'    => ucwords($row['part_name']) . " ({$row['part_number']})",
                'url'     => "https://flying411.com/parts/inventory/" . $row['part_id']
            ];
        }
        if (count($results) >= 50) break;
    }

    echo json_encode($results);
} catch (Exception $e) {
    // Optional: log error in development
    echo json_encode(['error' => $e->getMessage()]);
}
?>
