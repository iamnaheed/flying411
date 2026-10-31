<?php
require 'config.php'; // Include your PDO connection file

$serviceType = isset($_GET['service_type']) ? trim($_GET['service_type']) : '';

try {
    if ($serviceType !== '') {
        $stmt = $pdo->prepare("SELECT id, vendor_id, service_type, instruction_rate, ground_rate, hourly_rate, 
            aircraft_available, shop_type, mechanic_ratings, instruction_offered, ratings, school_type,
            phone_number, email, website, country, photos ,business_name
            FROM services 
            WHERE service_type = :type 
            ORDER BY service_type ASC");
        $stmt->bindParam(':type', $serviceType, PDO::PARAM_STR);
    } else {
        // If no filter selected, fetch all
        $stmt = $pdo->prepare("SELECT id, vendor_id, service_type, instruction_rate, ground_rate, hourly_rate, 
            aircraft_available, shop_type, mechanic_ratings, instruction_offered, ratings, 
            phone_number, email, website, country, photos ,business_name,school_type,
            FROM services 
            ORDER BY service_type ASC");
    }

    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($services);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
