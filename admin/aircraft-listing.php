<?php
ob_start();
session_start();
include "header.php";
include "config.php";


// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Validate Vendor ID
if (!isset($_GET['vendor_id']) || empty($_GET['vendor_id'])) {
    header("Location: admin-aircraft.php");
    exit;
}
$vendor_id = $_GET['vendor_id'];

// Fetch Vendor Details
$stmt = $pdo->prepare("SELECT business_name, business_email FROM vendors WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
    header("Location: admin-aircraft.php");
    exit;
}

// Handle Approve, Reject, Approve All, Delete
if (isset($_GET['approve_id']) || isset($_GET['reject_id']) || isset($_GET['approve_all']) || isset($_GET['pending_id']) ||  isset($_GET['delete_id'])) {
    if (isset($_GET['approve_id'])) {
        $aircraft_id = $_GET['approve_id'];
        $pdo->prepare("UPDATE aircrafts SET status = 'approved' WHERE aircraft_id = ?")->execute([$aircraft_id]);
    } elseif (isset($_GET['reject_id'])) {
        $aircraft_id = $_GET['reject_id'];
        $pdo->prepare("UPDATE aircrafts SET status = 'rejected' WHERE aircraft_id = ?")->execute([$aircraft_id]);
    } elseif (isset($_GET['approve_all'])) {
        $pdo->prepare("UPDATE aircrafts SET status = 'approved' WHERE vendor_id = ?")->execute([$vendor_id]);
        
    } elseif (isset($_GET['pending_id'])) {
    $aircraft_id = $_GET['pending_id'];
    $pdo->prepare("UPDATE aircrafts SET status = 'pending' WHERE aircraft_id = ?")->execute([$aircraft_id]);
        
    } elseif (isset($_GET['delete_id'])) {
        $aircraft_id = $_GET['delete_id'];

        // Delete associated images
        $pdo->prepare("DELETE FROM product_images WHERE product_id = ? AND product_type = 'aircraft'")->execute([$aircraft_id]);

        // Delete associated documents
        $pdo->prepare("DELETE FROM product_aircraft_documents WHERE product_id = ?")->execute([$aircraft_id]);

        // Delete aircraft record
        $pdo->prepare("DELETE FROM aircrafts WHERE aircraft_id = ?")->execute([$aircraft_id]);
    }

    // Redirect after action to prevent resubmission
    header("Location: aircraft-listing.php?vendor_id=$vendor_id");
    exit;
}

// Fetch Aircrafts for Display
$stmt = $pdo->prepare("SELECT * FROM aircrafts WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$aircrafts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$active_aircrafts = [];
$sold_aircrafts = [];

foreach ($aircrafts as $aircraft) {
    if ($aircraft['is_sold']) {
        $sold_aircrafts[] = $aircraft;
    } else {
        $active_aircrafts[] = $aircraft;
    }
}

?>
<style>
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

.sold-toggle-btn.not-sold { background-color: #6c757d; }


.sold-toggle-btn.Unmark-as-Sold { background-color: green; }


</style>
<div class="main-panel">
   <div class="content-wrapper">

<h3>Aircrafts by <?= htmlspecialchars($vendor['business_name']); ?></h3>
<h6>Email: <?= htmlspecialchars($vendor['business_email']); ?></h6>


<!-- ======================================================= -->
<!-- ACTIVE AIRCRAFT LISTINGS -->
<!-- ======================================================= -->
<h4 class="mt-4">Active Aircraft Listings</h4>

<div class="table-responsive">
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Image</th>
            <th>Model</th>
            <th>Manufacturer</th>
            <th>Single Engine Time</th>
            <th>Engine 1 Time</th>
            <th>Prop 1 Time</th>
            <th>Engine 2 Time</th>
            <th>Prop 2 Time</th>
            <th>Year</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>

<tbody>
<?php if (!empty($active_aircrafts)): ?>
<?php foreach ($active_aircrafts as $aircraft): ?>

    <?php 
        $imgStmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND product_type = 'aircraft' ORDER BY sort_order ASC LIMIT 1");
        $imgStmt->execute([$aircraft['aircraft_id']]);
        $image = $imgStmt->fetchColumn();

        $vendorPath = "../vendors/" . $image;
        $clientPath = "../clients/" . $image;
    ?>

<tr>
    <td>
        <?php if ($image && file_exists($vendorPath)): ?>
            <img src="<?= $vendorPath ?>" width="80">
        <?php elseif ($image && file_exists($clientPath)): ?>
            <img src="<?= $clientPath ?>" width="80">
        <?php else: ?>
            <span>No Image</span>
        <?php endif; ?>
    </td>

    <td><?= htmlspecialchars($aircraft['model']); ?></td>
    <td><?= htmlspecialchars($aircraft['manufacturer']); ?></td>

    <td><?= $aircraft['enginehours']; ?> <?= $aircraft['enginestatus']; ?></td>
    <td><?= $aircraft['engine1_hours']; ?> <?= $aircraft['engine1_status']; ?></td>
    <td><?= $aircraft['prop1_hours']; ?> <?= $aircraft['prop1_status']; ?></td>
    <td><?= $aircraft['engine2_hours']; ?> <?= $aircraft['engine2_status']; ?></td>
    <td><?= $aircraft['prop2_hours']; ?> <?= $aircraft['prop2_status']; ?></td>

    <td><?= $aircraft['year']; ?></td>

    <td>
        <?php
        if ($aircraft['price_label'] === 'call') {
            echo "Call for Price";
        } else {
            echo "$" . number_format($aircraft['price']);
        }
        ?>
    </td>

    <td class="<?= $aircraft['status'] === 'approved' ? 'text-success' : 'text-warning'; ?>">
        <?= ucfirst($aircraft['status']); ?>
    </td>

    <td>
        <!-- Mark Sold -->
        <a href="toggle_sold.php?id=<?= $aircraft['aircraft_id']; ?>&type=aircraft&current=0"
           class="btn btn-sm btn-primary">
           Mark as Sold
        </a>

        <a href="?approve_id=<?= $aircraft['aircraft_id']; ?>&vendor_id=<?= $vendor_id; ?>" class="btn btn-success btn-sm">Approve</a>

        <a href="?pending_id=<?= $aircraft['aircraft_id']; ?>&vendor_id=<?= $vendor_id; ?>" class="btn btn-warning btn-sm">Pending</a>

        <a href="?reject_id=<?= $aircraft['aircraft_id']; ?>&vendor_id=<?= $vendor_id; ?>" class="btn btn-danger btn-sm">Reject</a>

        <?php
        $docStmt = $pdo->prepare("SELECT document_url FROM product_aircraft_documents WHERE product_id = ? LIMIT 1");
        $docStmt->execute([$aircraft['aircraft_id']]);
        $doc_url = $docStmt->fetchColumn();
        ?>

        <?php if ($doc_url): ?>
            <a href="../vendors/<?= htmlspecialchars($doc_url); ?>" target="_blank" class="btn btn-primary btn-sm">View Document</a>
        <?php else: ?>
            <span class="btn btn-secondary btn-sm">No Document</span>
        <?php endif; ?>

        <a href="edit-aircraft.php?aircraft_id=<?= $aircraft['aircraft_id']; ?>" class="btn btn-warning btn-sm">Edit</a>

        <a href="?delete_id=<?= $aircraft['aircraft_id']; ?>&vendor_id=<?= $vendor_id; ?>" onclick="return confirm('Are you sure?');" class="btn btn-dark btn-sm">Delete</a>
    </td>

</tr>

<?php endforeach; ?>
<?php else: ?>
<tr><td colspan="12" class="text-center text-muted">No active aircraft listings.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>




<!-- ======================================================= -->
<!-- SOLD AIRCRAFT LISTINGS -->
<!-- ======================================================= -->
<h4 class="mt-4">Older Aircraft Listings (Sold)</h4>

<div class="table-responsive">
<table class="table table-striped table-bordered">
<thead>
    <tr>
        <th>Image</th>
        <th>Model</th>
        <th>Manufacturer</th>
        <th>Single Engine Time</th>
        <th>Engine 1 Time</th>
        <th>Prop 1 Time</th>
        <th>Engine 2 Time</th>
        <th>Prop 2 Time</th>
        <th>Year</th>
        <th>Price</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
</thead>

<tbody>
<?php if (!empty($sold_aircrafts)): ?>
<?php foreach ($sold_aircrafts as $aircraft): ?>

    <?php 
        $imgStmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND product_type = 'aircraft' ORDER BY sort_order ASC LIMIT 1");
        $imgStmt->execute([$aircraft['aircraft_id']]);
        $image = $imgStmt->fetchColumn();

        $vendorPath = "../vendors/" . $image;
        $clientPath = "../clients/" . $image;
    ?>

<tr class="table-warning">

<td>
    <?php if ($image && file_exists($vendorPath)): ?>
        <img src="<?= $vendorPath ?>" width="80">
    <?php elseif ($image && file_exists($clientPath)): ?>
        <img src="<?= $clientPath ?>" width="80">
    <?php else: ?>
        <span>No Image</span>
    <?php endif; ?>
</td>

<td><?= htmlspecialchars($aircraft['model']); ?></td>
<td><?= htmlspecialchars($aircraft['manufacturer']); ?></td>

<td><?= $aircraft['enginehours']; ?> <?= $aircraft['enginestatus']; ?></td>
<td><?= $aircraft['engine1_hours']; ?> <?= $aircraft['engine1_status']; ?></td>
<td><?= $aircraft['prop1_hours']; ?> <?= $aircraft['prop1_status']; ?></td>
<td><?= $aircraft['engine2_hours']; ?> <?= $aircraft['engine2_status']; ?></td>
<td><?= $aircraft['prop2_hours']; ?> <?= $aircraft['prop2_status']; ?></td>

<td><?= $aircraft['year']; ?></td>

<td>
    <?php
    if ($aircraft['price_label'] === 'call') {
        echo "Call for Price";
    } else {
        echo "$" . number_format($aircraft['price']);
    }
    ?>
</td>

<td><span class="text-muted">Sold</span></td>

<td>
    <a href="toggle_sold.php?id=<?= $aircraft['aircraft_id']; ?>&type=aircraft&current=1"
       class="btn btn-sm btn-success">
       Unmark as Sold
    </a>

    <a href="edit-aircraft.php?aircraft_id=<?= $aircraft['aircraft_id']; ?>" class="btn btn-warning btn-sm">Edit</a>

    <a href="?delete_id=<?= $aircraft['aircraft_id']; ?>&vendor_id=<?= $vendor_id; ?>" 
       onclick="return confirm('Are you sure?');" 
       class="btn btn-dark btn-sm">
       Delete
    </a>
</td>

</tr>

<?php endforeach; ?>
<?php else: ?>
<tr><td colspan="12" class="text-center text-muted">No sold aircraft listings.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>



<a href="?approve_all=1&vendor_id=<?= $vendor_id; ?>" class="btn btn-success mt-3">Approve All</a>

</div>

</div>

<?php include "footer.php"; ob_end_flush(); ?>
