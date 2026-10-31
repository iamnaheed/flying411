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
    $shop_type = isset($_POST['shop_type']) ? implode(", ", $_POST['shop_type']) : NULL;
    $mechanic_ratings = isset($_POST['mechanic_ratings']) ? implode(", ", $_POST['mechanic_ratings']) : NULL;
    $instruction_offered = isset($_POST['instruction_offered']) ? implode(", ", $_POST['instruction_offered']) : NULL;
    $ratings = isset($_POST['ratings']) ? implode(", ", $_POST['ratings']) : NULL;
    $phone_number = $_POST['phone_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $website = $_POST['website'] ?? '';
    $country = $_POST['country'] ?? '';

    // Update service data
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
        country=:country 
        WHERE id=:id");

    $update_query->bindParam(":service_type", $service_type);
    $update_query->bindParam(":instruction_rate", $instruction_rate);
    $update_query->bindParam(":ground_rate", $ground_rate);
    $update_query->bindParam(":hourly_rate", $hourly_rate);
    $update_query->bindParam(":aircraft_available", $aircraft_available);
    $update_query->bindParam(":shop_type", $shop_type);
    $update_query->bindParam(":mechanic_ratings", $mechanic_ratings);
    $update_query->bindParam(":instruction_offered", $instruction_offered);
    $update_query->bindParam(":ratings", $ratings);
    $update_query->bindParam(":phone_number", $phone_number);
    $update_query->bindParam(":email", $email);
    $update_query->bindParam(":website", $website);
    $update_query->bindParam(":country", $country);
    $update_query->bindParam(":id", $id, PDO::PARAM_INT);

    if ($update_query->execute()) {
        echo "<script>alert('Service updated successfully!'); window.location.href='services_list.php';</script>";
    } else {
        echo "<script>alert('Update failed!');</script>";
    }
}
?>

<div class="container mt-4">
    <h2>Edit Service</h2>
    <form method="POST" id="serviceForm">
        <div class="mb-3">
            <label for="service_type" class="form-label">Service Type</label>
            <select class="form-control" id="service_type" name="service_type">
                <option value="">Select Service Type</option>
                <option value="Flight Instructor" <?= ($service['service_type'] == "Flight Instructor") ? "selected" : "" ?>>Flight Instructor</option>
                <option value="Flight School" <?= ($service['service_type'] == "Flight School") ? "selected" : "" ?>>Flight School</option>
                <option value="Engine Shop" <?= ($service['service_type'] == "Engine Shop") ? "selected" : "" ?>>Engine Shop</option>
                <option value="Avionics Shop" <?= ($service['service_type'] == "Avionics Shop") ? "selected" : "" ?>>Avionics Shop</option>
                <option value="Maintenance Shop" <?= ($service['service_type'] == "Maintenance Shop") ? "selected" : "" ?>>Maintenance Shop</option>
                <option value="Local Mechanic" <?= ($service['service_type'] == "Local Mechanic") ? "selected" : "" ?>>Local Mechanic</option>
            </select>
        </div>

        <div id="dynamicFields">
            <!-- Dynamic fields will be inserted here based on service type -->
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
            <select class="form-control" id="country" name="country">
                <option value="">Select Country</option>
                <?php foreach ($countries as $c) { ?>
                    <option value="<?= htmlspecialchars($c['name']) ?>" <?= ($service['country'] == $c['name']) ? "selected" : "" ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php } ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Service</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $("#service_type").change(function(){
        var serviceType = $(this).val();
        $.ajax({
            url: "fetch_fields.php",
            method: "POST",
            data: { service_type: serviceType },
            success: function(response){
                $("#dynamicFields").html(response);
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>
