<?php
require 'config.php';

try {
$stmt = $pdo->prepare("
    SELECT 
        s.id, 
        s.service_type,
        s.instruction_rate,
        s.ground_rate,
        s.hourly_rate,
        s.photos, 
        s.ratings, 
        s.business_name, 
        s.school_type,
        s.email AS service_email,
        s.country, 
        s.website, 
        s.phone_number AS service_phone,
        v.business_email AS vendor_email,
        v.business_name AS vendor_name
    FROM services s
    LEFT JOIN vendors v ON s.vendor_id = v.vendor_id
");

$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);


    foreach ($services as &$service) {
        $photos = json_decode($service['photos'], true); // Decode JSON
        $service['photo'] = (!empty($photos) && is_array($photos)) ? $photos[0] : 'default-service.jpg'; // Get first image or use default
    }

    echo json_encode($services);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
