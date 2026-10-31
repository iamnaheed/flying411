<?php
session_start();
include "config.php";

if (!isset($_SESSION['vendor_id'])) {
    die("Unauthorized access");
}

$vendor_id = $_SESSION['vendor_id'];
$uploadDir = "uploads/";
$documentDir = "uploads/documents/";

if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
if (!is_dir($documentDir)) mkdir($documentDir, 0777, true);

// Helper: normalize value (trim and strip surrounding quotes)
function normalize_value($val) {
    $v = trim($val);
    $v = preg_replace('/^["\']+|["\']+$/', '', $v);
    return $v;
}

// Helper: detect full URLs robustly (http/https)
function is_full_url($val) {
    $v = trim($val);
    if ($v === '') return false;
    $v = preg_replace('/^["\']+|["\']+$/', '', $v);
    if (filter_var($v, FILTER_VALIDATE_URL)) return true;
    return preg_match('/^\s*https?:\/\//i', $v);
}

$anyError = false;
$errorMessage = "";

if (isset($_FILES['bulk_upload']) && $_FILES['bulk_upload']['error'] === 0) {

    $csvFile = $_FILES['bulk_upload']['tmp_name'];

    if (($handle = fopen($csvFile, 'r')) !== FALSE) {

        $header = fgetcsv($handle);
        if ($header === false) {
            echo "<script>alert('CSV header read error.');</script>";
            exit;
        }

        $header = array_map(function($h) {
            return strtolower(str_replace(' ', '_', trim($h)));
        }, $header);

        $rowNumber = 1; // header is row 1

        while (($data = fgetcsv($handle)) !== FALSE) {
            $rowNumber++;

            if ($anyError) break;

            try {
                $row = @array_combine($header, $data);
                if ($row === false) throw new Exception("Malformed CSV row at line $rowNumber.");

                // Validate images
                if (!empty($row['images'])) {
                    $images = explode(";", $row['images']);
                    foreach ($images as $imgRaw) {
                        $img = normalize_value($imgRaw);
                        if ($img === '' || strtolower($img) === 'nil') continue;
                        if (is_full_url($img)) {
                            throw new Exception("Image URL not allowed at row $rowNumber: $img");
                        }
                    }
                }

                // Validate documents
                if (!empty($row['documents'])) {
                    $docs = explode(";", $row['documents']);
                    foreach ($docs as $docRaw) {
                        $doc = normalize_value($docRaw);
                        if ($doc === '' || strtolower($doc) === 'nil') continue;
                        if (is_full_url($doc)) {
                            throw new Exception("Document URL not allowed at row $rowNumber: $doc");
                        }
                    }
                }

                // Insert part
                $pdo->beginTransaction();

                $region =trim($row['country']);

                $stmt = $pdo->prepare("
                    INSERT INTO parts 
                    (
                        vendor_id, part_name, part_number, `condition`, price_label, country, region, price, currency,
                        tagged_with_easa_form_1, extra_details, warranty, description, weight, dimensions,
                        package_weight, package_dimensions, compatible_aircraft, manufacturer, owned_type,
                        material, eccn_number, status, created_at
                    )
                    VALUES 
                    (
                        :vendor_id, :part_name, :part_number, :condition, :price_label, :country, :region, :price, :currency,
                        :tag_type, :extra_details, :warranty, :description, :weight, :dimensions,
                        :package_weight, :package_dimensions, :compatible_aircraft, :manufacturer, :sale_type,
                        :material, :eccn_number, 'pending', NOW()
                    )
                ");


                $stmt->execute([
                    ':vendor_id' => $vendor_id,
                    ':part_name' => normalize_value($row['part_name']),
                    ':part_number' => normalize_value($row['part_number']),
                    ':condition' => normalize_value($row['condition']),
                    ':price_label' => normalize_value($row['price_label']),
                    ':country' => normalize_value($row['country']),
                    ':region' => $region,
                    ':price' => normalize_value($row['price']),
                    ':currency' => normalize_value($row['currency']),
                
                    // ✅ CORRECT CSV → DB mapping
                    ':sale_type' => normalize_value($row['sale_type']),     // owned_type
                    ':tag_type'  => normalize_value($row['tag_type']),      // tagged_with_easa_form_1
                
                    ':extra_details' => normalize_value($row['extra_details']),
                    ':warranty' => normalize_value($row['warranty']),
                    ':description' => normalize_value($row['description']),
                    ':weight' => normalize_value($row['weight']),
                    ':dimensions' => normalize_value($row['dimensions']),
                    ':package_weight' => normalize_value($row['package_weight']),
                    ':package_dimensions' => normalize_value($row['package_dimensions']),
                    ':compatible_aircraft' => normalize_value($row['compatible_aircraft']),
                    ':manufacturer' => normalize_value($row['manufacturer']),
                    ':material' => normalize_value($row['material']),
                    ':eccn_number' => normalize_value($row['eccn_number'])
                ]);



                $part_id = $pdo->lastInsertId();

                // Save images
                if (!empty($row['images'])) {
                    $images = explode(";", $row['images']);
                    foreach ($images as $imgRaw) {
                        $img = normalize_value($imgRaw);
                        if ($img === '' || strtolower($img) === 'nil') continue;
                        $imgBasename = basename($img);
                        $pdo->prepare("
                            INSERT INTO product_images (product_id, product_type, image_url, created_at)
                            VALUES (:pid, 'part', :url, NOW())
                        ")->execute([':pid'=>$part_id, ':url'=>$uploadDir.$imgBasename]);
                    }
                }

                // Save documents
                if (!empty($row['documents'])) {
                    $docs = explode(";", $row['documents']);
                    foreach ($docs as $docRaw) {
                        $doc = normalize_value($docRaw);
                        if ($doc === '' || strtolower($doc) === 'nil') continue;
                        $docBasename = basename($doc);
                        $pdo->prepare("
                            INSERT INTO product_parts_documents (product_id, document_url, created_at)
                            VALUES (:pid, :url, NOW())
                        ")->execute([':pid'=>$part_id, ':url'=>$documentDir.$docBasename]);
                    }
                }

                $pdo->commit();

            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $anyError = true;
                $errorMessage = $e->getMessage();
                break;
            }
        }

        fclose($handle);

        if ($anyError) {
            echo "CSV upload failed: Image URL not allowed Used correct URL default-service.jpg";
        } else {
            echo "'CSV uploaded successfully!'";
        }

        exit;

    } else {
        echo "('Unable to read CSV file.');";
        exit;
    }

} else {
    echo "No CSV file uploaded or file upload error.";
    exit;
}
?>