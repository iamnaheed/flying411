<?php
include "config.php";

session_start();
$vendor_id = $_SESSION['vendor_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['format'])) {
    $format = $_POST['format'];

    // Fetch sales data
    $query = $pdo->prepare("
        SELECT 
            c.category_name,
            p.product_name,
            COUNT(o.order_id) AS total_sales,
            SUM(o.total_amount) AS total_revenue
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.product_id
        JOIN categories c ON p.category_id = c.category_id
        WHERE p.vendor_id = ?
        GROUP BY c.category_name, p.product_name
    ");
    $query->execute([$vendor_id]);
    $sales_data = $query->fetchAll(PDO::FETCH_ASSOC);

    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sales_report.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Category', 'Product', 'Total Sales', 'Total Revenue']);
        foreach ($sales_data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    } elseif ($format === 'pdf') {
        // Use a PDF generation library like TCPDF or FPDF
        require 'fpdf.php';
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(40, 10, 'Sales Report');
        $pdf->Ln(10);
        foreach ($sales_data as $row) {
            $pdf->Cell(0, 10, $row['category_name'] . ' - ' . $row['product_name'] . ' - ' . $row['total_sales'] . ' - $' . $row['total_revenue'], 0, 1);
        }
        $pdf->Output('D', 'sales_report.pdf');
        exit;
    }
}

header("Location: sales_reports.php");
exit;
?>
