<?php
session_start();
require_once 'config.php'; // Database connection
require_once 'header.php'; // Admin header file

// Fetch shipping details
$query = "
    SELECT s.*, o.order_id AS order_number 
    FROM shipping s
    LEFT JOIN orders o ON s.order_id = o.order_id
    ORDER BY s.shipping_date DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="main-panel">
<div class="container-fluid content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="mt-4">Manage Shipping</h4>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Shipping ID</th>
                                <th>Order ID</th>
                                <th>Tracking Number</th>
                                <th>Shipping Company</th>
                                <th>Status</th>
                                <th>Shipping Date</th>
                                <th>Delivered Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shipments as $shipment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($shipment['shipping_id']); ?></td>
                                    <td><?= htmlspecialchars($shipment['order_number']); ?></td>
                                    <td><?= htmlspecialchars($shipment['tracking_number'] ?? 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($shipment['shipping_company'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?= ($shipment['shipping_status'] == 'pending') ? 'badge-warning' :
                                                (($shipment['shipping_status'] == 'shipped') ? 'badge-primary' :
                                                (($shipment['shipping_status'] == 'delivered') ? 'badge-success' :
                                                'badge-danger')); ?>">
                                            <?= htmlspecialchars(ucwords($shipment['shipping_status'])); ?>
                                        </span>
                                    </td>
                                    <td><?= $shipment['shipping_date'] ? date('Y-m-d', strtotime($shipment['shipping_date'])) : 'N/A'; ?></td>
                                    <td><?= $shipment['delivered_date'] ? date('Y-m-d', strtotime($shipment['delivered_date'])) : 'N/A'; ?></td>
                                    <td>
                                        <?php if ($shipment['shipping_status'] != 'delivered' && $shipment['shipping_status'] != 'cancelled'): ?>
                                            <a href="process-shipping.php?action=shipped&id=<?= $shipment['shipping_id']; ?>" class="btn btn-primary btn-sm">Mark as Shipped</a>
                                            <a href="process-shipping.php?action=delivered&id=<?= $shipment['shipping_id']; ?>" class="btn btn-success btn-sm">Mark as Delivered</a>
                                            <a href="process-shipping.php?action=cancelled&id=<?= $shipment['shipping_id']; ?>" class="btn btn-danger btn-sm">Cancel</a>
                                        <?php else: ?>
                                            <span class="text-muted">Finalized</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($shipments)): ?>
                                <tr><td colspan="8" class="text-center">No shipping records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php require_once 'includes/footer.php'; ?>
