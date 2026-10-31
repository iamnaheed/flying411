<?php
include "header.php";
include "config.php";

if (!isset($_GET['id'])) {
    die("Order ID is required.");
}

$order_id = $_GET['id'];

// Fetch order details
$query = $pdo->prepare("
    SELECT 
        o.order_id,
        o.total_amount,
        o.status,
        o.created_at,
        c.name AS customer_name,
        c.email AS customer_email,
        c.phone AS customer_phone,
        c.address AS customer_address,
        GROUP_CONCAT(CONCAT(p.product_name, ' (Qty: ', oi.quantity, ')') SEPARATOR ', ') AS products
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.order_id = ?
    GROUP BY o.order_id
");
$query->execute([$order_id]);
$order = $query->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Order Details</h3>
        <h6 class="font-weight-normal mb-0">View detailed information about this order.</h6>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Order #<?= htmlspecialchars($order['order_id']); ?></h4>
            <p><strong>Status:</strong> <?= htmlspecialchars($order['status']); ?></p>
            <p><strong>Total Amount:</strong> $<?= number_format($order['total_amount'], 2); ?></p>
            <p><strong>Ordered On:</strong> <?= htmlspecialchars($order['created_at']); ?></p>
            <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']); ?> (<?= htmlspecialchars($order['customer_email']); ?>)</p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']); ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($order['customer_address']); ?></p>
            <p><strong>Products:</strong> <?= htmlspecialchars($order['products']); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
