<?php
require_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1); // IMPORTANT for JSON APIs
header('Content-Type: application/json; charset=utf-8');


$query = trim($_GET['q'] ?? '');
if (!$query) {
    echo json_encode([]);
    exit;
}

$results = [];
$seen = [];

$normalizedQuery = strtolower(str_replace(['-', '_', '/', ' '], '', $query));

$sql = "
    SELECT model, manufacturer, aircraft_type, type_designer
    FROM aircrafts
    WHERE status='approved' AND deleted_at IS NULL
      AND (
          REPLACE(REPLACE(REPLACE(REPLACE(LOWER(model), '-', ''), '_', ''), '/', ''), ' ', '') LIKE :q
          OR REPLACE(REPLACE(REPLACE(REPLACE(LOWER(type_designer), '-', ''), '_', ''), '/', ''), ' ', '') LIKE :q
          OR LOWER(manufacturer) LIKE :q
          OR LOWER(aircraft_type) LIKE :q
      )
    ORDER BY manufacturer ASC, model ASC
    LIMIT 50
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':q' => "%$normalizedQuery%"]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $model        = trim($row['model']);
    $manufacturer = trim($row['manufacturer']);
    $typeDesigner = trim($row['type_designer']);
    $aircraftType = trim($row['aircraft_type']);

    $displayName = $manufacturer . ' ' . $model;

    if (!isset($seen['model_'.$displayName])) {
        $seen['model_'.$displayName] = true;

        $normalizedDesigner = strtolower(str_replace(['-', '_', '/', ' '], '', $typeDesigner));

        if (!empty($typeDesigner) && strpos($normalizedDesigner, $normalizedQuery) !== false) {
            $numericPart = preg_replace('/\D/', '', $typeDesigner); // C-172 -> 172
            $url = "/aircraft?model=" . urlencode($numericPart);
        } elseif (!empty($aircraftType) && strtolower($aircraftType) === strtolower($query)) {
            $url = "/aircraft?category=" . urlencode($aircraftType);
        } else {
            $url = "/aircraft?model=" . urlencode($model);
        }

        $results[] = [
            'name'           => ucwords($displayName),
            'url'            => $url,
            'type'           => 'aircraft',
            'manufacturer'   => $manufacturer,
            'type_designer'  => $typeDesigner,
            'aircraft_type'  => $aircraftType
        ];
    }

    if (count($results) >= 50) break;
}

// ========================
// Engines suggestions
// ========================
$stmt = $pdo->prepare("SELECT engine_id, model FROM engines WHERE LOWER(model) LIKE ? LIMIT 50");
$stmt->execute(["%$query%"]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $model = strtolower($row['model']);
    if (!isset($seen['engine_' . $model])) {
        $seen['engine_' . $model] = true;
        $results[] = [
            'name' => ucwords($row['model']),
            'url'  => "/engine?model=" . urlencode($row['model']),
            'type' => 'engine'
        ];
    }
    if (count($results) >= 50) break;
}

// ========================
// Parts suggestions (MASTER + VENDOR LOGIC)
// ========================
try {
    $search = "%" . strtolower($query) . "%";

    // =============================
    // 1️⃣ Search in 'parts' table first
    // =============================
    $stmt = $pdo->prepare("
        SELECT part_name, part_number, part_id AS vendor_part_id
        FROM parts
        WHERE deleted_at IS NULL
          AND status = 'approved'
          AND (LOWER(part_name) LIKE ? OR LOWER(part_number) LIKE ?)
        LIMIT 50
    ");
    $stmt->execute([$search, $search]);

    $foundParts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If parts found in 'parts' table
    if (!empty($foundParts)) {
        foreach ($foundParts as $row) {
            $key = strtolower($row['part_name']) . '_' . strtolower($row['part_number']);
            if (isset($seen['part_' . $key])) continue;
            $seen['part_' . $key] = true;

            $url  = "/parts?name=" . urlencode($row['part_name']);
            $type = 'part';

            $results[] = [
                'name' => ucwords($row['part_name']) . " ({$row['part_number']})",
                'url'  => $url,
                'type' => $type
            ];

            if (count($results) >= 50) break;
        }
    } else {
        // =============================
        // 2️⃣ If not found, search in 'parts_master'
        // =============================
        $stmt = $pdo->prepare("
            SELECT pm.part_name, pm.part_number, NULL AS vendor_part_id
            FROM parts_master pm
            WHERE pm.status = 'active'
              AND pm.part_name IS NOT NULL
              AND pm.part_number IS NOT NULL
              AND (LOWER(pm.part_name) LIKE ? OR LOWER(pm.part_number) LIKE ?)
            LIMIT 50
        ");
        $stmt->execute([$search, $search]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $key = strtolower($row['part_name']) . '_' . strtolower($row['part_number']);
            if (isset($seen['part_' . $key])) continue;
            $seen['part_' . $key] = true;

            // Master-only → informational page
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $row['part_name']), '-'));
            $url  = "/parts/" . urlencode($row['part_number']) . "/" . $slug;
            $type = 'part-info';

            $results[] = [
                'name' => ucwords($row['part_name']) . " ({$row['part_number']})",
                'url'  => $url,
                'type' => $type
            ];

            if (count($results) >= 50) break;
        }
    }

} catch (PDOException $e) {
    error_log("Parts search error: " . $e->getMessage());
    echo json_encode($results);
    exit;
}



// ========================
// Output Final Result
// ========================
header('Content-Type: application/json');
echo json_encode($results);
