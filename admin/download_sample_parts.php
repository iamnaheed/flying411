<?php
// Force browser to download as CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=sample_parts.csv');

// Open PHP output as a file handle
$output = fopen('php://output', 'w');

// Write CSV header according to updated table
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



// Close the output
fclose($output);
exit;
?>
