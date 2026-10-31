<?php 
session_start();
include "header.php";
include "config.php";

// Redirect to login page if user is not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$username = $_SESSION['username'] ?? 'Admin';

// Initialize variables
$total_vendors = $total_clients = $total_orders = $total_revenue = $pending_withdrawals = $new_registrations = 0;
$latest_orders = $notifications = [];

try {
    // Fetch Total Vendors
    $vendor_query = $pdo->query("SELECT COUNT(*) AS total_vendors FROM vendors");
    $total_vendors = $vendor_query->fetchColumn();

    // Fetch Total Clients
    $client_query = $pdo->query("SELECT COUNT(*) AS total_clients FROM clients");
    $total_clients = $client_query->fetchColumn();

    // Fetch Total Orders & Revenue
    $orders_query = $pdo->query("SELECT COUNT(order_id) AS total_orders, SUM(total_amount) AS total_revenue FROM orders");
    $orders_data = $orders_query->fetch(PDO::FETCH_ASSOC);
    $total_orders = $orders_data['total_orders'] ?? 0;
    $total_revenue = $orders_data['total_revenue'] ?? 0;

    // Fetch Pending Withdrawals from vendor_withdrawals
    $withdrawal_query = $pdo->query("SELECT COUNT(*) AS pending_withdrawals FROM vendor_withdrawals WHERE status = 'pending'");
    $pending_withdrawals = $withdrawal_query->fetchColumn();

    // Fetch New Vendor Registrations
    $new_vendor_query = $pdo->query("SELECT COUNT(*) AS new_registrations FROM vendors WHERE status = 'pending'");
    $new_registrations = $new_vendor_query->fetchColumn();

    // Fetch Latest Orders
    $latest_orders_query = $pdo->query("SELECT order_id, client_id, total_amount, status FROM orders ORDER BY created_at DESC LIMIT 5");
    $latest_orders = $latest_orders_query->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Notifications
    $notification_query = $pdo->query("SELECT message FROM notifications ORDER BY created_at DESC LIMIT 5");
    $notifications = $notification_query->fetchAll(PDO::FETCH_ASSOC);
    // Fetch Aircraft Counts
$aircraft_counts_query = $pdo->query("
    SELECT 
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending
    FROM aircrafts
");
$aircraft_counts = $aircraft_counts_query->fetch(PDO::FETCH_ASSOC);

// Fetch Engine Counts
$engine_counts_query = $pdo->query("
    SELECT 
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending
    FROM engines
");
$engine_counts = $engine_counts_query->fetch(PDO::FETCH_ASSOC);

// Fetch Parts Counts
$parts_counts_query = $pdo->query("
    SELECT 
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending
    FROM parts
");
$parts_counts = $parts_counts_query->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<head>
    <style>
    .card{
            background-color: white !important;
            box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px;
    }
        .card p{
            color:black !important;
        }
        .card-body p a{
            color:black !important;
        }
    </style>
</head>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Welcome, <?= htmlspecialchars($username); ?></h3>
        <h6 class="font-weight-normal mb-0">
          Here's your administrative overview. You have 
          <span class="text-primary"><?= count($notifications); ?> unread notifications!</span>
        </h6>
      </div>
    </div>

    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h3 class="mb-4" style="color:black">Aircraft Overview</h3>
            <p class="fs-30 mb-2">
                <a href="aircraft_approved_product.php" class="text-white text-decoration-none">Total: <?= $aircraft_counts['approved'] + $aircraft_counts['rejected'] + $aircraft_counts['pending']; ?></a>
            </p>
            <p><a href="aircraft_approved_product.php" class="text-white text-decoration-none">Approved: <?= $aircraft_counts['approved']; ?></a></p>
            <p><a href="aircraft_rejected.php" class="text-white text-decoration-none">Rejected: <?= $aircraft_counts['rejected']; ?></a></p>
            <p><a href="aircraft_pending.php" class="text-white text-decoration-none">Pending: <?= $aircraft_counts['pending']; ?></a></p>
        </div>
    </div>
</div>

<div class="col-md-4 grid-margin stretch-card">
    <div class="card card-dark-blue">
        <div class="card-body">
            <h3 class="mb-4" style="color:black">Engines Overview</h3>
            <p class="fs-30 mb-2">
                <a href="engine_approved_product.php" class="text-white text-decoration-none">Total: <?= $engine_counts['approved'] + $engine_counts['rejected'] + $engine_counts['pending']; ?></a>
            </p>
            <p><a href="engine_approved_product.php" class="text-white text-decoration-none">Approved: <?= $engine_counts['approved']; ?></a></p>
            <p><a href="engine_rejected.php" class="text-white text-decoration-none">Rejected: <?= $engine_counts['rejected']; ?></a></p>
            <p><a href="engine_pending.php" class="text-white text-decoration-none">Pending: <?= $engine_counts['pending']; ?></a></p>
        </div>
    </div>
</div>

<div class="col-md-4 grid-margin stretch-card">
    <div class="card card-light-danger">
        <div class="card-body">
            <h3 class="mb-4" style="color:black">Parts Overview</h3>
            <p class="fs-30 mb-2">
                <a href="parts_approved_product.php" class="text-white text-decoration-none">Total: <?= $parts_counts['approved'] + $parts_counts['rejected'] + $parts_counts['pending']; ?></a>
            </p>
            <p><a href="parts_approved_product.php" class="text-white text-decoration-none">Approved: <?= $parts_counts['approved']; ?></a></p>
            <p><a href="parts_reject.php "class="text-white text-decoration-none">Rejected: <?= $parts_counts['rejected']; ?></a></p>
            <p><a href="parts_pending.php" class="text-white text-decoration-none">Pending: <?= $parts_counts['pending']; ?></a></p>
        </div>
    </div>
</div>


      <div class="col-md-3 grid-margin stretch-card">
          
        <div class="card card-tale">
            <a href="admin-vendors.php" style="text-decoration:none; color:white;">
          <div class="card-body">
            <p class="mb-4"> Total Vendors</p>
            <p class="fs-30 mb-2"><?= htmlspecialchars($total_vendors); ?></p>
          </div>
          </a>
        </div>
    
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-dark-blue">
                 <a href="admin-clients.php" style="text-decoration:none; color:white;">
          <div class="card-body">
            <p class="mb-4">Total Clients</p>
            <p class="fs-30 mb-2"><?= htmlspecialchars($total_clients); ?></p>
          </div>
          </a>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-light-blue">
              <a href="admin-orders.php" style="text-decoration:none; color:white;">
          <div class="card-body">
            <p class="mb-4">Total Orders</p>
            <p class="fs-30 mb-2"><?= htmlspecialchars($total_orders); ?></p>
          </div>
          </a>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-light-danger">
            
          <div class="card-body">
            <p class="mb-4">Total Revenue</p>
            <p class="fs-30 mb-2">$<?= number_format($total_revenue, 2); ?></p>
          </div>
         
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-tale">
              <a href="admin-withdrawals.php" style="text-decoration:none; color:white;">
          <div class="card-body">
            <p class="mb-4">Pending Withdrawals</p>
            <p class="fs-30 mb-2"><?= htmlspecialchars($pending_withdrawals); ?></p>
          </div>
          </a>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-dark-blue">
            <a href="admin-clients.php" style="text-decoration:none; color:white;">
          <div class="card-body">
            <p class="mb-4">New Registrations</p>
            <p class="fs-30 mb-2"><?= htmlspecialchars($new_registrations); ?></p>
          </div>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include "footer.php"; ?>
