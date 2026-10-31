<?php
session_start();
include "header.php";
include "config.php"; // Database connection

// Check if vendor_id is set in the session
if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch orders for the logged-in vendor
$query = $pdo->prepare("
    SELECT 
        o.order_id,
        o.status AS order_status,
        o.total_amount AS total_price,
        c.name AS customer_name,
        c.email AS customer_email,
        GROUP_CONCAT(
            CASE 
                WHEN oi.product_type = 'aircraft' THEN ac.model
                WHEN oi.product_type = 'part' THEN pt.part_number
                WHEN oi.product_type = 'engine' THEN en.model
                ELSE 'Unknown Product'
            END 
            SEPARATOR ', '
        ) AS products
    FROM orders o
    JOIN clients c ON o.client_id = c.client_id
    JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN aircrafts ac ON oi.product_id = ac.aircraft_id AND oi.product_type = 'aircraft'
    LEFT JOIN parts pt ON oi.product_id = pt.part_id AND oi.product_type = 'part'
    LEFT JOIN engines en ON oi.product_id = en.engine_id AND oi.product_type = 'engine'
    WHERE (ac.vendor_id = ? OR pt.vendor_id = ? OR en.vendor_id = ?)
    GROUP BY o.order_id
");
$query->execute([$vendor_id, $vendor_id, $vendor_id]);
$orders = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Manage Orders</h3>
        <h6 class="font-weight-normal mb-0">Track and manage your customer orders.</h6>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Order List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Products</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                      <tr>
                        <td>#<?= htmlspecialchars($order['order_id']); ?></td>
                        <td>
                          <?= htmlspecialchars(substr($order['customer_name'], 0, 1)); ?>**** 
                          (<?= htmlspecialchars(substr($order['customer_email'], 0, strpos($order['customer_email'], '@'))); ?>)
                        </td>
                        <td><?= htmlspecialchars($order['products']); ?></td>
                        <td>$<?= number_format($order['total_price'], 2); ?></td>
                        <td>
                          <span class="<?= $order['order_status'] === 'delivered' ? 'text-success' : ($order['order_status'] === 'shipped' ? 'text-warning' : 'text-danger'); ?>">
                            <?= htmlspecialchars($order['order_status']); ?>
                          </span>
                        </td>
                        <td>
                          <a href="view_order.php?id=<?= $order['order_id']; ?>" class="btn btn-sm btn-info">View</a>
                          <?php if ($order['order_status'] !== 'delivered'): ?>
                            <a href="update_order_status.php?id=<?= $order['order_id']; ?>&status=shipped" class="btn btn-sm btn-primary">Mark as Shipped</a>
                          <?php endif; ?>
                          <?php if ($order['order_status'] === 'shipped'): ?>
                            <a href="update_order_status.php?id=<?= $order['order_id']; ?>&status=delivered" class="btn btn-sm btn-success">Mark as Delivered</a>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" class="text-center">No orders found.</td>
                    </tr>
                  <?php endif; ?>
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
