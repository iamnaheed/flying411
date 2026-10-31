<?php
include "config.php";

if (!isset($_GET['id'])) {
    die("Product ID is required.");
}

$product_id = $_GET['id'];

// Fetch the original product
$query = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$query->execute([$product_id]);
$product = $query->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

// Insert a duplicate product
$query = $pdo->prepare("
    INSERT INTO products (vendor_id, category_id, product_name, description, price, stock, images, approval_status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
");
$query->execute([
    $product['vendor_id'],
    $product['category_id'],
    $product['product_name'] . " (Copy)",
    $product['description'],
    $product['price'],
    $product['stock'],
    $product['images']
]);

echo "Product duplicated successfully.";
header("Location: manage_products.php");
exit;
?>
