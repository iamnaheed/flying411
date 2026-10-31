<?php
session_start();
include "config.php"; // Database connection

// Show errors during development (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$uploadDir = "../vendors/uploads/";
$documentDir = "../vendors/uploads/documents/";

// Ensure upload directories exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if (!is_dir($documentDir)) {
    mkdir($documentDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $vendor_id = $_POST['vendor_id'] ?? null;

        // ✅ Validate vendor_id exists
        $check = $pdo->prepare("SELECT COUNT(*) FROM vendors WHERE vendor_id = :id AND status = 'active' AND user_role = 'vendor'");
        $check->execute([':id' => $vendor_id]);

        if ($check->fetchColumn() == 0) {
            throw new Exception("Invalid or inactive vendor selected.");
        }

        // ✅ Begin transaction
        $pdo->beginTransaction();

        // ✅ Insert part data
     $stmt = $pdo->prepare("
            INSERT INTO parts
            (vendor_id, part_name, part_number, `condition`, price,currency, tagged_with_easa_form_1, city, state, country, region,
             extra_details, warranty, description, weight, dimensions, package_weight, package_dimensions, compatible_aircraft,
             manufacturer, owned_type, material, eccn_number, status, created_at)
            VALUES
            (:vendor_id, :part_name, :part_number, :condition, :price,:currency, :tagged_with_easa_form_1, :city, :state, :country, :region,
             :extra_details, :warranty, :description, :weight, :dimensions, :package_weight, :package_dimensions, :compatible_aircraft,
             :manufacturer, :owned_type, :material, :eccn_number, 'pending', NOW())
        ");

        $stmt->execute([
            ':vendor_id' => $vendor_id,
            ':part_name' => $_POST['part_name'],
            ':part_number' => $_POST['part_number'],
            ':condition' => $_POST['condition'],
            ':price' => $_POST['price'],
            ':currency' => $_POST['currency'],
            ':tagged_with_easa_form_1' => $_POST['tagged_with_easa_form_1'],
            ':city' => $_POST['city'],
            ':state' => $_POST['state'],
            ':country' => $_POST['country'],
            ':region' => $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['country'],
            ':extra_details' => $_POST['extra_details'],
            ':warranty' => $_POST['warranty'],
            ':description' => $_POST['description'],
            ':weight' => $_POST['weight'],
            ':dimensions' => $_POST['dimensions'],
            ':package_weight' => $_POST['package_weight'],
            ':package_dimensions' => $_POST['package_dimensions'],
            ':compatible_aircraft' => $_POST['compatible_aircraft'],
            ':manufacturer' => $_POST['manufacturer'],
            ':owned_type' => $_POST['owned_type'],
            ':material' => $_POST['material'],
            ':eccn_number' => $_POST['eccn_number']
        ]);

        $product_id = $pdo->lastInsertId();

        // ✅ Handle image uploads
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

        // ✅ Handle document uploads
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

        // ✅ Commit transaction
        $pdo->commit();
        echo "<script>alert('Part added successfully!'); window.location.href='admin-parts.php';</script>";

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Error: " . $e->getMessage());
    }
} else {
    die("Invalid request.");
}
?>
