<?php
session_start();
require 'config.php'; // Database connection

// Check vendor authentication
if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $vendor_id = $_SESSION['vendor_id'];
        $service_type = trim($_POST['service_type'] ?? '');
        $instruction_rate = $_POST['instruction_rate'] ?? null;
        $ground_rate = $_POST['ground_rate'] ?? null;
        $hourly_rate = $_POST['hourly_rate'] ?? null;
        $aircraft_available = trim($_POST['aircraft_available'] ?? '');
        $shop_type = isset($_POST['shop_type']) ? json_encode($_POST['shop_type']) : null;
        $mechanic_ratings = isset($_POST['mechanic_ratings']) ? json_encode($_POST['mechanic_ratings']) : null;
        $instruction_offered = isset($_POST['instruction_offered']) ? json_encode($_POST['instruction_offered']) : null;
        $ratings = isset($_POST['ratings']) ? json_encode($_POST['ratings']) : null;
        $phone_number = trim($_POST['phone_number'] ?? '');
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : null;
        $website = filter_var($_POST['website'], FILTER_VALIDATE_URL) ? $_POST['website'] : null;
        $country = trim($_POST['country'] ?? '');

        // Validate required fields
        if (!$service_type || !$phone_number || !$country) {
            $_SESSION['error'] = "Service Type, Phone Number, and Country are required.";
            header("Location: add_service.php");
            exit;
        }

        // Handle photo uploads
        $photos = [];
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $uploads_dir = 'uploads/';

        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        if (!empty($_FILES['photos']['name'][0])) {
            foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['photos']['error'][$key] == UPLOAD_ERR_OK) {
                    $file_extension = strtolower(pathinfo($_FILES['photos']['name'][$key], PATHINFO_EXTENSION));

                    if (in_array($file_extension, $allowed_types)) {
                        $file_name = uniqid() . '_' . basename($_FILES['photos']['name'][$key]);
                        $target_path = $uploads_dir . $file_name;

                        if (move_uploaded_file($tmp_name, $target_path)) {
                            $photos[] = $file_name;
                        } else {
                            $_SESSION['error'] = "Failed to upload some files.";
                            header("Location: add_service.php");
                            exit;
                        }
                    } else {
                        $_SESSION['error'] = "Invalid file type. Only JPG, PNG, and GIF allowed.";
                        header("Location: add_service.php");
                        exit;
                    }
                }
            }
        }

        $photos_json = !empty($photos) ? json_encode($photos) : null;

        // Insert data into the database
        $stmt = $pdo->prepare("INSERT INTO services (vendor_id, service_type, instruction_rate, ground_rate, hourly_rate, 
                aircraft_available, shop_type, mechanic_ratings, instruction_offered, ratings, phone_number, email, 
                website, country, photos) 
                VALUES (:vendor_id, :service_type, :instruction_rate, :ground_rate, :hourly_rate, :aircraft_available, 
                :shop_type, :mechanic_ratings, :instruction_offered, :ratings, :phone_number, :email, :website, 
                :country, :photos)");

        $stmt->execute([
            ':vendor_id' => $vendor_id,
            ':service_type' => $service_type,
            ':instruction_rate' => $instruction_rate,
            ':ground_rate' => $ground_rate,
            ':hourly_rate' => $hourly_rate,
            ':aircraft_available' => $aircraft_available,
            ':shop_type' => $shop_type,
            ':mechanic_ratings' => $mechanic_ratings,
            ':instruction_offered' => $instruction_offered,
            ':ratings' => $ratings,
            ':phone_number' => $phone_number,
            ':email' => $email,
            ':website' => $website,
            ':country' => $country,
            ':photos' => $photos_json
        ]);

        $_SESSION['success'] = "Service added successfully!";
        header("Location: list_of_services.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: add_service.php");
        exit;
    }
}

?>
