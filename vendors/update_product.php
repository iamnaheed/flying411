<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $category_id = $_POST['category'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    // Handle file uploads if any
    $uploadedImages = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            $fileName = uniqid() . '_' . $_FILES['images']['name'][$key];
            $filePath = 'uploads/' . $fileName;
            if (move_uploaded_file($tmpName, $filePath)) {
                $uploadedImages[] = $filePath;
            }
        }
    }
    $imagesJson = $uploadedImages ? json_encode($uploadedImages) : null;

    // Update product
    $query = $pdo->prepare("
        UPDATE products 
        SET category_id = ?, product_name = ?, description = ?, price = ?, stock = ?, images = IFNULL(?, images)
        WHERE product_id = ?
    ");
    $query->execute([$category_id, $product_name, $description, $price, $stock, $imagesJson, $product_id]);

    echo "Product updated successfully.";
    header("Location: manage_products.php");
    exit;
}
?>
