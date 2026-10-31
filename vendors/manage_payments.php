<?php
session_start();
include "header.php";
include "config.php"; // Database connection

// Check if vendor_id is in session
if (!isset($_SESSION['vendor_id'])) {
   header("Location: ../login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

$query = $pdo->prepare("
    SELECT 
        p.payment_id,
        o.order_id,
        p.payment_amount AS amount,
        p.payment_status,
        e.escrow_status,
        p.payment_date AS created_at,
        c.name AS customer_name,
        GROUP_CONCAT(
            CASE 
                WHEN oi.product_type = 'aircraft' THEN CONCAT(ac.model, ' (Qty: ', oi.quantity, ', Price: $', oi.price, ')')
                WHEN oi.product_type = 'part' THEN CONCAT(pt.part_number, ' (Qty: ', oi.quantity, ', Price: $', oi.price, ')')
                WHEN oi.product_type = 'engine' THEN CONCAT(en.model, ' (Qty: ', oi.quantity, ', Price: $', oi.price, ')')
                ELSE 'Unknown Product'
            END 
            SEPARATOR ', '
        ) AS products
    FROM payments p
    JOIN orders o ON p.order_id = o.order_id
    JOIN clients c ON o.client_id = c.client_id
    JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN aircrafts ac ON oi.product_id = ac.aircraft_id AND oi.product_type = 'aircraft'
    LEFT JOIN parts pt ON oi.product_id = pt.part_id AND oi.product_type = 'part'
    LEFT JOIN engines en ON oi.product_id = en.engine_id AND oi.product_type = 'engine'
    LEFT JOIN escrow_logs e ON p.payment_id = e.payment_id
    WHERE (ac.vendor_id = ? OR pt.vendor_id = ? OR en.vendor_id = ?)
    GROUP BY p.payment_id
");
$query->execute([$vendor_id, $vendor_id, $vendor_id]);

$payments = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<style> .btn-primary{ background-color: #4747A1; color: white; } </style>
<div class="main-panel">
  <div class="content-wrapper">
    <!--<div class="row">-->
    <!--  <div class="col-md-12 grid-margin">-->
    <!--    <h3 class="font-weight-bold">Manage Payments</h3>-->
    <!--    <h6 class="font-weight-normal mb-0">Track and manage payments for your orders.</h6>-->
    <!--  </div>-->
    <!--</div>-->
    
    
     <div class="row">
  <div class="col-md-12 grid-margin">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start flex-wrap flex-lg-nowrap">
      
      <!-- LEFT SIDE: Welcome Text -->
      <div class="mb-3 mb-lg-0 me-lg-4">
        <h3 class="font-weight-bold">Manage Payments</h3>
        <h6 class="font-weight-normal mb-0">
          Track and manage payments for your orders.
           <span class="text-primary"></span>
        </h6>
      </div>

      <!-- RIGHT SIDE: Listing Buttons -->
      <div class="pages-links w-100 w-lg-auto overflow-auto">
        <div class="d-flex flex-nowrap gap-2" style="justify-content:end;">
          <a href="addaircraft.php" class="btn btn-primary">List An Aircraft</a>
          <a href="addparts.php" class="btn btn-primary">List A Part</a>
          <a href="addengines.php" class="btn btn-primary">List An Engine</a>
          <a href="add_services.php" class="btn btn-primary">List A Service</a>
          <a href="manage_products.php" class="btn btn-primary">My Listings</a>
        </div>
      </div>

    </div>
  </div>
</div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Payments List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Payment ID</th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Products</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th>Escrow Status</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (count($payments) > 0): ?>
                    <?php foreach ($payments as $payment): ?>
                      <tr>
                        <td>#<?= htmlspecialchars($payment['payment_id']); ?></td>
                        <td>#<?= htmlspecialchars($payment['order_id']); ?></td>
                        <td><?= htmlspecialchars(substr($payment['customer_name'], 0, 1)); ?>****</td>
                        <td><?= htmlspecialchars($payment['products']); ?></td>
                        <td>$<?= number_format($payment['amount'], 2); ?></td>
                        <td>
                          <span class="<?= $payment['payment_status'] === 'completed' ? 'text-success' : ($payment['payment_status'] === 'refunded' ? 'text-danger' : 'text-warning'); ?>">
                            <?= ucfirst($payment['payment_status']); ?>
                          </span>
                        </td>
                        <td>
                          <span class="<?= $payment['escrow_status'] === 'released' ? 'text-success' : 'text-warning'; ?>">
                            <?= ucfirst($payment['escrow_status']); ?>
                          </span>
                        </td>
                        <td><?= htmlspecialchars($payment['created_at']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="8" class="text-center">No payments found.</td>
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
