<?php
session_start();
require 'config.php';

// Check vendor authentication
if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch all services for this vendor
$stmt = $pdo->prepare("SELECT * FROM services WHERE vendor_id = :vendor_id ORDER BY created_at DESC");
$stmt->execute([':vendor_id' => $vendor_id]);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "header.php";
?>

<div class="main-panel">
    <div class="container mt-4">
        <h2 class="mb-3">All Services</h2>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Service Listings</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Vendor</th>
                                <th>Type</th>
                                <th>Country</th>
                                <th>Rates</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Website</th>
                                <th>Aircraft Available</th>
                                <th>Shop Type</th>
                                <th>Mechanic Ratings</th>
                                <th>Instruction Offered</th>
                                <th>Overall Ratings</th>
                                <th>Photos</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($services)): ?>
                                <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($service['business_name']); ?></td>
                                        <td><?= htmlspecialchars($service['service_type']); ?></td>
                                        <td><?= htmlspecialchars($service['country']); ?></td>
                                        <td>
                                            <?php
                                            $rates = [];
                                            if (!empty($service['instruction_rate'])) {
                                                $rates[] = "Instruction: $" . number_format($service['instruction_rate'], 2);
                                            }
                                            if (!empty($service['ground_rate'])) {
                                                $rates[] = "Ground: $" . number_format($service['ground_rate'], 2);
                                            }
                                            if (!empty($service['hourly_rate'])) {
                                                $rates[] = "Hourly: $" . number_format($service['hourly_rate'], 2);
                                            }
                                            echo implode("<br>", $rates);
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($service['phone_number']); ?></td>
                                        <td><?= htmlspecialchars($service['email']); ?></td>
                                        <td>
                                            <?php if (!empty($service['website'])): ?>
                                                <a href="<?= htmlspecialchars($service['website']); ?>" target="_blank">Visit</a>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($service['aircraft_available']); ?></td>
                                        <td><?= htmlspecialchars(json_decode($service['shop_type'], true) ? implode(', ', json_decode($service['shop_type'], true)) : 'N/A'); ?></td>
                                        <td><?= htmlspecialchars(json_decode($service['mechanic_ratings'], true) ? implode(', ', json_decode($service['mechanic_ratings'], true)) : 'N/A'); ?></td>
                                        <td><?= htmlspecialchars(json_decode($service['instruction_offered'], true) ? implode(', ', json_decode($service['instruction_offered'], true)) : 'N/A'); ?></td>
                                        <td><?= htmlspecialchars(json_decode($service['ratings'], true) ? implode(', ', json_decode($service['ratings'], true)) : 'N/A'); ?></td>
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
                                            <!--<a href="edit_service.php?id=<?= $service['id']; ?>" class="btn btn-sm btn-warning">Edit</a>-->
                                            <a href="delete_service.php?id=<?= $service['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this service?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="14" class="text-center text-muted">No services found.</td>
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