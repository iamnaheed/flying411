<?php
session_start();
include "config.php";

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Validate service ID
if (!isset($_GET['id'])) {
    header("Location: manage-services.php");
    exit;
}

$service_id = $_GET['id'];

// Fetch existing service
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    echo "<h3>Service not found</h3>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect POST data safely
    $business_name       = $_POST['business_name'] ?? '';
    $service_type        = $_POST['service_type'] ?? '';
    $instruction_rate    = $_POST['instruction_rate'] ?? 0;
    $ground_rate         = $_POST['ground_rate'] ?? 0;
    $hourly_rate         = $_POST['hourly_rate'] ?? 0;
    $aircraft_available  = $_POST['aircraft_available'] ?? '';
    $shop_type           = $_POST['shop_type'] ?? '';
    $mechanic_ratings    = $_POST['mechanic_ratings'] ?? '';
    $instruction_offered = $_POST['instruction_offered'] ?? '';
    $ratings             = $_POST['ratings'] ?? '';
    $phone_number        = $_POST['phone_number'] ?? '';
    $email               = $_POST['email'] ?? '';
    $website             = $_POST['website'] ?? '';
    $country             = $_POST['country'] ?? '';
    $description         = $_POST['description'] ?? '';
    $school_type         = $_POST['school_type'] ?? '';
    $certificate_number  = $_POST['certificate_number'] ?? '';
    $postal_code         = $_POST['postal_code'] ?? '';
    $address_line1       = $_POST['address_line1'] ?? '';
    $address_line2       = $_POST['address_line2'] ?? '';

    // Handle photo upload
    $photo_name = $service['photos']; // default existing photo
    if (isset($_FILES['photos']) && $_FILES['photos']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../vendors/";
        $tmpName   = $_FILES['photos']['tmp_name'];
        $ext       = pathinfo($_FILES['photos']['name'], PATHINFO_EXTENSION);
        $newName   = "service_" . time() . "." . $ext;

        // Delete old photo if exists
        if (!empty($service['photos']) && file_exists($uploadDir . $service['photos'])) {
            unlink($uploadDir . $service['photos']);
        }

        move_uploaded_file($tmpName, $uploadDir . $newName);
        $photo_name = $newName;
    }

    // Update service
    $updateStmt = $pdo->prepare("
        UPDATE services SET
            business_name       = ?,
            service_type        = ?,
            instruction_rate    = ?,
            ground_rate         = ?,
            hourly_rate         = ?,
            aircraft_available  = ?,
            shop_type           = ?,
            mechanic_ratings    = ?,
            instruction_offered = ?,
            ratings             = ?,
            phone_number        = ?,
            email               = ?,
            website             = ?,
            country             = ?,
            photos              = ?,
            description         = ?,
            school_type         = ?,
            certificate_number  = ?,
            postal_code         = ?,
            address_line1       = ?,
            address_line2       = ?
        WHERE id = ?
    ");

    $updateStmt->execute([
        $business_name,
        $service_type,
        $instruction_rate,
        $ground_rate,
        $hourly_rate,
        $aircraft_available,
        $shop_type,
        $mechanic_ratings,
        $instruction_offered,
        $ratings,
        $phone_number,
        $email,
        $website,
        $country,
        $photo_name,
        $description,
        $school_type,
        $certificate_number,
        $postal_code,
        $address_line1,
        $address_line2,
        $service_id
    ]);

    header("Location: view-service.php?id=$service_id");
    exit;
}

include "header.php";
?>

<style>
.service-img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #ddd;
    margin-bottom: 10px;
}
</style>

<div class="main-panel">
<div class="content-wrapper">

    <h3>Edit Service</h3>
    <a href="services-listing.php?vendor_id=<?= $service['vendor_id']; ?>" class="btn btn-secondary btn-sm mb-3">
        ← Back to Services
    </a>

    <div class="card p-4">
        <form method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label>Business Name</label>
                <input type="text" name="business_name" class="form-control" required
                       value="<?= htmlspecialchars($service['business_name']); ?>">
            </div>

            <div class="form-group">
                <label>Service Type</label>
                <input type="text" name="service_type" class="form-control"
                       value="<?= htmlspecialchars($service['service_type']); ?>">
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Instruction Rate</label>
                    <input type="number" step="0.01" name="instruction_rate" class="form-control"
                           value="<?= htmlspecialchars($service['instruction_rate']); ?>">
                </div>
                <div class="form-group col-md-4">
                    <label>Ground Rate</label>
                    <input type="number" step="0.01" name="ground_rate" class="form-control"
                           value="<?= htmlspecialchars($service['ground_rate']); ?>">
                </div>
                <div class="form-group col-md-4">
                    <label>Hourly Rate</label>
                    <input type="number" step="0.01" name="hourly_rate" class="form-control"
                           value="<?= htmlspecialchars($service['hourly_rate']); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Aircraft Available</label>
                <input type="text" name="aircraft_available" class="form-control"
                       value="<?= htmlspecialchars($service['aircraft_available']); ?>">
            </div>

            <div class="form-group">
                <label>Instruction Offered</label>
                <input type="text" name="instruction_offered" class="form-control"
                       value="<?= htmlspecialchars($service['instruction_offered']); ?>">
            </div>

            <div class="form-group">
                <label>Shop Type</label>
                <input type="text" name="shop_type" class="form-control"
                       value="<?= htmlspecialchars($service['shop_type']); ?>">
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Mechanic Ratings</label>
                    <input type="text" name="mechanic_ratings" class="form-control"
                           value="<?= htmlspecialchars($service['mechanic_ratings']); ?>">
                </div>
                <div class="form-group col-md-6">
                    <label>Ratings</label>
                    <input type="text" name="ratings" class="form-control"
                           value="<?= htmlspecialchars($service['ratings']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" class="form-control"
                           value="<?= htmlspecialchars($service['phone_number']); ?>">
                </div>
                <div class="form-group col-md-6">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($service['email']); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Website</label>
                <input type="text" name="website" class="form-control"
                       value="<?= htmlspecialchars($service['website']); ?>">
            </div>

            <div class="form-group">
                <label>Country</label>
                <input type="text" name="country" class="form-control"
                       value="<?= htmlspecialchars($service['country']); ?>">
            </div>

            <div class="form-group">
                <label>School Type</label>
                <input type="text" name="school_type" class="form-control"
                       value="<?= htmlspecialchars($service['school_type']); ?>">
            </div>

            <div class="form-group">
                <label>Certificate Number</label>
                <input type="text" name="certificate_number" class="form-control"
                       value="<?= htmlspecialchars($service['certificate_number']); ?>">
            </div>

            <div class="form-group">
                <label>Postal Code</label>
                <input type="text" name="postal_code" class="form-control"
                       value="<?= htmlspecialchars($service['postal_code']); ?>">
            </div>

            <div class="form-group">
                <label>Address Line 1</label>
                <input type="text" name="address_line1" class="form-control"
                       value="<?= htmlspecialchars($service['address_line1']); ?>">
            </div>

            <div class="form-group">
                <label>Address Line 2</label>
                <input type="text" name="address_line2" class="form-control"
                       value="<?= htmlspecialchars($service['address_line2']); ?>">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($service['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Photo</label><br>
                <?php if (!empty($service['photos']) && file_exists("../vendors/" . $service['photos'])): ?>
                    <img src="../vendors/<?= $service['photos']; ?>" class="service-img"><br>
                <?php endif; ?>
                <input type="file" name="photos" class="form-control-file mt-2">
                <small class="text-muted">Upload new photo to replace existing</small>
            </div>

            <button type="submit" class="btn btn-primary">Update Service</button>
        </form>
    </div>

</div>
</div>

<?php include "footer.php"; ?>
