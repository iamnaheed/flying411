<?php
session_start();
include "config.php";

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Validate ID
if (!isset($_GET['id'])) {
    header("Location: manage-services.php");
    exit;
}

$service_id = $_GET['id'];

// Fetch Service Details
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if service not found
if (!$service) {
    echo "<h3>Service not found</h3>";
    exit;
}

include "header.php";
?>

<style>
.service-img {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #ddd;
}
.details-row {
    margin-bottom: 10px;
}
.details-label {
    font-weight: bold;
}
</style>

<div class="main-panel">
<div class="content-wrapper">

    <h3>View Service Details</h3>
    <a href="services-listing.php?vendor_id=<?= $service['vendor_id']; ?>" class="btn btn-secondary btn-sm mb-3">
        ← Back to Services
    </a>

    <div class="card p-4">

        <div class="row mb-4">
            <div class="col-md-4">
                <?php 
                $photoPath = "../vendors/" . $service['photos'];
                if (!empty($service['photos']) && file_exists($photoPath)): ?>
                    <img src="<?= $photoPath ?>" class="service-img">
                <?php else: ?>
                    <div class="text-muted">No Photo Available</div>
                <?php endif; ?>
            </div>

            <div class="col-md-8">
                <h4><?= htmlspecialchars($service['business_name']); ?></h4>
                <p><?= htmlspecialchars($service['description']); ?></p>
            </div>
        </div>

        <hr>

        <h4>Service Information</h4>

        <div class="row details-row">
            <div class="col-md-4 details-label">Service Type:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['service_type']); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">Instruction Rate:</div>
            <div class="col-md-8">$<?= number_format($service['instruction_rate'], 2); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">Ground Rate:</div>
            <div class="col-md-8">$<?= number_format($service['ground_rate'], 2); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">Hourly Rate:</div>
            <div class="col-md-8">$<?= number_format($service['hourly_rate'], 2); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">Aircraft Available:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['aircraft_available']); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">Instruction Offered:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['instruction_offered']); ?></div>
        </div>

        <hr>

        <h4>Ratings & Reviews</h4>

        <div class="row details-row">
            <div class="col-md-4 details-label">Mechanic Ratings:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['mechanic_ratings']); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">Ratings:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['ratings']); ?></div>
        </div>

        <hr>

        <h4>Business & Contact Info</h4>

        <div class="row details-row">
            <div class="col-md-4 details-label">Phone Number:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['phone_number']); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">Email:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['email']); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">Website:</div>
            <div class="col-md-8">
                <?php if (!empty($service['website'])): ?>
                    <a href="<?= htmlspecialchars($service['website']); ?>" target="_blank">Visit Website</a>
                <?php else: ?>
                    <span class="text-muted">N/A</span>
                <?php endif; ?>
            </div>
        </div>

        <hr>

        <h4>Location Information</h4>

        <div class="row details-row">
            <div class="col-md-4 details-label">Country:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['country']); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">Postal Code:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['postal_code']); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">Address Line 1:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['address_line1']); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">Address Line 2:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['address_line2']); ?></div>
        </div>

        <hr>

        <h4>Additional Information</h4>

        <div class="row details-row">
            <div class="col-md-4 details-label">Shop Type:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['shop_type']); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">School Type:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['school_type']); ?></div>
        </div>

        <div class="row details-row">
            <div class="col-md-4 details-label">Certificate Number:</div>
            <div class="col-md-8"><?= htmlspecialchars($service['certificate_number']); ?></div>
        </div>

        <hr>

        <h4>Created At</h4>
        <p><?= date("F d, Y h:i A", strtotime($service['created_at'])); ?></p>

    </div>

</div>
</div>

<?php include "footer.php"; ?>
