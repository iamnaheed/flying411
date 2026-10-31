<?php
session_start();
include "config.php";

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get Vendor ID
if (!isset($_GET['vendor_id'])) {
    header("Location: manage-services.php");
    exit;
}

$vendor_id = $_GET['vendor_id'];

// Fetch Vendor Details
$stmt = $pdo->prepare("SELECT business_name, business_email FROM vendors WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

// Delete Service
if (isset($_GET['delete_id'])) {
    $service_id = $_GET['delete_id'];

    // Delete service photo
    $stmtImg = $pdo->prepare("SELECT photos FROM services WHERE id = ?");
    $stmtImg->execute([$service_id]);
    $servicePhoto = $stmtImg->fetchColumn();

    if ($servicePhoto && file_exists("../vendors/$servicePhoto")) {
        unlink("../vendors/$servicePhoto");
    }

    // Delete service record
    $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$service_id]);

    header("Location: services-listing.php?vendor_id=$vendor_id");
    exit;
}

// Fetch All Services
$stmt = $pdo->prepare("
    SELECT *
    FROM services
    WHERE vendor_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$vendor_id]);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "header.php";
?>

<style>
.table-img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 6px;
}
</style>

<div class="main-panel">
<div class="content-wrapper">

    <h3>Services by <?= htmlspecialchars($vendor['business_name']); ?></h3>
    <h6>Email: <?= htmlspecialchars($vendor['business_email']); ?></h6>

    <hr>

    <h4 class="mt-4">Service Listings</h4>

    <div class="table-responsive">
    <table class="table table-striped table-bordered w-100">
        <thead>
            <tr>
                <th>Photo</th>
                <th>Business Name</th>
                <th>Service Type</th>
                <th>Instruction Rate</th>
                <th>Ground Rate</th>
                <th>Hourly Rate</th>
                <th>Phone</th>
                <th>Country</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        <?php if (!empty($services)): ?>
            <?php foreach ($services as $srv): ?>
            <tr>

                <!-- Photo -->
                <td>
                    <?php 
                    $photoPath = "../vendors/" . $srv['photos'];
                    if (!empty($srv['photos']) && file_exists($photoPath)): ?>
                        <img src="<?= $photoPath ?>" class="table-img">
                    <?php else: ?>
                        <span class="text-muted">No Photo</span>
                    <?php endif; ?>
                </td>

                <td><?= htmlspecialchars($srv['business_name']); ?></td>

                <td><?= htmlspecialchars($srv['service_type']); ?></td>

                <td>
                    <?= $srv['instruction_rate'] ? "$" . number_format($srv['instruction_rate'], 2) : "N/A"; ?>
                </td>

                <td>
                    <?= $srv['ground_rate'] ? "$" . number_format($srv['ground_rate'], 2) : "N/A"; ?>
                </td>

                <td>
                    <?= $srv['hourly_rate'] ? "$" . number_format($srv['hourly_rate'], 2) : "N/A"; ?>
                </td>

                <td><?= htmlspecialchars($srv['phone_number']); ?></td>

                <td><?= htmlspecialchars($srv['country']); ?></td>

                <td><?= date("M d, Y", strtotime($srv['created_at'])); ?></td>

                <td>

                    <a href="view-service.php?id=<?= $srv['id']; ?>" 
                       class="btn btn-primary btn-sm">
                       View
                    </a>

                    <a href="edit-service.php?id=<?= $srv['id']; ?>" 
                       class="btn btn-warning btn-sm">
                       Edit
                    </a>

                    <a href="?delete_id=<?= $srv['id']; ?>&vendor_id=<?= $vendor_id; ?>"
                       onclick="return confirm('Delete this service?');"
                       class="btn btn-danger btn-sm">
                       Delete
                    </a>

                </td>

            </tr>
            <?php endforeach; ?>

        <?php else: ?>
            <tr>
                <td colspan="10" class="text-center text-muted">
                    No services found for this vendor.
                </td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>
    </div>

</div>
</div>

<?php include "footer.php"; ?>
