<?php
session_start();
include "config.php"; // Database connection

if (!isset($_SESSION['vendor_id'])) {
    die("Unauthorized access. Please log in first.");
}

$vendor_id = $_SESSION['vendor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['bulk_upload']) && $_FILES['bulk_upload']['error'] == 0) {
        $csvFile = $_FILES['bulk_upload']['tmp_name'];

        if (($handle = fopen($csvFile, 'r')) !== FALSE) {
            fgetcsv($handle); // Skip the header row

            $rowCount = 0;
            $errorCount = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                try {
                    $pdo->beginTransaction();

                    // Insert into engines table
                    $stmt = $pdo->prepare("INSERT INTO engines 
                        (vendor_id, model, manufacturer,sale_type, location, engine_type, power_thrust, year, total_time_hours, hr, cycles, `condition`,price_label, price, currency,warranty, extra_details, tags, status, created_at) 
                        VALUES (:vendor_id, :model, :manufacturer,:sale_type, :location, :engine_type, :power_thrust, :year, :total_time_hours, :hr, :cycles, :condition,:price_label, :price,:currency, :warranty, :extra_details, :tag_type, :status, NOW())");

                    $stmt->execute([
                        ':vendor_id' => $vendor_id,
                        ':model' => $data[0] ?? '',
                        ':manufacturer' => $data[1] ?? '',
                        ':sale_type' => $data[2] ?? '',
                        ':location' => $data[3] ?? '',
                        ':engine_type' => $data[4] ?? '',
                        ':power_thrust' => $data[5] ?? '',
                        ':year' => $data[6] ?? '',
                        ':total_time_hours' => $data[7] ?? '',
                        ':hr' => $data[8] ?? '',
                        ':cycles' => $data[9] ?? '',
                        ':condition' => $data[10] ?? '',
                        ':price_label' => $data[11] ?? '',
                        ':price' => $data[12] ?? '',
                        ':currency' => $data[13] ?? '$',
                        ':warranty' => $data[14] ?? '',
                        ':extra_details' => $data[15] ?? '',
                        ':tag_type' => $data[16] ?? '',
                        ':status' => "pending"
                    ]);

                    $product_id = $pdo->lastInsertId();

                    // Handle Image URLs (column 14)
                    if (!empty($data[17])) {
                        $imageUrls = explode(",", $data[17]);
                        foreach ($imageUrls as $imageUrl) {
                            $stmt = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url, created_at)
                                VALUES (:product_id, 'engine', :image_path, NOW())");
                            $stmt->execute([
                                ':product_id' => $product_id,
                                ':image_path' => 'uploads/' . trim($imageUrl)
                            ]);
                        }
                    }

                    // Handle Document URLs (column 15)
                    if (!empty($data[18])) {
                        $documentUrls = explode(",", $data[18]);
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
                    $rowCount++;
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $errorCount++;
                    error_log("Error processing CSV row: " . $e->getMessage());
                }
            }

            fclose($handle);

            echo "Upload complete. {$rowCount} rows added successfully.";
            if ($errorCount > 0) {
                echo " {$errorCount} rows failed to import.";
            }
        } else {
            echo "Failed to open CSV file.";
        }
    } else {
        echo "No CSV file uploaded or file error.";
    }
} else {
    echo "Invalid request.";
}
?>
