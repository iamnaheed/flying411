<?php
session_start();
include "header.php";
include "config.php";

// Ensure the vendor is logged in
if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch total sales and revenue over time
$sales_query = $pdo->prepare("
    SELECT 
        DATE_FORMAT(o.created_at, '%Y-%m') AS month,
        COUNT(o.order_id) AS total_sales,
        SUM(oi.price * oi.quantity) AS total_revenue
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    WHERE EXISTS (
        SELECT 1 FROM aircrafts a WHERE a.aircraft_id = oi.product_id AND oi.product_type = 'aircraft' AND a.vendor_id = ?
        UNION
        SELECT 1 FROM parts p WHERE p.part_id = oi.product_id AND oi.product_type = 'part' AND p.vendor_id = ?
        UNION
        SELECT 1 FROM engines e WHERE e.engine_id = oi.product_id AND oi.product_type = 'engine' AND e.vendor_id = ?
    )
    GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
    ORDER BY DATE_FORMAT(o.created_at, '%Y-%m') DESC
");
$sales_query->execute([$vendor_id, $vendor_id, $vendor_id]);
$sales_data = $sales_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch revenue by product/category
$revenue_query = $pdo->prepare("
    SELECT 
        CASE 
            WHEN oi.product_type = 'aircraft' THEN 'Aircraft'
            WHEN oi.product_type = 'part' THEN 'Part'
            WHEN oi.product_type = 'engine' THEN 'Engine'
        END AS category_name,
        COALESCE(a.model, p.part_number, e.model) AS product_name,
        SUM(oi.price * oi.quantity) AS total_revenue
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN aircrafts a ON oi.product_id = a.aircraft_id AND oi.product_type = 'aircraft' AND a.vendor_id = ?
    LEFT JOIN parts p ON oi.product_id = p.part_id AND oi.product_type = 'part' AND p.vendor_id = ?
    LEFT JOIN engines e ON oi.product_id = e.engine_id AND oi.product_type = 'engine' AND e.vendor_id = ?
    WHERE oi.product_type IN ('aircraft', 'part', 'engine')
    GROUP BY oi.product_type, product_name
    ORDER BY total_revenue DESC
");
$revenue_query->execute([$vendor_id, $vendor_id, $vendor_id]);
$revenue_data = $revenue_query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Sales Reports</h3>
        <h6 class="font-weight-normal mb-0">Analyze your sales performance with detailed reports.</h6>
      </div>
    </div>

    <!-- Total Sales Over Time -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Total Sales Over Time</h4>
            <canvas id="salesChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Revenue by Product/Category -->
    <div class="row mt-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Revenue by Product/Category</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Total Revenue</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($revenue_data as $revenue): ?>
                    <tr>
                      <td><?= htmlspecialchars($revenue['category_name']); ?></td>
                      <td><?= htmlspecialchars($revenue['product_name']); ?></td>
                      <td>$<?= number_format($revenue['total_revenue'], 2); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Downloadable Reports -->
    <div class="row mt-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Download Reports</h4>
            <form method="POST" action="download_report.php">
              <button type="submit" name="format" value="csv" class="btn btn-primary">Download CSV</button>
              <button type="submit" name="format" value="pdf" class="btn btn-secondary">Download PDF</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Prepare data for the sales chart
  const salesData = {
    labels: <?= json_encode(array_column($sales_data, 'month')); ?>,
    datasets: [{
      label: 'Total Revenue ($)',
      data: <?= json_encode(array_column($sales_data, 'total_revenue')); ?>,
      borderColor: 'rgba(75, 192, 192, 1)',
      backgroundColor: 'rgba(75, 192, 192, 0.2)',
      fill: true
    }]
  };

  // Render the sales chart
  const ctx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(ctx, {
    type: 'line',
    data: salesData,
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: true,
          text: 'Total Sales Over Time'
        }
      }
    }
  });
</script>

<?php include "footer.php"; ?>
