<?php
session_start();
include "header.php";
include "config.php"; // Database connection

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get filter inputs
$filter_email = $_GET['email'] ?? '';
$filter_vendor = $_GET['vendor'] ?? '';

// Fetch vendors who have added aircraft, with filters
$query = "SELECT v.vendor_id, v.business_name, v.business_email, 
                 COUNT(a.aircraft_id) AS total_aircraft,
                 SUM(CASE WHEN a.status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                 SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) AS pending_count
          FROM vendors v
          JOIN aircrafts a ON v.vendor_id = a.vendor_id
          WHERE v.business_email LIKE ? AND v.business_name LIKE ?
          GROUP BY v.vendor_id";

$stmt = $pdo->prepare($query);
$stmt->execute(["%$filter_email%", "%$filter_vendor%"]);
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<head>
    <style>
     .table-responsive {
  width: 100% !important;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.table {
  min-width: 100% !important; /* Ensures table doesn't shrink */
}


    </style>
    
</head>
<div class="main-panel">
    <div class="content-wrapper">
        <h3>Manage Aircraft Vendors Products</h3>

        <!-- Filters -->
        <form method="GET" class="mb-3">
            <input type="text" name="vendor" placeholder="Vendor Name" value="<?= htmlspecialchars($filter_vendor) ?>" class="form-control mb-2">
            <input type="email" name="email" placeholder="Vendor Email" value="<?= htmlspecialchars($filter_email) ?>" class="form-control mb-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <!-- Vendor Table -->
        <div class="table-responsive">
        <table class="table table-striped  w-100">
            <thead class="thead-dark">
                <tr>
                    <th>Vendor</th>
                    <th>Email</th>
                    <th>Total Aircraft</th>
                    <th>Approved</th>
                    <th>Pending</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendors as $vendor): ?>
                    <tr>
                        <td><?= htmlspecialchars($vendor['business_name']); ?></td>
                        <td><?= htmlspecialchars($vendor['business_email']); ?></td>
                        <td><?= $vendor['total_aircraft']; ?></td>
                        <td class="text-success"><?= $vendor['approved_count']; ?></td>
                        <td class="text-warning"><?= $vendor['pending_count']; ?></td>
                        <td>
                            <a href="aircraft-listing.php?vendor_id=<?= $vendor['vendor_id']; ?>" class="btn btn-info btn-sm">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php include "footer.php"; ?> "