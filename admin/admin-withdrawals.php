<?php
session_start();
include  'config.php'; // Database connection
include 'header.php'; // Admin header file

// Fetch withdrawal requests with vendor details
$query = "
    SELECT w.*, v.business_name AS vendor_name, v.bank_name, v.account_number, v.iban, v.swift_code 
    FROM vendor_withdrawals w
    LEFT JOIN vendors v ON w.vendor_id = v.vendor_id
    ORDER BY w.request_date DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="mt-4">Manage Vendor Withdrawals</h4>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vendor</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Request Date</th>
                                <th>Approval Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($withdrawals as $withdrawal): ?>
                                <tr>
                                    <td><?= htmlspecialchars($withdrawal['withdrawal_id']); ?></td>
                                    <td><?= htmlspecialchars($withdrawal['vendor_name']); ?></td>
                                    <td>$<?= number_format($withdrawal['amount'], 2); ?></td>
                                    <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $withdrawal['payment_method']))); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?= $withdrawal['status'] == 'pending' ? 'badge-warning' : ($withdrawal['status'] == 'approved' ? 'badge-success' : 'badge-danger'); ?>">
                                            <?= htmlspecialchars($withdrawal['status']); ?>
                                        </span>
                                    </td>
                                    <td><?= date('Y-m-d H:i:s', strtotime($withdrawal['request_date'])); ?></td>
                                    <td>
                                        <?= $withdrawal['approval_date'] ? date('Y-m-d H:i:s', strtotime($withdrawal['approval_date'])) : 'N/A'; ?>
                                    </td>
                                    <td>
                                        <?php if ($withdrawal['status'] == 'pending'): ?>
                                            <a href="process-withdrawal.php?action=approve&id=<?= $withdrawal['withdrawal_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                                            <a href="process-withdrawal.php?action=deny&id=<?= $withdrawal['withdrawal_id']; ?>" class="btn btn-danger btn-sm">Deny</a>
                                        <?php else: ?>
                                            <span class="text-muted">Processed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($withdrawals)): ?>
                                <tr><td colspan="8" class="text-center">No withdrawal requests found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
