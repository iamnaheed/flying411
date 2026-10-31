<?php
session_start();
include "config.php";


// ---------------------- AUTH CHECK ----------------------
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// ---------------------- REQUEST VALIDATION ----------------------
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    die("Invalid request.");
}

$service_type = $_POST['service_type'] ?? "";
if ($service_type == "") {
    die("Service type is required.");
}

// ---------------------- FILE CHECK ----------------------
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== 0) {
    die("CSV file upload failed.");
}

$csvFile = $_FILES['csv_file']['tmp_name'];
$handle = fopen($csvFile, "r");

if (!$handle) {
    die("Unable to open CSV file.");
}

// ---------------------- READ HEADER ----------------------
$header = fgetcsv($handle);
$header = array_map(fn($h) => strtolower(trim($h)), $header);

// ---------------------- SERVICE TYPE COLUMN MAPS ----------------------
$maps = [
    "flight_school" => [
        "business_name"      => "school_name",
        "school_type"        => "school_type",
        "certificate_number" => "certificate_number",
        "instruction_rate"   => "instruction_rate",
        "ground_rate"        => "ground_rate",
        "instruction_offered"=> "instruction_offered",
        "aircraft_available" => "aircraft_available",
        "address_line1"      => "address_line1",
        "address_line2"      => "address_line2",
        "postal_code"        => "postal_code",
        "country"            => "country", // combine city,state,country here
        "phone_number"       => "phone_number",
        "email"              => "email",
        "website"            => "website",
        "description"        => "description",
        "photos"             => null,
        "hourly_rate"        => null,
        "mechanic_ratings"   => null,
        "shop_type"          => null,
        "ratings"            => "ratings",
    ],

    "avionics_shop" => [
        "business_name"      => "business name",
        "country"            => "country",
        "phone_number"       => "phone number",
        "email"              => "email",
        "website"            => "website",
        "description"        => "description",
        "photos"             => null,
        "hourly_rate"        => "hourly rate",
        "instruction_rate"   => null,
        "ground_rate"        => null,
        "instruction_offered"=> null,
        "aircraft_available" => null,
        "school_type"        => null,
        "certificate_number" => null,
        "ratings"            => null,
        "mechanic_ratings"   => null,
        "shop_type"          => null,
        "address_line1"      => null,
        "address_line2"      => null,
        "postal_code"        => null
    ],

    "engine_shop" => [
        "business_name"    => "business name",
        "country"          => "country",
        "phone_number"     => "phone number",
        "email"            => "email",
        "website"          => "website",
        "description"      => "description",
        "photos"           => null,
        "shop_type"        => "ShopType",
        "hourly_rate"      => "Hourly Rate",
        "mechanic_ratings" => null,
        "ratings"          => null,
        "instruction_rate" => null,
        "ground_rate"      => null,
        "instruction_offered"=> null,
        "aircraft_available"=> null,
        "school_type"        => null,
        "certificate_number" => null,
        "address_line1"      => null,
        "address_line2"      => null,
        "postal_code"        => null
    ],

    "flight_instructor" => [
        "business_name"      => "business name",
        "country"            => "country",
        "phone_number"       => "phone number",
        "email"              => "email",
        "website"            => "website",
        "description"        => "description",
        "photos"             => null,
        "instruction_rate"   => "instruction rate",
        "ground_rate"        => "ground rate",
        "ratings"            => "Flight Instructor Ratings (CFI, CFII, MEI)",
        "aircraft_available" => "aircraft available",
        "hourly_rate"        => null,
        "mechanic_ratings"   => null,
        "shop_type"          => null,
        "instruction_offered"=> null,
        "school_type"        => null,
        "certificate_number" => null,
        "address_line1"      => null,
        "address_line2"      => null,
        "postal_code"        => null
    ],

    "local_mechanic" => [
        "business_name"    => "business name",
        "country"          => "country",
        "phone_number"     => "phone number",
        "email"            => "email",
        "website"          => "website",
        "description"      => "description",
        "photos"           => null,
        "hourly_rate"      => "Hourly Rate",
        "mechanic_ratings" => "Mechanic Ratings (A, P, A&P, IA)",
        "instruction_rate" => null,
        "ground_rate"      => null,
        "ratings"          => null,
        "aircraft_available" => null,
        "school_type"        => null,
        "certificate_number" => null,
        "address_line1"      => null,
        "address_line2"      => null,
        "postal_code"        => null
    ],

    "maintenance_shop" => [
        "business_name"    => "business name",
        "country"          => "country",
        "phone_number"     => "phone number",
        "email"            => "email",
        "website"          => "website",
        "description"      => "description",
        "photos"           => null,
        "hourly_rate"      => "hourly rate",
        "instruction_rate" => null,
        "ground_rate"      => null,
        "ratings"          =>null,
        "mechanic_ratings" => null,
        "shop_type"        => null,
        "instruction_offered"=> null,
        "aircraft_available"=> null,
        "school_type"        => null,
        "certificate_number"=> null,
        "address_line1"     => null,
        "address_line2"     => null,
        "postal_code"       => null
    ],
];

    
// ---------------------- SELECT RIGHT MAP ----------------------
$map = $maps[$service_type];

// ---------------------- SAFE NULL HELPER ----------------------
$safe = fn($v) => ($v === "" || $v === null) ? null : trim($v);

// ---------------------- SQL PREPARE (REMOVE city/state) ----------------------
$sql = "INSERT INTO services (
    vendor_id, service_type, instruction_rate, ground_rate, hourly_rate,
    aircraft_available, shop_type, mechanic_ratings, instruction_offered,
    ratings, phone_number, email, website, country, photos, business_name,
    description, school_type, certificate_number, postal_code, address_line1,
    address_line2
) VALUES (
    NULL, :service_type, :instruction_rate, :ground_rate, :hourly_rate,
    :aircraft_available, :shop_type, :mechanic_ratings, :instruction_offered,
    :ratings, :phone_number, :email, :website, :country, :photos, :business_name,
    :description, :school_type, :certificate_number, :postal_code, :address_line1,
    :address_line2
)";

$stmt = $pdo->prepare($sql);

$count = 0;

// ---------------------- PROCESS CSV ROWS ----------------------
// ---------------------- PROCESS CSV ROWS ----------------------
while ($row = fgetcsv($handle)) {

    $rowData = [];
    foreach ($header as $i => $h) {
        $rowData[$h] = $row[$i] ?? "";
    }

    $rowData = [];
    foreach ($header as $i => $h) {
        $rowData[$h] = $row[$i] ?? "";
    }

    // Build final object
    $d = [];
    foreach ($map as $field => $csvCol) {
        if ($field === "country") {
            // Combine city, state, country from CSV
            $city    = trim($rowData['city'] ?? "");
            $state   = trim($rowData['state'] ?? "");
            $country = trim($rowData['country'] ?? "");
            $d[$field] = implode(", ", array_filter([$city, $state, $country]));
        } else {
            $d[$field] = $csvCol !== null ? trim($rowData[strtolower($csvCol)] ?? "") : null;
        }
    }

    // Final insert
    $stmt->execute([
        ":service_type"        => $service_type,
        ":instruction_rate"    => $d["instruction_rate"],
        ":ground_rate"         => $d["ground_rate"],
        ":hourly_rate"         => $d["hourly_rate"],
        ":aircraft_available"  => $d["aircraft_available"],
        ":shop_type"           => $d["shop_type"],
        ":mechanic_ratings"    => $d["mechanic_ratings"],
        ":instruction_offered" => $d["instruction_offered"],
        ":ratings"             => $d["ratings"],
        ":phone_number"        => $d["phone_number"],
        ":email"               => $d["email"],
        ":website"             => $d["website"],
        ":country"             => $d["country"], // combined city,state,country
        ":photos"              => $d["photos"],
        ":business_name"       => $d["business_name"],
        ":description"         => $d["description"],
        ":school_type"         => $d["school_type"],
        ":certificate_number"  => $d["certificate_number"],
        ":postal_code"         => $d["postal_code"],
        ":address_line1"       => $d["address_line1"],
        ":address_line2"       => $d["address_line2"]
    ]);

    $count++;
}

fclose($handle);

echo '
<style>
.success-box {
    width: 400px;
    margin: 80px auto;
    text-align: center;
    padding: 30px;
    background-color: #ffffff;
    border-radius: 20px !important;
    padding: 20px;
    box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px;
    font-family: Arial, sans-serif;
}
.success-box .icon {
    font-size: 60px;
    color: #38a169;
}
.success-box h3 {
    margin: 10px 0 20px;
    color: #2f855a;
}
.success-box a {
    display: inline-block;
    padding: 10px 20px;
    background: #38a169;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: 0.2s;
}
.success-box a:hover {
    background: #2f855a;
}
</style>

<div class="success-box">
    <div class="icon">✔️</div>
    <h3>' . $count . ' Records Uploaded Successfully!</h3>
    <a href="bulk_upload_flight_school.php">Upload New Listing</a>
</div>
';

?>

