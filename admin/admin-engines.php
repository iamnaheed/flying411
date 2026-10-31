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

// Pagination settings
$limit = 50; // Show 50 per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total count for pagination
$count_query = "SELECT COUNT(DISTINCT v.vendor_id) AS total FROM vendors v
                JOIN engines e ON v.vendor_id = e.vendor_id
                WHERE v.business_email LIKE ? AND v.business_name LIKE ?";
$count_stmt = $pdo->prepare($count_query);
$count_stmt->execute(["%$filter_email%", "%$filter_vendor%"]);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch paginated vendors who have added engines, with filters
$query = "SELECT v.vendor_id, v.business_name, v.business_email, 
                 COUNT(e.engine_id) AS total_engines,
                 SUM(CASE WHEN e.status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                 SUM(CASE WHEN e.status = 'pending' THEN 1 ELSE 0 END) AS pending_count
          FROM vendors v
          JOIN engines e ON v.vendor_id = e.vendor_id
          WHERE v.business_email LIKE ? AND v.business_name LIKE ?
          GROUP BY v.vendor_id
          LIMIT $limit OFFSET $offset";

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
        <h3>Manage Engine Vendors</h3>

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
                    <th>Total Engines</th>
                    <th>Approved</th>
                    <th>Pending</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vendors)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No vendors found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vendors as $vendor): ?>
                        <tr>
                            <td><?= htmlspecialchars($vendor['business_name']); ?></td>
                            <td><?= htmlspecialchars($vendor['business_email']); ?></td>
                            <td><?= $vendor['total_engines']; ?></td>
                            <td class="text-success"><?= $vendor['approved_count']; ?></td>
                            <td class="text-warning"><?= $vendor['pending_count']; ?></td>
                            <td>
                                <a href="engines-listing.php?vendor_id=<?= $vendor['vendor_id']; ?>" class="btn btn-info btn-sm">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>

        <!-- Pagination -->
        <nav>
            <ul class="pagination" style="margin-top:50px; text-align:center;">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?vendor=<?= urlencode($filter_vendor) ?>&email=<?= urlencode($filter_email) ?>&page=<?= $page - 1 ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?vendor=<?= urlencode($filter_vendor) ?>&email=<?= urlencode($filter_email) ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?vendor=<?= urlencode($filter_vendor) ?>&email=<?= urlencode($filter_email) ?>&page=<?= $page + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<?php include "footer.php"; ?>
