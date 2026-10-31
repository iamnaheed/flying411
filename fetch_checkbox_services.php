<?php
require 'config.php'; // Database connection

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$response = [];

$selectedServices = [];
$selectedSubservice = [];

// Collect services
if (isset($_POST['services']) && !empty($_POST['services'])) {
    $selectedServices = json_decode($_POST['services'], true);
}

// Collect subservice for Flight School
if (isset($_POST['subservice']) && !empty($_POST['subservice'])) {
    $subservice = $_POST['subservice']; // "141", "61", or "141,61"

    $mapping = [
        '141' => 'Part 141',
        '61'  => 'Part 61',
        '141,61' => ['Part 141', 'Part 61'],
    ];

    $selectedSubservice = $mapping[$subservice] ?? [];
}

// Build SQL dynamically
try {
    $conditions = [];
    $params = [];

    if (!empty($selectedServices)) {
        $placeholders = implode(',', array_fill(0, count($selectedServices), '?'));
        $conditions[] = "s.service_type IN ($placeholders)";
        $params = array_merge($params, $selectedServices);
    }

    if (!empty($selectedSubservice)) {
        if (is_array($selectedSubservice)) {
            $subPlaceholders = implode(',', array_fill(0, count($selectedSubservice), '?'));
            $conditions[] = "s.school_type IN ($subPlaceholders)";
            $params = array_merge($params, $selectedSubservice);
        } else {
            $conditions[] = "s.school_type = ?";
            $params[] = $selectedSubservice;
        }
    }

    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    $sql = "SELECT s.id, s.vendor_id, s.service_type, s.instruction_rate, s.ground_rate, s.hourly_rate,
            s.ratings, s.email, s.photos, s.school_type, s.business_name, s.website, s.country,
            v.business_phone, v.business_email, v.business_name AS vendor_name
            FROM services s
            LEFT JOIN vendors v ON s.vendor_id = v.vendor_id
            $whereClause
            ORDER BY s.service_type ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($services)) {
        echo json_encode(["error" => "No matching services found."]);
    } else {
        echo json_encode($services);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
