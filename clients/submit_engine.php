<?php
session_start();
include "config.php"; // Database connection

if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['bulk_upload']) && $_FILES['bulk_upload']['error'] == 0) {
        // Process CSV File Upload
        $csvFile = $_FILES['bulk_upload']['tmp_name'];
        if (($handle = fopen($csvFile, 'r')) !== FALSE) {
            fgetcsv($handle); // Skip header row
            
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                try {
                    $pdo->beginTransaction();

                    // Insert engine data
                    $stmt = $pdo->prepare("INSERT INTO engines 
                        (vendor_id, model, manufacturer, location, engine_type, power_thrust, year, total_time_hours, hr, cycles, `condition`, price,warranty, extra_details,  tags, status, created_at) 
                        VALUES (:vendor_id, :model, :manufacturer, :location, :engine_type, :power_thrust, :year, :total_time_hours, :hr, :cycles, :condition, :price, :extra_details, :warranty, :tags, :status, NOW())");

                    $stmt->execute([
                        ':vendor_id' => $vendor_id,
                        ':model' => $data[0], 
                        ':manufacturer' => $data[1],
                        ':location' => $data[2],
                        ':engine_type' => $data[3],
                        ':power_thrust' => $data[4],
                        ':year' => $data[5],
                        ':total_time_hours' => $data[6],
                        ':hr' => $data[7],
                        ':cycles' => $data[8],
                        ':condition' => $data[9],
                        ':price' => $data[10],
                        ':extra_details' => $data[11],
                        ':warranty' => $data[12],
                        ':tags' => $data[13],
                        ':status' => "pending"
                    ]);

                    $product_id = $pdo->lastInsertId();

                    // Handle Image URLs (CSV Column 14)
                    if (!empty($data[14])) {
                        $imageUrls = explode(",", $data[14]);
                        foreach ($imageUrls as $imageUrl) {
                            $stmt = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url, created_at) 
                                VALUES (:product_id, 'engine', :image_path, NOW())");
                            $stmt->execute([
                                ':product_id' => $product_id,
                                ':image_path' => 'uploads/' . trim($imageUrl)
                            ]);
                        }
                    }

                    // Handle Document URLs (CSV Column 15)
                    if (!empty($data[15])) {
                        $documentUrls = explode(",", $data[15]);
                        foreach ($documentUrls as $documentUrl) {
                            $stmt = $pdo->prepare("INSERT INTO product_documents (product_id, product_type, document_url, created_at) 
                                VALUES (:product_id, 'engine', :document_path, NOW())");
                            $stmt->execute([
                                ':product_id' => $product_id,
                                ':document_path' => 'uploads/documents/' . trim($documentUrl)
                            ]);
                        }
                    }

                    $pdo->commit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                    die("Error: " . $e->getMessage());
                }
            }
            fclose($handle);
        }
        echo "<script>alert('CSV data uploaded successfully!'); window.location.href='manage_products.php';</script>";
    } else {
        // Process manual form submission
        try {
            $pdo->beginTransaction();

            // Insert into engines table
            $stmt = $pdo->prepare("INSERT INTO engines 
                    (vendor_id, model, manufacturer, location, engine_type, power_thrust, year, total_time_hours, hr, cycles, `condition`, price, extra_details, warranty, tags, status, created_at) 
                    VALUES (:vendor_id, :model, :manufacturer, :location, :engine_type, :power_thrust, :year, :total_time_hours, :hr, :cycles, :condition, :price, :extra_details, :warranty, :tags, :status, NOW())");

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
                ':tags' => $_POST['tags'],
                ':status' => "pending"
            ]);

            $product_id = $pdo->lastInsertId();

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                $uploadDir = "uploads/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    $fileName = time() . "_" . basename($_FILES['images']['name'][$key]);
                    $filePath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmp_name, $filePath)) {
                        $stmt = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url, created_at) VALUES (:product_id, 'engine', :image_path, NOW())");
                        $stmt->execute([
                            ':product_id' => $product_id,
                            ':image_path' => $filePath
                        ]);
                    }
                }
            }

            // Handle document uploads
            if (!empty($_FILES['documents']['name'][0])) {
                $documentDir = "uploads/documents/";
                if (!is_dir($documentDir)) mkdir($documentDir, 0777, true);

                foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
                    $docName = time() . "_" . basename($_FILES['documents']['name'][$key]);
                    $docPath = $documentDir . $docName;

                    if (move_uploaded_file($tmp_name, $docPath)) {
                        $stmt = $pdo->prepare("INSERT INTO product_documents (product_id, product_type, document_url, created_at) VALUES (:product_id, 'engine', :document_path, NOW())");
                        $stmt->execute([
                            ':product_id' => $product_id,
                            ':document_path' => $docPath
                        ]);
                    }
                }
            }

            $pdo->commit();
            echo "<script>alert('Engine added successfully!'); window.location.href='manage_products.php';</script>";

        } catch (Exception $e) {
            $pdo->rollBack();
            die("Error: " . $e->getMessage());
        }
    }
} else {
    die("Invalid request.");
}
?>
