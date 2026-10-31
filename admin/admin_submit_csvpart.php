<?php
session_start();
include "config.php";

if (!isset($_POST['vendor_id'])) {
    die("Vendor ID missing");
}

$vendor_id = $_POST['vendor_id'];

$uploadDir = "../vendors/uploads/";
$documentDir = "../vendors/uploads/documents/";

if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
if (!is_dir($documentDir)) mkdir($documentDir, 0777, true);

if (isset($_FILES['bulk_upload']) && $_FILES['bulk_upload']['error'] === 0) {

    $csvFile = $_FILES['bulk_upload']['tmp_name'];

    if (($handle = fopen($csvFile, 'r')) !== FALSE) {

        $header = fgetcsv($handle);

        $header = array_map(function($h) {
            return strtolower(str_replace(' ', '_', trim($h)));
        }, $header);

        while (($data = fgetcsv($handle)) !== FALSE) {

            try {
                $pdo->beginTransaction();

                $row = array_combine($header, $data);

                $region = trim($row['city']) . ', ' . trim($row['state']) . ', ' . trim($row['country']);

                $stmt = $pdo->prepare("
                    INSERT INTO parts 
                    (vendor_id, part_name, part_number, `condition`, city, state, country, region, price, currency,
                    tagged_with_easa_form_1, extra_details, warranty, description, weight, dimensions,
                    package_weight, package_dimensions, compatible_aircraft, manufacturer, owned_type,
                    material, eccn_number, status, created_at)
                    VALUES 
                    (:vendor_id, :part_name, :part_number, :condition, :city, :state, :country, :region, :price, :currency,
                    :tagged_with_easa_form_1, :extra_details, :warranty, :description, :weight, :dimensions,
                    :package_weight, :package_dimensions, :compatible_aircraft, :manufacturer, :owned_type,
                    :material, :eccn_number, 'pending', NOW())
                ");

                $stmt->execute([
                    ':vendor_id' => $vendor_id,
                    ':part_name' => trim($row['part_name']),
                    ':part_number' => trim($row['part_number']),
                    ':condition' => trim($row['condition']),
                    ':city' => trim($row['city']),
                    ':state' => trim($row['state']),
                    ':country' => trim($row['country']),
                    ':region' => $region,
                    ':price' => trim($row['price']),
                    ':currency' => trim($row['currency']),
                    ':tagged_with_easa_form_1' => trim($row['tagged_with_easa_form_1']),
                    ':extra_details' => trim($row['extra_details']),
                    ':warranty' => trim($row['warranty']),
                    ':description' => trim($row['description']),
                    ':weight' => trim($row['weight']),
                    ':dimensions' => trim($row['dimensions']),
                    ':package_weight' => trim($row['package_weight']),
                    ':package_dimensions' => trim($row['package_dimensions']),
                    ':compatible_aircraft' => trim($row['compatible_aircraft']),
                    ':manufacturer' => trim($row['manufacturer']),
                    ':owned_type' => trim($row['owned_type']),
                    ':material' => trim($row['material']),
                    ':eccn_number' => trim($row['eccn'])
                ]);

                $part_id = $pdo->lastInsertId();

                // -------------------------------------------
                // ✅ SAVE IMAGES (only block URLs)
                // -------------------------------------------
                if (!empty($row['images'])) {
                    $images = explode(";", $row['images']);

                    foreach ($images as $img) {

                        $img = trim($img);

                        // ❌ Block URLs
                        if (preg_match('/^https?:\/\//i', $img)) {
                            throw new Exception("URL not allowed in images: $img");
                        }

                        $path = $uploadDir . $img;

                        $pdo->prepare("
                                INSERT INTO product_images (product_id, product_type, image_url, created_at)
                                VALUES (:pid, 'part', :url, NOW())
                            ")->execute([
                                ':pid' => $part_id,
                                ':url' => $path
                            ]);
                    }
                }

                // -------------------------------------------
                // ✅ SAVE DOCUMENTS (only block URLs)
                // -------------------------------------------
                if (!empty($row['documents'])) {
                    $docs = explode(";", $row['documents']);

                    foreach ($docs as $doc) {

                        $doc = trim($doc);

                        // ❌ Block URLs
                        if (preg_match('/^https?:\/\//i', $doc)) {
                            throw new Exception("URL not allowed in documents: $doc");
                        }

                        $path = $documentDir . $doc;

                        $pdo->prepare("
                                INSERT INTO product_parts_documents (product_id, document_url, created_at)
                                VALUES (:pid, :url, NOW())
                            ")->execute([
                                ':pid' => $part_id,
                                ':url' => $path
                            ]);
                    }
                }

                $pdo->commit();

            } catch (Exception $e) {
                $pdo->rollBack();
                echo "Error: " . $e->getMessage();
                exit;
            }
        }

        fclose($handle);
        echo "CSV data uploaded successfully!";
    } else {
        echo "Unable to read CSV file.";
    }

} else {
    echo "No CSV file uploaded or upload error.";
}
?>
