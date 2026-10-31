<?php 
include "header.php";
session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect to login page if user is not logged in
if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../login.php");
    exit;
}

include "config.php";
$vendor_id = $_SESSION['vendor_id'];

$username = $_SESSION['username'] ?? 'Vendor'; // Prevent undefined array key error

// Initialize variables
$total_sales = 0;
$total_revenue = 0;
$pending_orders = 0;
$approved_products = 0;
$rejected_products = 0;
$escrow_balance = 0;
$withdrawal_requests = 0;
$notifications = [];

// Fetch Total Sales and Revenue
$sales_query = $pdo->prepare("SELECT COUNT(DISTINCT order_id) AS total_sales, SUM(total_amount) AS total_revenue FROM orders WHERE order_id IN (SELECT order_id FROM order_items WHERE product_id IN (SELECT part_id FROM parts WHERE vendor_id = ?))");
$sales_query->execute([$vendor_id]);
$sales_data = $sales_query->fetch(PDO::FETCH_ASSOC);
$total_sales = $sales_data['total_sales'] ?? 0;
$total_revenue = $sales_data['total_revenue'] ?? 0;

// Fetch Pending Orders
$pending_orders_query = $pdo->prepare("SELECT COUNT(order_id) AS pending_orders FROM orders WHERE status = 'pending' AND order_id IN (SELECT order_id FROM order_items WHERE product_id IN (SELECT part_id FROM parts WHERE vendor_id = ?))");
$pending_orders_query->execute([$vendor_id]);
$pending_orders = $pending_orders_query->fetchColumn() ?? 0;

// Check if approval_status exists in parts
$table_check = $pdo->query("SHOW COLUMNS FROM parts LIKE 'approval_status'");
if ($table_check->rowCount() > 0) {
    $product_status_query = $pdo->prepare("SELECT SUM(CASE WHEN approval_status = 'approved' THEN 1 ELSE 0 END) AS approved_products, SUM(CASE WHEN approval_status = 'rejected' THEN 1 ELSE 0 END) AS rejected_products FROM parts WHERE vendor_id = ?");
    $product_status_query->execute([$vendor_id]);
    $product_status_data = $product_status_query->fetch(PDO::FETCH_ASSOC);
    $approved_products = $product_status_data['approved_products'] ?? 0;
    $rejected_products = $product_status_data['rejected_products'] ?? 0;
}

// Fetch Notifications
$notification_query = $pdo->prepare("SELECT message FROM notifications WHERE vendor_id = ? ORDER BY created_at DESC LIMIT 5");
$notification_query->execute([$vendor_id]);
$notifications = $notification_query->fetchAll(PDO::FETCH_ASSOC);



$vendor_id = $_SESSION['vendor_id'];

// Set pagination variables
$limit = 30; // Products per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Set filters
$product_type = isset($_GET['product_type']) ? $_GET['product_type'] : '';
$approval_status = isset($_GET['approval_status']) ? $_GET['approval_status'] : '';

// Base query components
$conditions = [];
$params = [];

// Filter product type
if (!empty($product_type) && in_array($product_type, ['aircraft', 'engine', 'part'])) {
    $conditions[] = "product_type = ?";
    $params[] = $product_type;
}

// Filter approval status
if (!empty($approval_status) && in_array($approval_status, ['pending', 'approved', 'rejected'])) {
    $conditions[] = "status = ?";
    $params[] = $approval_status;
}

// Query for fetching products with pagination
$query = $pdo->prepare("
    SELECT * FROM (
        SELECT 'aircraft' AS product_type, aircraft_id AS product_id, model AS product_name,country, location, price, status, price_label, is_sold
        FROM aircrafts WHERE vendor_id = ?
        UNION ALL
        SELECT 'engine' AS product_type, engine_id AS product_id, model AS product_name, country, location, price, status, price_label, is_sold
        FROM engines WHERE vendor_id = ?
        UNION ALL
        SELECT 'part' AS product_type, part_id AS product_id, part_number AS product_name, country , region AS location, price, status,  price_label, is_sold
        FROM parts WHERE vendor_id = ?
    ) AS combined_products
    " . (!empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "") . "
    LIMIT $limit OFFSET $offset
");

$query->execute(array_merge([$vendor_id, $vendor_id, $vendor_id], $params));
$products = $query->fetchAll(PDO::FETCH_ASSOC);


// Get total product count with filters
$countQuery = $pdo->prepare("
    SELECT COUNT(*) FROM (
        SELECT 'aircraft' AS product_type, aircraft_id AS product_id, model AS product_name, location, price, status, price_label
        FROM aircrafts WHERE vendor_id = ?
        UNION ALL
        SELECT 'engine' AS product_type, engine_id AS product_id, model AS product_name, location, price, status, NULL AS price_label
        FROM engines WHERE vendor_id = ?
        UNION ALL
        SELECT 'part' AS product_type, part_id AS product_id, part_number AS product_name, region AS location, price, status, NULL AS price_label
        FROM parts WHERE vendor_id = ?
    ) AS total_products
    " . (!empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "")
);

$countQuery->execute(array_merge([$vendor_id, $vendor_id, $vendor_id], $params));
$total_products = $countQuery->fetchColumn();
$total_pages = ceil($total_products / $limit);


$active_products = [];
$sold_products = [];

foreach ($products as $product) {
    if ($product['is_sold']) {
        $sold_products[] = $product;
    } else {
        $active_products[] = $product;
    }
}
?>
<head>
    <style>
        .card {
            background-color: white !important;
        }
                .card p, .card-title{
                    color:black !important;
                }
               .row a:hover{
                   text-decoration:none;
                   
               }
               .pages-links a{
                  background-color: #4747a1 !important;
                  color:white !important;
               }
                              .pages-links a:hover{
                                  background-color: lightgray !important;
                                  color:black !important;
                                  border:1px solid black !important;
                              }
                              /* Common button styles */
/* Common button styles */
.sold-toggle-btn {
    display: inline-block;
    padding: 7px 14px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    color: #fff;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
}

/* Not sold button - gray */
.sold-toggle-btn.not-sold {
    background-color: #6c757d; /* gray */
}


/* Sold button - red */
.sold-toggle-btn.Unmark-as-Sold {
    background-color: Green; /* red */
}




    </style>
</head>
<div class="main-panel">
  <div class="content-wrapper">
 <div class="row">
  <div class="col-md-12 grid-margin">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start flex-wrap flex-lg-nowrap">
      
      <!-- LEFT SIDE: Welcome Text -->
      <div class="mb-3 mb-lg-0 me-lg-4">
        <h3 class="font-weight-bold">Welcome,<br> <?= htmlspecialchars($username); ?></h3>
        <h6 class="font-weight-normal mb-0">
          Here's your store's performance summary.
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
      <div class="col-md-3 grid-margin ">
    <a href="sales_reports.php">
        <div class="card card-tale">
          <div class="card-body">
            <p class="mb-4">Total Sales</p>
            <p class="fs-30 mb-2"><?= htmlspecialchars($total_sales); ?></p>
            <p>15% increase (last 30 days)</p>
          </div>
        </div>
    </a>
      </div>
      <div class="col-md-3 grid-margin ">
           <a href="sales_reports.php">
        <div class="card card-dark-blue">
          <div class="card-body">
            <p class="mb-4">Total Revenue</p>
            <p class="fs-30 mb-2">$<?= number_format($total_revenue, 2); ?></p>
            <p>10% increase (last 30 days)</p>
          </div>
        </div>
        </a>
      </div>
      <div class="col-md-3 grid-margin ">
           <a href="orders-management.php">
        <div class="card card-light-blue">
          <div class="card-body">
            <p class="mb-4">Pending Orders</p>
            <p class="fs-30 mb-2"><?= htmlspecialchars($pending_orders); ?></p>
            <p>5 new orders</p>
          </div>
        </div>
        </a>
      </div>
      <div class="col-md-3 grid-margin ">
           <a href="manage_products.php">
        <div class="card card-light-danger">
          <div class="card-body">
            <p class="mb-4">Products Status</p>
            <p class="fs-30 mb-2">Approved: <?= htmlspecialchars($approved_products); ?></p>
            <p>Rejected: <?= htmlspecialchars($rejected_products); ?></p>
          </div>
        </div>
        </a>
      </div>
    </div>
    
      <div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">

<!-- ================= ACTIVE LISTINGS ================= -->

<h4 class="card-title">My Aircraft Listings (Active)</h4>

<div class="table-responsive">
<table class="table table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>Type</th>
            <th>Name</th>
            <th>Location</th>
            <th>Region</th>
            <th>Price ($)</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
    <?php if (!empty($active_products)): ?>
        <?php foreach ($active_products as $product): ?>

            <?php
                // Determine correct edit page
                switch ($product['product_type']) {
                    case 'aircraft': $edit_page = "aircraft_edit.php"; break;
                    case 'engine': $edit_page = "engine_edit.php"; break;
                    case 'part': $edit_page = "part_edit.php"; break;
                    default: $edit_page = "#";
                }
            ?>

            <tr>
                <td><?= ucfirst($product['product_type']); ?></td>
                <td><?= htmlspecialchars($product['product_name']); ?></td>
                <td><?= htmlspecialchars($product['location']); ?></td>
                <td><?= htmlspecialchars($product['country']); ?></td>

                <td>
                    <?php
                    if (isset($product['price_label']) && $product['price_label'] === 'call') {
                        echo "Call for Price";
                    } else {
                        echo "$" . number_format($product['price'], 2);
                    }
                    ?>
                </td>

                <td>
                    <?php 
                        $status = strtolower($product['status']);
                        $status_class = $status === 'approved' ? 'text-success' :
                                        ($status === 'rejected' ? 'text-danger' : 'text-warning');
                    ?>
                    <span class="<?= $status_class; ?>"><?= ucfirst($status); ?></span>
                </td>

                <td>
                    <!-- Mark as Sold -->
                    <a href="toggle_sold.php?id=<?= $product['product_id']; ?>&type=<?= $product['product_type']; ?>&current=0"
                       class="btn btn-sm btn-primary" style="color:black">
                        Mark as Sold
                    </a>

                    <!-- Edit -->
                   <a href="<?= $edit_page; ?>?id=<?= $product['product_id']; ?>" class="btn btn-sm btn-warning">Edit</a> <a href="delete_product.php?type=<?= $product['product_type']; ?>&id=<?= $product['product_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                </td>
            </tr>

        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6" class="text-center text-muted">No active products.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>


<!-- ================= SOLD LISTINGS ================= -->

<h4 class="card-title mt-4">Older Product Listings (Sold)</h4>

<div class="table-responsive">
<table class="table table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>Type</th>
            <th>Name</th>
            <th>Location</th>
            <th>Price ($)</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
    <?php if (!empty($sold_products)): ?>
        <?php foreach ($sold_products as $product): ?>

            <?php
                // Determine correct edit page
                switch ($product['product_type']) {
                    case 'aircraft': $edit_page = "aircraft_edit.php"; break;
                    case 'engine': $edit_page = "engine_edit.php"; break;
                    case 'part': $edit_page = "part_edit.php"; break;
                    default: $edit_page = "#";
                }
            ?>

            <tr class="table-warning">
                <td><?= ucfirst($product['product_type']); ?></td>
                <td><?= htmlspecialchars($product['product_name']); ?></td>
                <td><?= htmlspecialchars($product['location']); ?></td>

                <td>
                    <?php
                    if (isset($product['price_label']) && $product['price_label'] === 'call') {
                        echo "Call for Price";
                    } else {
                        echo "$" . number_format($product['price'], 2);
                    }
                    ?>
                </td>

                <td><span class="text-muted">Sold</span></td>

                <td>
                    <!-- Unmark sold -->
                    <a href="toggle_sold.php?id=<?= $product['product_id']; ?>&type=<?= $product['product_type']; ?>&current=1"
                       class="btn btn-sm btn-success">
                        Unmark as Sold
                    </a>

                    <!-- Edit -->
                   <a href="<?= $edit_page; ?>?id=<?= $product['product_id']; ?>" class="btn btn-sm btn-warning">Edit</a> <a href="delete_product.php?type=<?= $product['product_type']; ?>&id=<?= $product['product_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                </td>

            </tr>

        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6" class="text-center text-muted">No sold products.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>


<!-- ===== PAGINATION ===== -->
<nav>
  <ul class="pagination justify-content-center mt-3">
    <?php if ($page > 1): ?>
      <li class="page-item"><a class="page-link" href="?page=<?= $page - 1; ?>&product_type=<?= $product_type; ?>&approval_status=<?= $approval_status; ?>">Previous</a></li>
    <?php endif; ?>

    <?php 
      $displayedPages = [];
      for ($i = 1; $i <= $total_pages; $i++) {
        if ($i <= 2 || $i >= $total_pages - 1 || ($i >= $page - 2 && $i <= $page + 2)) {
          if (!empty($displayedPages) && $i - end($displayedPages) > 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
          }
          echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">
                  <a class="page-link" href="?page=' . $i . '&product_type=' . $product_type . '&approval_status=' . $approval_status . '">' . $i . '</a>
                </li>';
          $displayedPages[] = $i;
        }
      }
    ?>

    <?php if ($page < $total_pages): ?>
      <li class="page-item"><a class="page-link" href="?page=<?= $page + 1; ?>&product_type=<?= $product_type; ?>&approval_status=<?= $approval_status; ?>">Next</a></li>
    <?php endif; ?>
  </ul>
</nav>

      </div>
    </div>
  </div>
</div>
  </div>
</div>
<?php include "footer.php"; ?>
