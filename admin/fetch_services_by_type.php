<?php
session_start();
include "config.php"; // PDO connection

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo '<option value="">Access denied</option>';
    exit;
}

// Get POST data
$service_type = isset($_POST['service_type']) ? trim($_POST['service_type']) : '';
$search = isset($_POST['search']) ? trim($_POST['search']) : '';

// Prepare query
$sql = "SELECT id, business_name 
        FROM services 
        WHERE LOWER(service_type) = LOWER(:service_type)
          AND vendor_id IS NULL"; // only show services without vendor
$params = [':service_type' => $service_type];

// Add search condition if search term exists
if ($search !== '') {
    $sql .= " AND business_name LIKE :search";
    $params[':search'] = "%$search%";
}

$sql .= " ORDER BY business_name ASC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($services) {
        echo '<option value="">Select service</option>';
        foreach ($services as $s) {
            echo '<option value="'.htmlspecialchars($s['id']).'">'.htmlspecialchars($s['business_name']).'</option>';
        }
    } else {
        echo '<option value="">No unassigned services found</option>';
    }

} catch (PDOException $e) {
    echo '<option value="">Error loading services</option>';
}
