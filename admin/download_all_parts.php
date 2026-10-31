<?php
include "config.php"; // Your PDO connection
session_start();

// Fetch all parts
$stmt = $pdo->query("SELECT part_number, part_name,manufacturer, image_src, description, unit_of_measure, part_link, eccn, part_type, weight, dimensions, packaged_weight, packaged_dimensions, compatible_aircraft, notes,status FROM parts_master ORDER BY id ASC");
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Force download as CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=all_parts.csv');

$output = fopen('php://output', 'w');

// CSV Header
fputcsv($output, [
    'Part No', 
    'Part Title', 
    'Manufacturer', 
    'Image src',
    'Description',
    'Unit of Measure',
    'Part Link',
    'ECCN',
    'Part Type',
    'Weight',
    'Dimensions',
    'Packaged Weight',
    'Packaged Dimensions',
    'Compatible Aircraft',
    'Notes',
    'Status'
]);

// CSV Data
foreach ($parts as $p) {
    fputcsv($output, [
        $p['part_number'],
        $p['part_name'],
        $p['manufacturer'],
        $p['image_src'],
        $p['description'],
        $p['unit_of_measure'],
        $p['part_link'],
        $p['eccn'],
        $p['part_type'],
        $p['weight'],
        $p['dimensions'],
        $p['packaged_weight'],
        $p['packaged_dimensions'],
        $p['compatible_aircraft'],
        $p['notes'],
        $p['status']
    ]);
}

fclose($output);
exit;
?>
