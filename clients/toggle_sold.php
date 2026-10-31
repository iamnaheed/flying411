<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php'; // Path to your config.php

// Validate GET parameters
if(isset($_GET['id'], $_GET['current'], $_GET['type'])){
    $id = intval($_GET['id']);
    $current = intval($_GET['current']);
    $type = $_GET['type'];

    // Determine table name based on type
    switch($type){
        case 'aircraft': $table = 'aircrafts'; break;
        case 'engine':   $table = 'engines'; break;
        case 'part':     $table = 'parts'; break;
        default: die("Invalid product type.");
    }

    // Toggle is_sold value
    $new_status = $current ? 0 : 1;

    // Prepare and execute PDO query
    $stmt = $pdo->prepare("UPDATE {$table} SET is_sold = :is_sold WHERE {$type}_id = :id");
    $stmt->execute([
        ':is_sold' => $new_status,
        ':id' => $id
    ]);

    // Redirect back safely
    $redirect = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'products_list.php';
    header("Location: $redirect");
    exit;

} else {
    die("Invalid parameters.");
}
?>
