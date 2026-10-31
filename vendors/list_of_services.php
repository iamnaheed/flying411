<?php
session_start();
require 'config.php';

if (!isset($_SESSION['vendor_id'])) {
   header("Location: ../login.php");
   exit;
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch all services for this vendor
$stmt = $pdo->prepare("SELECT * FROM services WHERE vendor_id = :vendor_id ORDER BY created_at DESC");
$stmt->execute([':vendor_id' => $vendor_id]);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "header.php";
?>
<style>
.btn-primary {
    background-color: #4747A1;
    color: white;
}
</style>

<div class="main-panel">
    <div class="container mt-4">
        <h3 class="font-weight-bold">All Services</h3>
        <h6 class="font-weight-normal mb-3">Manage all your services.</h6>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Vendor</th>
                                <th>Type</th>
                                <th>Country</th>
                                <th>Instruction Rate</th>
                                <th>Ground Rate</th>
                                <th>Hourly Rate</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Website</th>
                                <th>Aircraft Available</th>
                                <th>Shop Type</th>
                                <th>Mechanic Ratings</th>
                                <th>Instruction Offered</th>
                                <th>Overall Ratings</th>
                                <th>School Type</th>
                                <th>Certificate #</th>
                                <th>Description</th>
                                <th>Photos</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td><?= htmlspecialchars($service['business_name'] ?: 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($service['service_type'] ?: 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($service['country'] ?: 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($service['instruction_rate'] ?: 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($service['ground_rate'] ?: 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($service['hourly_rate'] ?: 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($service['phone_number'] ?: 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($service['email'] ?: 'N/A'); ?></td>
                                    <td>
                                        <?php
                                        if (!empty($service['website'])) {
                                            $url = htmlspecialchars($service['website']);
                                            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) $url = "http://" . $url;
                                            echo "<a href='{$url}' target='_blank'>Visit</a>";
                                        } else {
                                            echo "N/A";
                                        }
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($service['aircraft_available'] ?: 'N/A'); ?></td>
                                    <td><?= htmlspecialchars(json_decode($service['shop_type'], true) ? implode(', ', json_decode($service['shop_type'], true)) : 'N/A'); ?></td>
                                    <td><?= htmlspecialchars(json_decode($service['mechanic_ratings'], true) ? implode(', ', json_decode($service['mechanic_ratings'], true)) : 'N/A'); ?></td>
                                    <td><?= htmlspecialchars(json_decode($service['instruction_offered'], true) ? implode(', ', json_decode($service['instruction_offered'], true)) : 'N/A'); ?></td>
                                    <td><?= htmlspecialchars(json_decode($service['ratings'], true) ? implode(', ', json_decode($service['ratings'], true)) : 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($service['school_type'] ?: 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($service['certificate_number'] ?: 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($service['description'] ?: 'N/A'); ?></td>
                                    <td>
                                        <?php
                                        $photos = json_decode($service['photos'], true);
                                        if (!empty($photos)) {
                                            foreach ($photos as $photo) {
                                                echo "<img src='uploads/" . htmlspecialchars($photo) . "' width='50' height='50' class='mr-1'>";
                                            }
                                        } else {
                                            echo "No Photos";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="edit_service.php?id=<?= $service['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="delete_service.php?id=<?= $service['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="17" class="text-center">No services found.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>
