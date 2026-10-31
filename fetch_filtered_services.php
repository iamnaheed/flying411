<?php
require 'config.php'; // Include your PDO database connection

if (isset($_GET['service_type'])) {
    $service_type = trim($_GET['service_type']);

    try {
        // Fetch services that match the selected service type, including vendor details
        $stmt = $pdo->prepare("
            SELECT 
                s.id, 
                s.vendor_id, 
                s.service_type, 
                s.instruction_rate, 
                s.ground_rate, 
                s.hourly_rate, 
                s.aircraft_available, 
                s.shop_type, 
                s.mechanic_ratings, 
                s.instruction_offered, 
                s.ratings, 
                s.phone_number, 
                s.email, 
                s.website, 
                s.country, 
                s.photos, 
                s.business_name,
                s.school_type,
                s.created_at,
                v.business_name, 
                v.busniess_name,
                v.business_phone, 
                v.business_email, 
                v.contact_name, 
                v.contact_phone, 
                v.profile_picture
            FROM services s
            LEFT JOIN vendors v ON s.vendor_id = v.vendor_id
            WHERE s.service_type = :service_type
            ORDER BY s.service_type ASC
        ");

        $stmt->bindParam(':service_type', $service_type, PDO::PARAM_STR);
        $stmt->execute();

        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($services);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
}
?>

