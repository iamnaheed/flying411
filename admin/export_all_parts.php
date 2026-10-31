<?php 
session_start();
include "config.php";

if (!isset($_SESSION['vendor_id'])) {
    die("Unauthorized access");
}

try {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="all_parts.csv"');

    $output = fopen('php://output', 'w');

    // Columns to include in CSV (exclude part_id and vendor_id)
    $columns = [
        'Part Name','Manufacturer','Owned Type','Material','ECCN Number','Description',
        'Weight','Dimensions','Package Weight','Package Dimensions','Compatible Aircraft',
        'Part Number','Type','Condition','City','State','Country','Region','Price','Currency',
        'Is Sold','Tagged With EASA Form 1','Extra Details','Warranty','Status','Deleted At','Created At'
    ];
    fputcsv($output, $columns);

    // Query all parts (exclude part_id and vendor_id)
    $stmt = $pdo->query("
        SELECT part_name, manufacturer, owned_type, material, eccn_number, description,
               weight, dimensions, package_weight, package_dimensions, compatible_aircraft,
               part_number, `type`, `condition`, city, state, country, region, price, currency,
               is_sold, tagged_with_easa_form_1, extra_details, warranty, status, deleted_at, created_at
        FROM parts
        ORDER BY part_name ASC
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;

} catch (Exception $e) {
    echo "Error generating CSV: " . $e->getMessage();
    exit;
}
?>
