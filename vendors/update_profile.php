<?php
include "config.php";
session_start();

// Check login
if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch existing vendor
$stmt = $pdo->prepare("SELECT * FROM vendors WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
    die("Vendor not found");
}

// --------------------
// Form data
// --------------------
$contact_name  = trim($_POST['contact_name'] ?? $vendor['contact_name']);
$contact_email = trim($_POST['contact_email'] ?? $vendor['contact_email']);
$contact_phone = trim($_POST['contact_phone'] ?? $vendor['contact_phone']);
$phone_code    = trim($_POST['contact_phone_code'] ?? $vendor['contact_phone_code']);

// --------------------
// Profile picture upload
// --------------------
$profile_picture = $vendor['profile_picture'];

if (!empty($_FILES['profile_picture']['name'])) {

    $folder = "uploads/profile/";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['profile_picture']['name']);
    $target   = $folder . $fileName;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target)) {
        $profile_picture = $target;
    }
}

// --------------------
// Update query (ONLY LOGIN INFO)
// --------------------
$update = $pdo->prepare("
    UPDATE vendors SET
        contact_name = ?,
        contact_email = ?,
        contact_phone = ?,
        contact_phone_code = ?,
        profile_picture = ?
    WHERE vendor_id = ?
");

$update->execute([
    $contact_name,
    $contact_email,
    $contact_phone,
    $phone_code,
    $profile_picture,
    $vendor_id
]);

// Redirect back
header("Location: vendor-profile.php?success=Profile updated successfully");
exit;
