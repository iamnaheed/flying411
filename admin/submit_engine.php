<?php
session_start();
include "config.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Ensure 'model' is not empty
        if (empty($_POST['engine_model'])) {
            throw new Exception("Model field cannot be empty.");
        }

        // Begin transaction for manual form submission
        $pdo->beginTransaction();

        // Insert into engines table
        $stmt = $pdo->prepare("INSERT INTO engines 
                (vendor_id, model, manufacturer,  city, state, country,location, engine_type, power_thrust, year, total_time_hours, hr, cycles, `condition`, price,currency, extra_details, warranty, tags, status, created_at) 
                VALUES (:vendor_id, :model, :manufacturer,:city, :state, :country, :location, :engine_type, :power_thrust, :year, :total_time_hours, :hr, :cycles, :condition, :price,:currency, :extra_details, :warranty, :tags, :status, NOW())");

        $stmt->execute([
            ':vendor_id' => $_POST['vendor_id'],
            ':model' => $_POST['engine_model'],
            ':manufacturer' => $_POST['manufacturer'],
            ':city' => $_POST['city'],
                ':state' => $_POST['state'],
                ':country' => $_POST['country'],
                ':location' => $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['country'],
            ':engine_type' => $_POST['engine_type'],
            ':power_thrust' => $_POST['power_thrust'],
            ':year' => $_POST['year'],
            ':total_time_hours' => $_POST['total_time_hours'],
            ':hr' => $_POST['hr'],
            ':cycles' => $_POST['cycles'],
            ':condition' => $_POST['condition'],
            ':price' => $_POST['price'],
            ':currency' => $_POST['currency'],
            ':extra_details' => $_POST['extra_details'],
            ':warranty' => $_POST['warranty'],
            ':tags' => $_POST['tags'],
            ':status' => "pending"
        ]);

        $product_id = $pdo->lastInsertId();

        // Handle image uploads
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = "../vendors/uploads/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $fileName = time() . "_" . basename($_FILES['images']['name'][$key]);
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($tmp_name, $filePath)) {
                    $stmt = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url, created_at) 
                        VALUES (:product_id, 'engine', :image_path, NOW())");
                    $stmt->execute([
                        ':product_id' => $product_id,
                        ':image_path' => $filePath
                    ]);
                }
            }
        }

        // Handle document uploads
        if (!empty($_FILES['documents']['name'][0])) {
            $documentDir = "../vendors/uploads/documents/";
            if (!is_dir($documentDir)) mkdir($documentDir, 0777, true);

            foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
                $docName = time() . "_" . basename($_FILES['documents']['name'][$key]);
                $docPath = $documentDir . $docName;

                if (move_uploaded_file($tmp_name, $docPath)) {
                    $stmt = $pdo->prepare("INSERT INTO product_documents (product_id, product_type, document_url, created_at) 
                        VALUES (:product_id, 'engine', :document_path, NOW())");
                    $stmt->execute([
                        ':product_id' => $product_id,
                        ':document_path' => $docPath
                    ]);
                }
            }
        }

        // Commit the transaction
        $pdo->commit();
        echo "<script>alert('Engine added successfully!'); window.location.href='admin-engines.php';</script>";

    } catch (Exception $e) {
        // Rollback if error occurs
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Error: " . $e->getMessage());
    }
} else {
    die("Invalid request.");
}
?>
