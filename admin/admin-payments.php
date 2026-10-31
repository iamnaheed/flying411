<?php
// Start session and include necessary files
session_start();
include "header.php"; // Keeping existing design
include "config.php";

error_reporting(E_ALL);
ini_set('display_errors',1);
// Fetch all payments and escrow logs with vendor details
$query = "
    SELECT p.*, e.escrow_status, e.hold_reason, e.release_date, v.business_name AS vendor_name
    FROM payments p
    LEFT JOIN escrow_logs e ON p.payment_id = e.payment_id
    LEFT JOIN vendors v ON e.vendor_id = v.vendor_id
    ORDER BY p.payment_date DESC
";

$stmt = $pdo->query($query);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle escrow release
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['release_escrow'])) {
    $payment_id = (int)$_POST['payment_id'];

    // Update escrow status to released
    $updateStmt = $pdo->prepare("UPDATE escrow_logs SET escrow_status = 'released', release_date = NOW() WHERE payment_id = ?");
    $updateStmt->execute([$payment_id]);

    // Update payment status in payments table
    $updatePaymentStmt = $pdo->prepare("UPDATE payments SET payment_status = 'escrow_released', escrow_release_date = NOW() WHERE payment_id = ?");
    $updatePaymentStmt->execute([$payment_id]);

    $_SESSION['success_message'] = "Escrow funds released successfully!";
    header("Location: admin-payments.php");
    exit();
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Manage Payments & Escrow</h3>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title">Payment Transactions</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Order ID</th>
                                        <th>Vendor</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Escrow Status</th>
                                        <th>Hold Reason</th>
                                        <th>Release Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($payment['payment_id']); ?></td>
                                            <td><?= htmlspecialchars($payment['order_id']); ?></td>
                                            <td><?= htmlspecialchars($payment['vendor_name'] ?? 'N/A'); ?></td>
                                            <td>$<?= htmlspecialchars($payment['payment_amount']); ?></td>
                                            <td><?= ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                                            <td>
                                                <span class="badge badge-<?= getStatusClass($payment['payment_status']); ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $payment['payment_status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= getEscrowClass($payment['escrow_status']); ?>">
                                                    <?= ucfirst($payment['escrow_status'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($payment['hold_reason'] ?? 'N/A'); ?></td>
                                            <td><?= htmlspecialchars($payment['release_date'] ?? 'N/A'); ?></td>
                                            <td>
                                                <?php if ($payment['escrow_status'] == 'held'): ?>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="payment_id" value="<?= $payment['payment_id']; ?>">
                                                        <button type="submit" name="release_escrow" class="btn btn-success btn-sm">Release Escrow</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>

<?php include "footer.php"; ?>

<?php
// Function to get Bootstrap class based on payment status
function getStatusClass($status) {
    switch ($status) {
        case 'pending': return 'warning';
        case 'completed': return 'success';
        case 'failed': return 'danger';
        case 'held_in_escrow': return 'primary';
        case 'escrow_released': return 'info';
        default: return 'secondary';
    }
}

// Function to get Bootstrap class based on escrow status
function getEscrowClass($status) {
    switch ($status) {
        case 'held': return 'warning';
        case 'released': return 'success';
        case 'disputed': return 'danger';
        case 'cancelled': return 'secondary';
        default: return 'secondary';
    }
}
?>
