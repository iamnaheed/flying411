<?php
session_start();
require_once 'config.php'; // Database connection
require_once 'header.php'; // Admin header file

// Fetch all discount coupons
$query = "
    SELECT d.*, v.business_name 
    FROM discount_coupons d
    LEFT JOIN vendors v ON d.vendor_id = v.vendor_id
    ORDER BY d.expiration_date DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="mt-4">Manage Discounts</h4>
            <a href="add-discount.php" class="btn btn-success mb-3">Add New Coupon</a>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Coupon ID</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Discount Amount</th>
                                <th>Expiration Date</th>
                                <th>Vendor</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coupons as $coupon): ?>
                                <tr>
                                    <td><?= htmlspecialchars($coupon['coupon_id']); ?></td>
                                    <td><?= htmlspecialchars($coupon['code']); ?></td>
                                    <td><?= htmlspecialchars($coupon['description'] ?? 'N/A'); ?></td>
                                    <td>$<?= number_format($coupon['discount_amount'], 2); ?></td>
                                    <td><?= $coupon['expiration_date'] ? date('Y-m-d', strtotime($coupon['expiration_date'])) : 'No Expiry'; ?></td>
                                    <td><?= htmlspecialchars($coupon['business_name'] ?? 'Admin'); ?></td>
                                    <td>
                                        <span class="badge <?= ($coupon['status'] == 'active') ? 'badge-success' : 'badge-danger'; ?>">
                                            <?= ucfirst($coupon['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit-discount.php?id=<?= $coupon['coupon_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                        <a href="delete-discount.php?id=<?= $coupon['coupon_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this coupon?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($coupons)): ?>
                                <tr><td colspan="8" class="text-center">No discount coupons available.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
