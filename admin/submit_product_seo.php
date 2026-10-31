<?php
session_start();
include "config.php"; // DB connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the form
    $category = $_POST['category'];
    $product_id = $_POST['product_id'];
    $meta_title = $_POST['meta_title'];
    $meta_keywords = $_POST['meta_keywords'];
    $meta_description = $_POST['meta_description'];

    try {
        // Insert the SEO data into the product_seo table
        $stmt = $pdo->prepare("INSERT INTO product_seo (category, product_id, meta_title, meta_keywords, meta_description) 
                               VALUES (?, ?, ?, ?, ?)");

        // Execute the query to insert data
        $stmt->execute([$category, $product_id, $meta_title, $meta_keywords, $meta_description]);

        // Redirect to product_success.php after successful insertion
        header("Location: product_success_seo.php");
        exit(); // Don't forget to call exit to ensure the script stops executing after the redirect.

    } catch (PDOException $e) {
        die('Database error: ' . $e->getMessage());
    }
}
?>
