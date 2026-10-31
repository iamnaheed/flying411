<?php
session_start();
include "config.php"; // PDO Database connection

if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];
$uploadDir = "uploads/";
$documentDir = "uploads/documents/";

// Ensure upload directories exist
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
if (!is_dir($documentDir)) mkdir($documentDir, 0777, true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ========================
    // CSV BULK UPLOAD
    // ========================
    if (isset($_FILES['bulk_upload']) && $_FILES['bulk_upload']['error'] == 0) {
        $csvFile = $_FILES['bulk_upload']['tmp_name'];
        if (($handle = fopen($csvFile, 'r')) !== FALSE) {
            fgetcsv($handle); // Skip header
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                try {
                    $pdo->beginTransaction();

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
                        ':part_name' => $data[0],
                        ':part_number' => $data[1],
                        ':condition' => $data[2],
                        ':price' => $data[3],
                        ':currency' => $data[4],
                        ':tagged_with_easa_form_1' => $data[5],
                        ':city' => $data[6],
                        ':state' => $data[7],
                        ':country' => $data[8],
                        ':region' => $data[6] . ', ' . $data[7] . ', ' . $data[8],
                        ':extra_details' => $data[9],
                        ':warranty' => $data[10],
                        ':description' => $data[11],
                        ':weight' => $data[12],
                        ':dimensions' => $data[13],
                        ':package_weight' => $data[14],
                        ':package_dimensions' => $data[15],
                        ':compatible_aircraft' => $data[16],
                        ':manufacturer' => $data[19] ?? '',
                        ':owned_type' => $data[20] ?? '',
                        ':material' => $data[21] ?? '',
                        ':eccn_number' => $data[22] ?? ''
                    ]);

                    $product_id = $pdo->lastInsertId();

                    // Images
                    if (!empty($data[17])) {
                        $images = explode(";", $data[17]);
                        foreach ($images as $img) {
                            $imgPath = $uploadDir . trim($img);
                            $stmt = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url, created_at) VALUES (:product_id, 'part', :imgPath, NOW())");
                            $stmt->execute([':product_id'=>$product_id, ':imgPath'=>$imgPath]);
                        }
                    }

                    // Documents
                    if (!empty($data[18])) {
                        $docs = explode(";", $data[18]);
                        foreach ($docs as $doc) {
                            $docPath = $documentDir . trim($doc);
                            $stmt = $pdo->prepare("INSERT INTO product_parts_documents (product_id, document_url, created_at) VALUES (:product_id, :docPath, NOW())");
                            $stmt->execute([':product_id'=>$product_id, ':docPath'=>$docPath]);
                        }
                    }

                    $pdo->commit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                    die("CSV Upload Error: " . $e->getMessage());
                }
            }
            fclose($handle);
        }
        echo "<script>alert('CSV uploaded successfully!'); window.location.href='manage_products.php';</script>";
        exit;
    }

    // ========================
    // MANUAL FORM SUBMISSION
    // ========================
    try {
        $pdo->beginTransaction();

            $stmt = $pdo->prepare("
            INSERT INTO parts
            (vendor_id, part_name, part_number, `condition`, price, price_label, currency, tagged_with_easa_form_1, country, region,
             extra_details, warranty, description, weight, dimensions, package_weight, package_dimensions, compatible_aircraft,
             manufacturer, owned_type, material, eccn_number, status, created_at)
            VALUES
            (:vendor_id, :part_name, :part_number, :condition, :price, :price_label, :currency, :tagged_with_easa_form_1, :country, :region,
             :extra_details, :warranty, :description, :weight, :dimensions, :package_weight, :package_dimensions, :compatible_aircraft,
             :manufacturer, :owned_type, :material, :eccn_number, 'pending', NOW())
        ");
    
        $stmt->execute([
            ':vendor_id' => $vendor_id,
            ':part_name' => $_POST['part_name'],
            ':part_number' => $_POST['part_number'],
            ':condition' => $_POST['condition'],
            ':price' => $_POST['price'],
            ':price_label' => $_POST['price_label'],
            ':currency' => $_POST['currency'],
            ':tagged_with_easa_form_1' => $_POST['tagged_with_easa_form_1'],
            ':country' => $_POST['country'],
            ':region' => $_POST['country'], // since city & state removed
            ':extra_details' => $_POST['extra_details'],
            ':warranty' => $_POST['warranty'],
            ':description' => $_POST['description'],
            ':weight' => $_POST['weight'],
            ':dimensions' => $_POST['dimensions'],
            ':package_weight' => $_POST['package_weight'],
            ':package_dimensions' => $_POST['package_dimensions'],
            ':compatible_aircraft' => $_POST['compatible_aircraft'], // comma-separated aircraft IDs
            ':manufacturer' => $_POST['manufacturer'],
            ':owned_type' => $_POST['owned_type'],
            ':material' => $_POST['material'],
            ':eccn_number' => $_POST['eccn_number']
        ]);
    
        $product_id = $pdo->lastInsertId();
    
        // ========================
        // Insert Compatible Aircraft into pivot table
        // ========================
        $aircraftIds = explode(',', $_POST['compatible_aircraft'] ?? []);
        foreach ($aircraftIds as $aid) {
            $aid = intval($aid);
            if ($aid > 0) {
                $stmt = $pdo->prepare("
                    INSERT INTO part_aircraft (part_id, aircraft_id) 
                    VALUES (:part_id, :aircraft_id)
                ");
                $stmt->execute([
                    ':part_id' => $product_id,
                    ':aircraft_id' => $aid
                ]);
            }
        }
        


        // Insert manufacturer into pivot table for SEO linking
        $manufacturer = trim($_POST['manufacturer']);
        if ($manufacturer !== '') {
            $stmt = $pdo->prepare("
                INSERT INTO part_manufacturer (part_id, manufacturer)
                VALUES (:part_id, :manufacturer)
                ON DUPLICATE KEY UPDATE updated_at = NOW()
            ");
            $stmt->execute([
                ':part_id' => $product_id,
                ':manufacturer' => $manufacturer
            ]);
        }


        // ========================
        // IMAGE UPLOAD
        // ========================
        if (!empty($_FILES['images']['name'][0])) {
            $allowedExtensions = ['jpg','jpeg','png','gif','webp'];
            foreach ($_FILES['images']['tmp_name'] as $key=>$tmp_name) {
                $fileName = time() . "_" . basename($_FILES['images']['name'][$key]);
                $filePath = $uploadDir . $fileName;
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (in_array($ext, $allowedExtensions) && move_uploaded_file($tmp_name, $filePath)) {
                    $stmt = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url, created_at) VALUES (:product_id, 'part', :imgPath, NOW())");
                    $stmt->execute([':product_id'=>$product_id, ':imgPath'=>$filePath]);
                }
            }
        }

        // ========================
        // DOCUMENT UPLOAD
        // ========================
        if (!empty($_FILES['documents']['name'][0])) {
            $allowedDocExtensions = ['pdf','doc','docx'];
            foreach ($_FILES['documents']['tmp_name'] as $key=>$tmp_name) {
                $fileName = time() . "_" . basename($_FILES['documents']['name'][$key]);
                $filePath = $documentDir . $fileName;
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (in_array($ext, $allowedDocExtensions) && move_uploaded_file($tmp_name, $filePath)) {
                    $stmt = $pdo->prepare("INSERT INTO product_parts_documents (product_id, document_url, created_at) VALUES (:product_id, :docPath, NOW())");
                    $stmt->execute([':product_id'=>$product_id, ':docPath'=>$filePath]);
                }
            }
        }

        $pdo->commit();
        echo "<script>alert('Part added successfully!'); window.location.href='manage_products.php';</script>";
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Form Submission Error: " . $e->getMessage());
    }

} else {
    die("Invalid request.");
}
?>
