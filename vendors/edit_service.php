<?php
include 'config.php';
include 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Validate service ID
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid Service ID.");
}

// Fetch service data
$query = $pdo->prepare("SELECT * FROM services WHERE id = :id");
$query->bindParam(":id", $id, PDO::PARAM_INT);
$query->execute();
$service = $query->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    die("Service not found.");
}

// Fetch country data from API
$api_url = "https://countriesnow.space/api/v0.1/countries/states";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);
$countries = $data['data'] ?? [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_type = $_POST['service_type'] ?? '';
    $instruction_rate = !empty($_POST['instruction_rate']) ? $_POST['instruction_rate'] : NULL;
    $ground_rate = !empty($_POST['ground_rate']) ? $_POST['ground_rate'] : NULL;
    $hourly_rate = !empty($_POST['hourly_rate']) ? $_POST['hourly_rate'] : NULL;
    $aircraft_available = $_POST['aircraft_available'] ?? NULL;
    $shop_type = isset($_POST['shop_type']) ? json_encode($_POST['shop_type']) : NULL;
    $mechanic_ratings = isset($_POST['mechanic_ratings']) ? json_encode($_POST['mechanic_ratings']) : NULL;
    $instruction_offered = isset($_POST['instruction_offered']) ? json_encode($_POST['instruction_offered']) : NULL;
    $ratings = isset($_POST['ratings']) ? json_encode($_POST['ratings']) : NULL;
    $phone_number = $_POST['phone_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $website = $_POST['website'] ?? '';
    $country = $_POST['country'] ?? '';
    $business_name = $_POST['business_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $school_type = $_POST['school_type'] ?? NULL;
    $certificate_number = $_POST['certificate_number'] ?? NULL;

    // Handle photo uploads
    $photos = json_decode($service['photos'], true) ?: [];
    if (!empty($_FILES['photos']['name'][0])) {
        $uploads_dir = 'uploads/';
        if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0777, true);

        foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['photos']['error'][$key] == UPLOAD_ERR_OK) {
                $file_ext = strtolower(pathinfo($_FILES['photos']['name'][$key], PATHINFO_EXTENSION));
                if (in_array($file_ext, ['jpg','jpeg','png','gif','svg'])) {
                    $file_name = uniqid() . '_' . basename($_FILES['photos']['name'][$key]);
                    $target_path = $uploads_dir . $file_name;
                    move_uploaded_file($tmp_name, $target_path);
                    $photos[] = $file_name;
                }
            }
        }
    }

    $photos_json = !empty($photos) ? json_encode($photos) : NULL;

    // Update service
    $update_query = $pdo->prepare("UPDATE services SET 
        service_type=:service_type,
        instruction_rate=:instruction_rate,
        ground_rate=:ground_rate,
        hourly_rate=:hourly_rate,
        aircraft_available=:aircraft_available,
        shop_type=:shop_type,
        mechanic_ratings=:mechanic_ratings,
        instruction_offered=:instruction_offered,
        ratings=:ratings,
        phone_number=:phone_number,
        email=:email,
        website=:website,
        country=:country,
        business_name=:business_name,
        description=:description,
        school_type=:school_type,
        certificate_number=:certificate_number,
        photos=:photos
        WHERE id=:id");

    $update_query->execute([
        ':service_type'=>$service_type,
        ':instruction_rate'=>$instruction_rate,
        ':ground_rate'=>$ground_rate,
        ':hourly_rate'=>$hourly_rate,
        ':aircraft_available'=>$aircraft_available,
        ':shop_type'=>$shop_type,
        ':mechanic_ratings'=>$mechanic_ratings,
        ':instruction_offered'=>$instruction_offered,
        ':ratings'=>$ratings,
        ':phone_number'=>$phone_number,
        ':email'=>$email,
        ':website'=>$website,
        ':country'=>$country,
        ':business_name'=>$business_name,
        ':description'=>$description,
        ':school_type'=>$school_type,
        ':certificate_number'=>$certificate_number,
        ':photos'=>$photos_json,
        ':id'=>$id
    ]);

    echo "<script>alert('Service updated successfully!'); window.location.href='list_of_services.php';</script>";
}
?>

<div class="container mt-4">
    <h2>Edit Service</h2>
    <form method="POST" enctype="multipart/form-data" id="serviceForm">
        <div class="mb-3">
            <label for="service_type" class="form-label">Service Type</label>
            <select class="form-control" id="service_type" name="service_type" required>
                <option value="">Select Service Type</option>
                <?php
                $types = ['Flight Instructor','Flight School','Engine Shop','Avionics Shop','Maintenance Shop','Local Mechanic'];
                foreach($types as $type) {
                    $selected = ($service['service_type']==$type) ? 'selected' : '';
                    echo "<option value='$type' $selected>$type</option>";
                }
                ?>
            </select>
        </div>

        <div id="dynamicFields">
            <?php
            // Preload dynamic fields based on existing service type
            $service_type = $service['service_type'];
            include 'fetch_fields.php'; // Same file used in insert form
            ?>
        </div>

        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($service['phone_number']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($service['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="website" class="form-label">Website</label>
            <input type="text" class="form-control" id="website" name="website" value="<?= htmlspecialchars($service['website']) ?>">
        </div>

        <div class="mb-3">
            <label for="country" class="form-label">Country</label>
            <input type="text" class="form-control" id="country" name="country" value="<?= htmlspecialchars($service['country']) ?>">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($service['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>Existing Photos</label><br>
            <?php if(!empty($service['photos'])): 
                $photos = json_decode($service['photos'], true);
                foreach($photos as $p){
                    echo "<img src='uploads/$p' style='height:80px;margin:5px;'>";
                }
            endif; ?>
        </div>

        <div class="mb-3">
            <label>Upload New Photos (Optional)</label>
            <input type="file" name="photos[]" class="form-control" multiple>
        </div>

        <button type="submit" class="btn" style="background-color:#4747a1; color:#fff;">Update Service</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    function loadDynamicFields(serviceType){
        $.ajax({
            url: "fetch_update_fields.php",
            method: "POST",
            data: { service_type: serviceType, service_id: <?= $id ?> },
            success: function(response){
                $("#dynamicFields").html(response);
            }
        });
    }

    $("#service_type").change(function(){
        loadDynamicFields($(this).val());
    });
});
</script>

<?php include 'footer.php'; ?>
