<?php
session_start();
require_once "config.php";

// Ensure login
if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: vendor-profile.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

// --------------------
// CLEAN INPUT
// --------------------
function clean($value) {
    return trim($value ?? '');
}

$business_name        = clean($_POST['business_name']);
$business_phone       = clean($_POST['business_phone']);
$business_phone_code  = clean($_POST['business_phone_code']);
$business_email       = clean($_POST['business_email']);
$website_url          = clean($_POST['website_url']);
$business_address     = clean($_POST['business_address']);

$bank_name            = clean($_POST['bank_name']);
$account_number       = clean($_POST['account_number']);
$account_holder       = clean($_POST['account_holder']);
$iban                 = clean($_POST['iban']);
$swift_code           = clean($_POST['swift_code']);

// Fix website URL
if (!empty($website_url) && !preg_match('#^https?://#i', $website_url)) {
    $website_url = 'https://' . $website_url;
}

// --------------------
// FETCH EXISTING DATA
// --------------------
$stmt = $pdo->prepare("SELECT logo FROM vendors WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
    $_SESSION['error'] = "Vendor not found";
    header("Location: vendor-profile.php");
    exit;
}

$logo = $vendor['logo'];

// --------------------
// LOGO UPLOAD
// --------------------
if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === 0) {

    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
    $fileName = time() . "_" . uniqid() . "." . $ext;
    $target = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
        $logo = $target;
    }
}

// --------------------
// UPDATE QUERY
// --------------------
$update = $pdo->prepare("
    UPDATE vendors SET
        business_name = ?,
        business_phone = ?,
        business_phone_code = ?,
        business_email = ?,
        website_url = ?,
        business_address = ?,
        logo = ?,
        bank_name = ?,
        account_number = ?,
        account_holder = ?,
        iban = ?,
        swift_code = ?
    WHERE vendor_id = ?
");

$update->execute([
    $business_name,
    $business_phone,
    $business_phone_code,
    $business_email,
    $website_url,
    $business_address,
    $logo,
    $bank_name,
    $account_number,
    $account_holder,
    $iban,
    $swift_code,
    $vendor_id
]);

$_SESSION['success'] = "Business & payment information updated successfully";
header("Location: vendor-business.php");
exit;
