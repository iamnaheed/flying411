<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

/* ================= REQUEST CHECK ================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

/* ================= HELPERS ================= */
function clean($value) {
    return trim(htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'));
}

/* ================= ERROR HOLDER ================= */
$errors = [];

/* ================= RECAPTCHA ================= */
if (empty($_POST['g-recaptcha-response'])) {
    echo json_encode([
        'success' => false,
        'message' => 'reCAPTCHA verification failed',
        'step'    => 3
    ]);
    exit;
}

$recaptcha = $_POST['g-recaptcha-response'];

$verify = @file_get_contents(
    "https://www.google.com/recaptcha/api/siteverify?secret=" .
    RECAPTCHA_SECRET_KEY . "&response=" . $recaptcha
);

if ($verify === false) {
    echo json_encode([
        'success' => false,
        'message' => 'reCAPTCHA server error',
        'step'    => 3
    ]);
    exit;
}

$captcha = json_decode($verify, true);

if (empty($captcha['success'])) {
    echo json_encode([
        'success' => false,
        'message' => 'reCAPTCHA verification failed',
        'step'    => 3
    ]);
    exit;
}

/* ================= STEP 1 ================= */
$contact_name       = clean($_POST['contact_name'] ?? '');
$contact_email      = clean($_POST['contact_email'] ?? '');
$contact_phone      = clean($_POST['contact_phone'] ?? '');
$contact_phone_code = clean($_POST['contact_phone_code'] ?? '');
$password_raw       = $_POST['password'] ?? '';

/* ================= STEP 2 ================= */
$business_name       = clean($_POST['business_name'] ?? '');
$business_email      = clean($_POST['business_email'] ?? '');
$business_phone      = clean($_POST['business_phone'] ?? '');
$business_phone_code = clean($_POST['business_phone_code'] ?? '');
$website_url         = clean($_POST['website_url'] ?? '');

/* ================= STEP 3 ================= */
$services = array_map('clean', (array)($_POST['selected_services'] ?? []));

$owns_aircraft = clean($_POST['owns_aircraft'] ?? 'no');
$aircraft_type = clean($_POST['aircraft_type'] ?? '');
$buy_sell      = clean($_POST['looking_to_buy_or_sell'] ?? 'no');

$marketing_consent = isset($_POST['marketing_consent']) ? 1 : 0;

/* ================= VALIDATION ================= */
$passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/';

/* STEP 1 */
if ($contact_name === '') {
    $errors['contact_name'] = 'Username is required';
} elseif (preg_match('/\s/', $contact_name)) {
    $errors['contact_name'] = 'Username must not contain spaces';
} elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $contact_name)) {
    $errors['contact_name'] = 'Only letters, numbers and underscore allowed';
}

if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
    $errors['contact_email'] = 'Valid contact email required';
}

if ($contact_phone === '') {
    $errors['contact_phone'] = 'Phone number required';
}

if (!preg_match($passwordPattern, $password_raw)) {
    $errors['password'] = 'Password must be strong';
}

/* STEP 2 */
if ($business_name === '') {
    $errors['business_name'] = 'Business name required';
}

if (!filter_var($business_email, FILTER_VALIDATE_EMAIL)) {
    $errors['business_email'] = 'Valid business email required';
}

/* STEP 3 */
if (empty($services)) {
    $errors['selected_services'] = 'Select at least one service';
}

if ($owns_aircraft === 'yes' && $aircraft_type === '') {
    $errors['aircraft_type'] = 'Aircraft type required';
}

if (!$marketing_consent) {
    $errors['marketing_consent'] = 'Marketing consent required';
}

/* ================= ERROR STEP DECISION ================= */
$step = 1;

if (isset($errors['business_name'], $errors['business_email'])) {
    $step = 2;
}

if (
    isset($errors['selected_services']) ||
    isset($errors['marketing_consent']) ||
    isset($errors['aircraft_type'])
) {
    $step = 3;
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please fix the highlighted errors',
        'errors'  => $errors,
        'step'    => $step
    ]);
    exit;
}

/* ================= DUPLICATE EMAIL CHECK ================= */
$stmt = $pdo->prepare("
    SELECT vendor_id
    FROM vendors
    WHERE contact_email = :ce
       OR business_email = :ce
       OR contact_email = :be
       OR business_email = :be
    LIMIT 1
");

$stmt->execute([
    ':ce' => $contact_email,
    ':be' => $business_email
]);

if ($stmt->fetch()) {
    echo json_encode([
        'success' => false,
        'message' => 'Email already exists',
        'errors'  => [
            'contact_email'  => 'Email already exists',
            'business_email' => 'Email already exists'
        ],
        'step' => 1
    ]);
    exit;
}

/* ================= FILE UPLOAD ================= */
function uploadFile($file, $dir) {
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $name = uniqid('', true) . '.' . $ext;
    move_uploaded_file($file['tmp_name'], $dir . $name);
    return $name;
}

$profile_picture = uploadFile($_FILES['profile_picture'] ?? null, 'uploads/profile/');
$logo            = uploadFile($_FILES['logo'] ?? null, 'uploads/logo/');

/* ================= ROLE (UNCHANGED LOGIC) ================= */
$user_role = (count($services) > 1 || !empty(array_diff($services, ['Aircraft for Sale'])))
    ? 'vendor'
    : 'client';

$selected_services = implode(', ', $services);

/* ================= INSERT ================= */
$insert = $pdo->prepare("
INSERT INTO vendors (
    business_name, business_phone, business_phone_code, business_email,
    contact_name, contact_phone, contact_phone_code, contact_email,
    profile_picture, logo, password, user_role, selected_services,
    owns_aircraft, aircraft_type, looking_to_buy_or_sell,
    website_url, status, created_at
) VALUES (
    :bn, :bp, :bpc, :be,
    :cn, :cp, :cpc, :ce,
    :pp, :lg, :pw, :ur, :ss,
    :oa, :at, :bs,
    :wu, 'active', NOW()
)
");

$insert->execute([
    ':bn' => $business_name,
    ':bp' => $business_phone,
    ':bpc'=> $business_phone_code,
    ':be' => $business_email,
    ':cn' => $contact_name,
    ':cp' => $contact_phone,
    ':cpc'=> $contact_phone_code,
    ':ce' => $contact_email,
    ':pp' => $profile_picture,
    ':lg' => $logo,
    ':pw' => password_hash($password_raw, PASSWORD_DEFAULT),
    ':ur' => $user_role,
    ':ss' => $selected_services,
    ':oa' => $owns_aircraft,
    ':at' => $aircraft_type ?: null,
    ':bs' => $buy_sell,
    ':wu' => $website_url
]);

/* ================= AUTO LOGIN ================= */
session_regenerate_id(true);
$_SESSION['vendor_id'] = $pdo->lastInsertId();
$_SESSION['username']  = $contact_name;
$_SESSION['user_role'] = $user_role;

/* ================= SUCCESS ================= */
echo json_encode([
    'success'  => true,
    'redirect' => ($user_role === 'vendor')
        ? 'vendors/index.php'
        : 'clients/index.php'
]);
exit;
