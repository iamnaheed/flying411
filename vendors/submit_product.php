<?php

session_start();
include "config.php"; // Database connection
if (!isset($_SESSION['vendor_id'])) {
   header("Location: ../login.php");// Redirect to login if not authenticated
    exit;
}
$vendor_id = $_SESSION['vendor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_type = $_POST['product_type'];
    $status = "pending"; // Default status

    try {
        $pdo->beginTransaction();

        if ($product_type === "aircraft") {
            // Insert into aircrafts table
            $stmt = $pdo->prepare("INSERT INTO aircrafts 
                (vendor_id, model, category, location, aircraft_type, manufacturer, condition, year, total_time_hours, engine_smh, price, description, features, warranty, status, created_at) 
                VALUES (:vendor_id, :model, :category, :location, :aircraft_type, :manufacturer, :condition, :year, :total_time_hours, :engine_smh, :price, :description, :features, :warranty, :status, NOW())");
            
            $stmt->execute([
                ':vendor_id' => $vendor_id,
                ':model' => $_POST['model'],
                ':category' => $_POST['category'],
                ':location' => $_POST['location'],
                ':aircraft_type' => $_POST['aircraft_type'],
                ':manufacturer' => $_POST['manufacturer'],
                ':condition' => $_POST['condition'],
                ':year' => $_POST['year'],
                ':total_time_hours' => $_POST['total_time_hours'],
                ':engine_smh' => $_POST['engine_smh'],
                ':price' => $_POST['price'],
                ':description' => $_POST['description'],
                ':features' => $_POST['features'],
                ':warranty' => $_POST['warranty'],
                ':status' => $status
            ]);

            $product_id = $pdo->lastInsertId(); // Get last inserted aircraft_id
        } elseif ($product_type === "engine") {
            // Insert into engines table
            $stmt = $pdo->prepare("INSERT INTO engines 
                (vendor_id, model, manufacturer, location, engine_type, power_thrust, year, total_time_hours, hr, cycles, condition, price, extra_details, warranty, status, created_at) 
                VALUES (:vendor_id, :model, :manufacturer, :location, :engine_type, :power_thrust, :year, :total_time_hours, :hr, :cycles, :condition, :price, :extra_details, :warranty, :status, NOW())");

            $stmt->execute([
                ':vendor_id' => $vendor_id,
                ':model' => $_POST['model'],
                ':manufacturer' => $_POST['manufacturer'],
                ':location' => $_POST['location'],
                ':engine_type' => $_POST['engine_type'],
                ':power_thrust' => $_POST['power_thrust'],
                ':year' => $_POST['year'],
                ':total_time_hours' => $_POST['total_time_hours'],
                ':hr' => $_POST['hr'],
                ':cycles' => $_POST['cycles'],
                ':condition' => $_POST['condition'],
                ':price' => $_POST['price'],
                ':extra_details' => $_POST['extra_details'],
                ':warranty' => $_POST['warranty'],
                ':status' => $status
            ]);

            $product_id = $pdo->lastInsertId(); // Get last inserted engine_id
        } elseif ($product_type === "part") {
            // Insert into parts table
            $stmt = $pdo->prepare("INSERT INTO parts 
                (vendor_id, part_number, type, condition, region, price, tagged_with_easa_form_1, extra_details, warranty, status, created_at) 
                VALUES (:vendor_id, :part_number, :type, :condition, :region, :price, :tagged_with_easa_form_1, :extra_details, :warranty, :status, NOW())");

            $stmt->execute([
                ':vendor_id' => $vendor_id,
                ':part_number' => $_POST['part_number'],
                ':type' => $_POST['type'],
                ':condition' => $_POST['condition'],
                ':region' => $_POST['region'],
                ':price' => $_POST['price'],
                ':tagged_with_easa_form_1' => $_POST['tagged_with_easa_form_1'],
                ':extra_details' => $_POST['extra_details'],
                ':warranty' => $_POST['warranty'],
                ':status' => $status
            ]);

            $product_id = $pdo->lastInsertId(); // Get last inserted part_id
        } else {
            die("Invalid product type selected.");
        }

        // Handle multiple image uploads
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = "uploads/";
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $fileName = basename($_FILES['images']['name'][$key]);
                $filePath = $uploadDir . time() . "_" . $fileName;

                if (move_uploaded_file($tmp_name, $filePath)) {
                    $stmt = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url, created_at) VALUES (:product_id, :product_type, :image_path, NOW())");
                    $stmt->execute([
                        ':product_id' => $product_id,
                        ':product_type' => $product_type,
                        ':image_path' => $filePath
                    ]);
                }
            }
        }

        $pdo->commit();
        echo "<script>alert('Product added successfully!'); window.location.href='manage_products.php';</script>";

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
} else {
    die("Invalid request.");
}
?>