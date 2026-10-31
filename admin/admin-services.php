<?php 
session_start();
include "header.php";
include "config.php";

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Filter inputs
$filter_vendor = $_GET['vendor'] ?? '';
$filter_email  = $_GET['email'] ?? '';

// Fetch vendors who added services
$query = "
    SELECT v.vendor_id, v.business_name, v.business_email,
           COUNT(s.id) AS total_services
    FROM vendors v
    JOIN services s ON v.vendor_id = s.vendor_id
    WHERE v.business_name LIKE ? 
      AND v.business_email LIKE ?
    GROUP BY v.vendor_id
";

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
  min-width: 100% !important;
}
</style>
</head>

<div class="main-panel">
<div class="content-wrapper">
    <h3>Manage Vendors & Services</h3>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="text" name="vendor" placeholder="Vendor Name" 
               value="<?= htmlspecialchars($filter_vendor) ?>" 
               class="form-control mb-2">

        <input type="email" name="email" placeholder="Vendor Email" 
               value="<?= htmlspecialchars($filter_email) ?>" 
               class="form-control mb-2">

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-striped w-100">
            <thead class="thead-dark">
                <tr>
                    <th>Vendor Name</th>
                    <th>Vendor Email</th>
                    <th>Total Services</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($vendors)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No services found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vendors as $vendor): ?>
                        <tr>
                            <td><?= htmlspecialchars($vendor['business_name']); ?></td>
                            <td><?= htmlspecialchars($vendor['business_email']); ?></td>
                            <td><?= $vendor['total_services']; ?></td>

                            <td>
                                <a href="services-listing.php?vendor_id=<?= $vendor['vendor_id']; ?>" 
                                   class="btn btn-info btn-sm">
                                   View Services
                                </a>
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
