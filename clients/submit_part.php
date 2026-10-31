<?php
session_start();
include "config.php"; // Database connection

if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];
$uploadDir = "uploads/";
$documentDir = "uploads/documents/";

// Ensure upload directories exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if (!is_dir($documentDir)) {
    mkdir($documentDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['bulk_upload']) && $_FILES['bulk_upload']['error'] == 0) {
        // Process CSV File Upload
        $csvFile = $_FILES['bulk_upload']['tmp_name'];
        if (($handle = fopen($csvFile, 'r')) !== FALSE) {
            fgetcsv($handle); // Skip header row

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                try {
                    $pdo->beginTransaction();

                    // Insert part data
                    $stmt = $pdo->prepare("INSERT INTO parts 
                        (vendor_id,part_name, part_number, `condition`, region, price, tagged_with_easa_form_1, extra_details, warranty, status, created_at) 
                        VALUES (:vendor_id,:part_name, :part_number, :condition, :region, :price, :tagged_with_easa_form_1, :details, :warranty, :status, NOW())");

                    $stmt->execute([
                        ':vendor_id' => $vendor_id,
                        ':part_name' => $data[0],
                        ':part_number' => $data[1],
                        ':condition' => $data[2],
                        ':region' => $data[3],
                        ':price' => $data[4],
                        ':tagged_with_easa_form_1' => $data[5],
                        ':details' => $data[6],
                        ':warranty' => $data[7],
                        ':status' => "pending"
                    ]);

                    $product_id = $pdo->lastInsertId();

                    // Handle images from CSV
                    if (!empty($data[7])) {
                        $imageUrls = explode(",", $data[7]);
                        foreach ($imageUrls as $imageUrl) {
                            $imagePath = $uploadDir . trim($imageUrl);
                            $stmt = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url, created_at) 
                                VALUES (:product_id, 'part', :image_path, NOW())");
                            $stmt->execute([
                                ':product_id' => $product_id,
                                ':image_path' => $imagePath
                            ]);
                        }
                    }

                    // Handle documents from CSV
                    if (!empty($data[8])) {
                        $documentUrls = explode(",", $data[8]);
                        foreach ($documentUrls as $documentUrl) {
                            $documentPath = $documentDir . trim($documentUrl);
                            $stmt = $pdo->prepare("INSERT INTO product_parts_documents (product_id, document_url, created_at) 
                                VALUES (:product_id, :document_path, NOW())");
                            $stmt->execute([
                                ':product_id' => $product_id,
                                ':document_path' => $documentPath
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

            // Insert part data
            $stmt = $pdo->prepare("INSERT INTO parts 
                (vendor_id,part_name, part_number, `condition`, region, price, tagged_with_easa_form_1, extra_details, warranty, status, created_at) 
                VALUES (:vendor_id,:part_name, :part_number, :condition, :region, :price, :tagged_with_easa_form_1, :details, :warranty, :status, NOW())");

            $stmt->execute([
                ':vendor_id' => $vendor_id,
                ':part_name' => $_POST['part_name'],
                ':part_number' => $_POST['part_number'],
                ':condition' => $_POST['condition'],
                ':region' => $_POST['region'],
                ':price' => $_POST['price'],
                ':tagged_with_easa_form_1' => $_POST['tagged_with_easa_form_1'],
                ':details' => $_POST['extra_details'],
                ':warranty' => $_POST['warranty'],
                ':status' => "pending"
            ]);

            $product_id = $pdo->lastInsertId();

            // Handle Image Uploads
            if (!empty($_FILES['images']['name'][0])) {
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    $fileName = time() . "_" . basename($_FILES['images']['name'][$key]);
                    $filePath = $uploadDir . $fileName;
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if (in_array($fileExtension, $allowedExtensions)) {
                        if (move_uploaded_file($tmp_name, $filePath)) {
                            $stmt = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url, created_at) 
                                VALUES (:product_id, 'part', :image_path, NOW())");
                            $stmt->execute([
                                ':product_id' => $product_id,
                                ':image_path' => $filePath
                            ]);
                        }
                    }
                }
            }

            // Handle Document Uploads
            if (!empty($_FILES['documents']['name'][0])) {
                $allowedDocExtensions = ['pdf', 'doc', 'docx'];
                foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
                    $fileName = time() . "_" . basename($_FILES['documents']['name'][$key]);
                    $filePath = $documentDir . $fileName;
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if (in_array($fileExtension, $allowedDocExtensions)) {
                        if (move_uploaded_file($tmp_name, $filePath)) {
                            $stmt = $pdo->prepare("INSERT INTO product_parts_documents (product_id, document_url, created_at) 
                                VALUES (:product_id, :document_path, NOW())");
                            $stmt->execute([
                                ':product_id' => $product_id,
                                ':document_path' => $filePath
                            ]);
                        }
                    }
                }
            }

            $pdo->commit();
            echo "<script>alert('Part added successfully!'); window.location.href='manage_products.php';</script>";

        } catch (Exception $e) {
            $pdo->rollBack();
            die("Error: " . $e->getMessage());
        }
    }
} else {
    die("Invalid request.");
}
?>
