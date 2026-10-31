<?php
session_start();
include "config.php";

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get Vendor ID
if (!isset($_GET['vendor_id']) || !is_numeric($_GET['vendor_id'])) {
    header("Location: admin-engines.php");
    exit;
}
$vendor_id = (int) $_GET['vendor_id']; // Ensure it's an integer

// Pagination settings
$limit = 50;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Fetch Vendor Details
$stmt = $pdo->prepare("SELECT business_name, business_email FROM vendors WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
    die("Vendor not found.");
}

// Approve Engine
if (isset($_GET['approve_id']) && is_numeric($_GET['approve_id'])) {
    $approve_id = (int) $_GET['approve_id'];
    $pdo->prepare("UPDATE engines SET status = 'approved' WHERE engine_id = ?")->execute([$approve_id]);
    header("Location: engines-listing.php?vendor_id=$vendor_id");
    exit;
}


// Approve Engine
if (isset($_GET['pending_id']) && is_numeric($_GET['pending_id'])) {
    $pending_id = (int) $_GET['pending_id'];
    $pdo->prepare("UPDATE engines SET status = 'pending' WHERE engine_id = ?")->execute([$pending_id]);
    header("Location: engines-listing.php?vendor_id=$vendor_id");
    exit;
}

// Reject Engine
if (isset($_GET['reject_id']) && is_numeric($_GET['reject_id'])) {
    $reject_id = (int) $_GET['reject_id'];
    $pdo->prepare("UPDATE engines SET status = 'rejected' WHERE engine_id = ?")->execute([$reject_id]);
    header("Location: engines-listing.php?vendor_id=$vendor_id");
    exit;
}

// Approve All Engines
if (isset($_GET['approve_all'])) {
    $pdo->prepare("UPDATE engines SET status = 'approved' WHERE vendor_id = ?")->execute([$vendor_id]);
    header("Location: engines-listing.php?vendor_id=$vendor_id");
    exit;
}
// Permanently Delete Engine
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];

    // Fetch file paths before deletion
    $stmt = $pdo->prepare("SELECT 
        (SELECT image_url FROM product_images WHERE product_id = e.engine_id AND product_type = 'engine' LIMIT 1) AS image_url,
        (SELECT document_url FROM product_documents WHERE product_id = e.engine_id AND product_type = 'engine' LIMIT 1) AS document_url
        FROM engines e WHERE engine_id = ?");
    $stmt->execute([$delete_id]);
    $files = $stmt->fetch(PDO::FETCH_ASSOC);

    // Delete image file
    if (!empty($files['image_url']) && file_exists("../vendors/" . $files['image_url'])) {
        unlink("../vendors/" . $files['image_url']);
    }

    // Delete document file
    if (!empty($files['document_url']) && file_exists("../vendors/" . $files['document_url'])) {
        unlink("../vendors/" . $files['document_url']);
    }

    // Delete from product_images and product_documents
    $pdo->prepare("DELETE FROM product_images WHERE product_id = ? AND product_type = 'engine' ")->execute([$delete_id]);
    $pdo->prepare("DELETE FROM product_documents WHERE product_id = ? AND product_type = 'engine'")->execute([$delete_id]);

    // Delete engine
    $pdo->prepare("DELETE FROM engines WHERE engine_id = ?")->execute([$delete_id]);

    header("Location: engines-listing.php?vendor_id=$vendor_id");
    exit;
}

// Fetch Engines with Pagination
$stmt = $pdo->prepare("
    SELECT e.*, 
           (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = e.engine_id AND pi.product_type = 'engine' order by sort_order LIMIT 1) AS image_url,
           (SELECT pd.document_url FROM product_documents pd WHERE pd.product_id = e.engine_id AND pd.product_type = 'engine' LIMIT 1) AS document_url
    FROM engines e
    WHERE e.vendor_id = ?
    ORDER BY e.status ASC
    LIMIT ? OFFSET ?
");
$stmt->bindParam(1, $vendor_id, PDO::PARAM_INT);
$stmt->bindParam(2, $limit, PDO::PARAM_INT);
$stmt->bindParam(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$engines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total records for pagination
$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM engines WHERE vendor_id = ?");
$total_stmt->execute([$vendor_id]);
$total_engines = $total_stmt->fetchColumn();
$total_pages = ceil($total_engines / $limit);

include "header.php";

$active_engines = [];
$sold_engines = [];

foreach ($engines as $engine) {
    if ($engine['is_sold']) {
        $sold_engines[] = $engine;
    } else {
        $active_engines[] = $engine;
    }
}
?>
<style>
    .sold-toggle-btn {
    display: inline-block;
    padding:7px 14px;
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
    <h3>Engines by <?= htmlspecialchars($vendor['business_name']); ?></h3>
    <h6>Email: <?= htmlspecialchars($vendor['business_email']); ?></h6>


<!-- ======================================================= -->
<!-- ACTIVE ENGINE LISTINGS -->
<!-- ======================================================= -->
<h4 class="mt-4">Active Engine Listings</h4>

<div class="table-responsive">
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Image</th>
            <th>Model</th>
            <th>Manufacturer</th>
            <th>Year</th>
            <th>Power/Thrust</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>

<tbody>
<?php if (!empty($active_engines)): ?>
<?php foreach ($active_engines as $engine): ?>

<tr>
    <td>
        <?php 
            $vendorPath = "../vendors/" . $engine['image_url'];
            $clientPath = "../clients/" . $engine['image_url'];
        ?>

        <?php if (!empty($engine['image_url']) && file_exists($vendorPath)): ?>
            <img src="<?= $vendorPath ?>" width="80">
        <?php elseif (!empty($engine['image_url']) && file_exists($clientPath)): ?>
            <img src="<?= $clientPath ?>" width="80">
        <?php else: ?>
            <span>No Image</span>
        <?php endif; ?>
    </td>

    <td><?= htmlspecialchars($engine['model']); ?></td>
    <td><?= htmlspecialchars($engine['manufacturer']); ?></td>
    <td><?= htmlspecialchars($engine['year']); ?></td>
    <td><?= htmlspecialchars($engine['power_thrust']); ?></td>

    <td>
        <?php 
        if ($engine['price_label'] === 'call') {
            echo "Call for Price";
        } else {
            echo "$" . number_format((float)$engine['price'], 2);
        }
        ?>
    </td>

    <td class="<?= $engine['status'] === 'approved' ? 'text-success' : 'text-warning'; ?>">
        <?= ucfirst($engine['status']); ?>
    </td>

    <td>
        <!-- Mark as Sold -->
        <a href="toggle_sold.php?id=<?= $engine['engine_id']; ?>&type=engine&current=0"
           class="btn btn-sm btn-primary">
           Mark as Sold
        </a>

        <a href="?approve_id=<?= $engine['engine_id']; ?>&vendor_id=<?= $vendor_id; ?>" class="btn btn-success btn-sm">Approve</a>
        <a href="?pending_id=<?= $engine['engine_id']; ?>&vendor_id=<?= $vendor_id; ?>" class="btn btn-warning btn-sm">Pending</a>
        <a href="?reject_id=<?= $engine['engine_id']; ?>&vendor_id=<?= $vendor_id; ?>" class="btn btn-danger btn-sm">Reject</a>

        <?php if (!empty($engine['document_url'])): ?>
            <a href="../vendors/<?= htmlspecialchars($engine['document_url']); ?>" target="_blank" class="btn btn-primary btn-sm">View Document</a>
        <?php else: ?>
            <span class="btn btn-secondary btn-sm">No Document</span>
        <?php endif; ?>

        <a href="edit-engine.php?engine_id=<?= $engine['engine_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
        <a href="?delete_id=<?= $engine['engine_id']; ?>&vendor_id=<?= $vendor_id; ?>" onclick="return confirm('Are you sure you want to delete this engine?');" class="btn btn-dark btn-sm">Delete</a>
    </td>

</tr>

<?php endforeach; ?>
<?php else: ?>
<tr><td colspan="8" class="text-center text-muted">No active engine listings.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>




<!-- ======================================================= -->
<!-- SOLD ENGINE LISTINGS -->
<!-- ======================================================= -->
<h4 class="mt-4">Older Engine Listings (Sold)</h4>

<div class="table-responsive">
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Image</th>
            <th>Model</th>
            <th>Manufacturer</th>
            <th>Year</th>
            <th>Power/Thrust</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>

<tbody>
<?php if (!empty($sold_engines)): ?>
<?php foreach ($sold_engines as $engine): ?>

<tr class="table-warning">

    <td>
        <?php 
            $vendorPath = "../vendors/" . $engine['image_url'];
            $clientPath = "../clients/" . $engine['image_url'];
        ?>

        <?php if (!empty($engine['image_url']) && file_exists($vendorPath)): ?>
            <img src="<?= $vendorPath ?>" width="80">
        <?php elseif (!empty($engine['image_url']) && file_exists($clientPath)): ?>
            <img src="<?= $clientPath ?>" width="80">
        <?php else: ?>
            <span>No Image</span>
        <?php endif; ?>
    </td>

    <td><?= htmlspecialchars($engine['model']); ?></td>
    <td><?= htmlspecialchars($engine['manufacturer']); ?></td>
    <td><?= htmlspecialchars($engine['year']); ?></td>
    <td><?= htmlspecialchars($engine['power_thrust']); ?></td>

    <td>
        <?php 
        if ($engine['price_label'] === 'call') {
            echo "Call for Price";
        } else {
            echo "$" . number_format((float)$engine['price'], 2);
        }
        ?>
    </td>

    <td><span class="text-muted">Sold</span></td>

    <td>
        <a href="toggle_sold.php?id=<?= $engine['engine_id']; ?>&type=engine&current=1"
           class="btn btn-sm btn-success">
           Unmark as Sold
        </a>

        <a href="edit-engine.php?engine_id=<?= $engine['engine_id']; ?>" class="btn btn-warning btn-sm">Edit</a>

        <a href="?delete_id=<?= $engine['engine_id']; ?>&vendor_id=<?= $vendor_id; ?>"
           onclick="return confirm('Are you sure you want to delete this engine?');" 
           class="btn btn-dark btn-sm">
           Delete
        </a>
    </td>

</tr>

<?php endforeach; ?>
<?php else: ?>
<tr><td colspan="8" class="text-center text-muted">No sold engine listings.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>



<!-- Approve All -->
<a href="?approve_all=1&vendor_id=<?= $vendor_id; ?>" class="btn btn-success mt-3">Approve All</a>



<!-- Pagination -->
<nav aria-label="Page navigation" style="margin-top:40px;">
    <ul class="pagination">

        <?php if ($page > 1): ?>
            <li class="page-item"><a class="page-link" href="?vendor_id=<?= $vendor_id; ?>&page=<?= $page - 1; ?>">Previous</a></li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?vendor_id=<?= $vendor_id; ?>&page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item"><a class="page-link" href="?vendor_id=<?= $vendor_id; ?>&page=<?= $page + 1; ?>">Next</a></li>
        <?php endif; ?>

    </ul>
</nav>

</div>

</div>

<?php include "footer.php"; ?>
