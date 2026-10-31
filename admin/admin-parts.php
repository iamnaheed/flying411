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
$filter_vendor = $_GET['vendor'] ?? '';
$filter_email = $_GET['email'] ?? '';

// Fetch vendors who have added parts, with filters
$query = "SELECT v.vendor_id, v.business_name, v.business_email,
                 COUNT(p.part_id) AS total_parts,
                 SUM(CASE WHEN p.status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                 SUM(CASE WHEN p.status = 'pending' THEN 1 ELSE 0 END) AS pending_count
          FROM vendors v
          JOIN parts p ON v.vendor_id = p.vendor_id
          WHERE v.business_name LIKE ? AND v.business_email LIKE ?
          GROUP BY v.vendor_id";

$stmt = $pdo->prepare($query);
$stmt->execute(["%$filter_vendor%", "%$filter_email%"]);
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
        <h3>Manage Vendors & Parts</h3>

        <!-- Filters -->
        <form method="GET" class="mb-3">
            <input type="text" name="vendor" placeholder="Vendor Name" value="<?= htmlspecialchars($filter_vendor) ?>" class="form-control mb-2">
            <input type="email" name="email" placeholder="Vendor Email" value="<?= htmlspecialchars($filter_email) ?>" class="form-control mb-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <!-- Vendors & Parts Table -->
      <div class="table-responsive">
        <table class="table table-striped  w-100">
            <thead class="thead-dark">
                <tr>
                    <th>Vendor Name</th>
                    <th>Vendor Email</th>
                    <th>Total Added Parts</th>
                    <th>Approved Parts</th>
                    <th>Pending Parts</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vendors)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No parts found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vendors as $vendor): ?>
                        <tr>
                            <td><?= htmlspecialchars($vendor['business_name']); ?></td>
                            <td><?= htmlspecialchars($vendor['business_email']); ?></td>
                            <td><?= $vendor['total_parts']; ?></td>
                            <td class="text-success"><?= $vendor['approved_count']; ?></td>
                            <td class="text-warning"><?= $vendor['pending_count']; ?></td>
                            <td>
                                <a href="parts-listing.php?vendor_id=<?= $vendor['vendor_id']; ?>" class="btn btn-info btn-sm">View Parts</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
