<?php
// Start session and include necessary files
session_start();
include "config.php";

// Fetch all orders
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];

    $updateStmt = $pdo->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE order_id = ?");
    $updateStmt->execute([$new_status, $order_id]);

    $_SESSION['success_message'] = "Order status updated successfully!";
    header("Location: admin-orders.php");
    exit();
}
include "header.php"; // Keeping the existing design
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Manage Orders</h3>
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
                        <h4 class="card-title">Order List</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Client ID</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($order['order_id']); ?></td>
                                            <td><?= htmlspecialchars($order['client_id']); ?></td>
                                            <td>$<?= htmlspecialchars($order['total_amount']); ?></td>
                                            <td>
                                                <span class="badge badge-<?= getStatusClass($order['status']); ?>">
                                                    <?= ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($order['created_at']); ?></td>
                                            <td><?= htmlspecialchars($order['updated_at']); ?></td>
                                            <td>
                                                <form method="POST" action="">
                                                    <input type="hidden" name="order_id" value="<?= $order['order_id']; ?>">
                                                    <select name="status" class="form-control">
                                                        <option value="pending" <?= ($order['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="processing" <?= ($order['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                                        <option value="shipped" <?= ($order['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                                        <option value="delivered" <?= ($order['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                                        <option value="cancelled" <?= ($order['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
                                                    <button type="submit" name="update_status" class="btn btn-primary btn-sm mt-2">Update</button>
                                                </form>
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
// Function to get Bootstrap class based on status
function getStatusClass($status) {
    switch ($status) {
        case 'pending': return 'warning';
        case 'processing': return 'info';
        case 'shipped': return 'primary';
        case 'delivered': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}
?>
