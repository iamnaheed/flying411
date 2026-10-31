<?php
session_start();
include "config.php"; // Your DB connection file

if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = "pending";
    $expires_at = date('Y-m-d H:i:s', strtotime('+8 weeks')); // <-- 8 weeks from now

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO aircrafts 
        (vendor_id, aircraft_type, manufacturer, model, registration_number, serial_number, year, price,currency,
         city, state, country, location, description, features, warranty, 
         engine1_status, engine1_hours,
         prop1_status, prop1_hours,
         engine2_status, engine2_hours,
         prop2_status, prop2_hours,
         show_seller_name, show_call_button, show_email_button, status, total_time_hours,enginestatus,enginehours, price_label, created_at, expires_at)
        VALUES
        (:vendor_id, :aircraft_type, :manufacturer, :model, :registration_number, :serial_number, :year, :price,:currency,
         :city, :state, :country, :location, :description, :features, :warranty,
         :engine1_status, :engine1_hours,
         :prop1_status, :prop1_hours,
         :engine2_status, :engine2_hours,
         :prop2_status, :prop2_hours,
         :show_seller_name, :show_call_button, :show_email_button, :status, :total_time_hours,:enginestatus,:enginehours,:price_label,  NOW(), :expires_at)");

        $stmt->execute([
            ':vendor_id' => $vendor_id,
            ':aircraft_type' => $_POST['aircraft_type'],
            ':manufacturer' => $_POST['manufacturer'],
            ':model' => $_POST['model'],
            ':registration_number' => $_POST['registration_number'],
            ':serial_number' => $_POST['serial_number'],
            ':year' => $_POST['year'],
            ':price' => $_POST['price'],
            ':currency' => $_POST['currency'],
            ':city' => $_POST['city'],
            ':state' => $_POST['state'],
            ':country' => $_POST['country'],
            ':location' => $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['country'],
            ':description' => $_POST['description'],
            ':features' => $_POST['features'],
            ':warranty' => $_POST['warranty'],
            ':engine1_status' => $_POST['engine1_status'] ?? null,
            ':engine1_hours' => $_POST['engine1_hours'] ?? null,
           
            ':prop1_status' => $_POST['prop1_status'] ?? null,
            ':prop1_hours' => $_POST['prop1_hours'] ?? null,
             ':engine2_status' => $_POST['engine2_status'] ?? null,
             ':engine2_hours' => $_POST['engine2_hours'] ?? null,
               ':prop2_status' => $_POST['prop2_status'] ?? null,
        ':prop2_hours' => $_POST['prop2_hours'] ?? null,
            ':show_seller_name' => isset($_POST['show_seller_name']) ? 1 : 0,
            ':show_call_button' => isset($_POST['show_call_button']) ? 1 : 0,
            ':show_email_button' => isset($_POST['show_email_button']) ? 1 : 0,
            ':status' => $status,
            ':total_time_hours' => $_POST['total_time_hours'],
             ':enginestatus' => $_POST['enginestatus'] ?? null,
            ':enginehours' => $_POST['enginehours'] ?? null,
            ':price_label' => $_POST['price_label'] ?? null,
            ':expires_at' => $expires_at // <-- Add this line
        ]);

    
      $product_id = $pdo->lastInsertId(); // Get last inserted aircraft_id
        
        // Handle multiple image uploads
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = "uploads/";
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $fileName = basename($_FILES['images']['name'][$key]);
                $filePath = $uploadDir . time() . "_" . $fileName;
                
                if (move_uploaded_file($tmp_name, $filePath)) {
                    $stmt = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url, created_at) VALUES (:product_id, 'aircraft', :image_path, NOW())");
                    $stmt->execute([
                        ':product_id' => $product_id,
                        ':image_path' => $filePath
                    ]);
                }
            }
        }

        // Handle multiple document uploads
        if (!empty($_FILES['documents']['name'][0])) {
            $uploadDir = "uploads/documents/";
            foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
                $fileName = basename($_FILES['documents']['name'][$key]);
                $filePath = $uploadDir . time() . "_" . $fileName;
                
                if (move_uploaded_file($tmp_name, $filePath)) {
                    $stmt = $pdo->prepare("INSERT INTO product_aircraft_documents (product_id, document_url, created_at) VALUES (:product_id, :document_path, NOW())");
                    $stmt->execute([
                        ':product_id' => $product_id,
                        ':document_path' => $filePath
                    ]);
                }
            }
        }

        $pdo->commit();
        echo "<script>alert('Aircraft added successfully!'); window.location.href='manage_products.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
}
}
?>