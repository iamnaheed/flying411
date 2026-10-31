<?php
session_start();
include "config.php";

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get Vendor ID
if (!isset($_GET['vendor_id'])) {
    header("Location: admin-parts.php");
    exit;
}
$vendor_id = $_GET['vendor_id'];

// Fetch Vendor Details
$stmt = $pdo->prepare("SELECT business_name, business_email FROM vendors WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

// Approve Part
if (isset($_GET['approve_id'])) {
    $approve_id = $_GET['approve_id'];
    $pdo->prepare("UPDATE parts SET status = 'approved' WHERE part_id = ?")->execute([$approve_id]);
    header("Location: parts-listing.php?vendor_id=$vendor_id");
    exit;
}

// Reject Part
if (isset($_GET['reject_id'])) {
    $reject_id = $_GET['reject_id'];
    $pdo->prepare("UPDATE parts SET status = 'rejected' WHERE part_id = ?")->execute([$reject_id]);
    header("Location: parts-listing.php?vendor_id=$vendor_id");
    exit;
}
if (isset($_GET['pending_id'])) {
    $pending_id = $_GET['pending_id'];
    $pdo->prepare("UPDATE parts SET status = 'pending' WHERE part_id = ?")->execute([$pending_id]);
    header("Location: parts-listing.php?vendor_id=$vendor_id");
    exit;
}

// Approve All Parts
if (isset($_GET['approve_all'])) {
    $pdo->prepare("UPDATE parts SET status = 'approved' WHERE vendor_id = ?")->execute([$vendor_id]);
    header("Location: parts-listing.php?vendor_id=$vendor_id");
    exit;
}
// Delete Part, Image, and Document
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete image file from server
    $img_stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND product_type = 'part'");
    $img_stmt->execute([$delete_id]);
    $image = $img_stmt->fetch(PDO::FETCH_ASSOC);
    if ($image && file_exists("../vendors/" . $image['image_url'])) {
        unlink("../vendors/" . $image['image_url']);
    }

    // Delete document file from server
    $doc_stmt = $pdo->prepare("SELECT document_url FROM product_parts_documents WHERE product_id = ?");
    $doc_stmt->execute([$delete_id]);
    $document = $doc_stmt->fetch(PDO::FETCH_ASSOC);
    if ($document && file_exists("../vendors/" . $document['document_url'])) {
        unlink("../vendors/" . $document['document_url']);
    }

    // Delete from related tables
    $pdo->prepare("DELETE FROM product_images WHERE product_id = ? AND product_type = 'part'")->execute([$delete_id]);
    $pdo->prepare("DELETE FROM product_parts_documents WHERE product_id = ?")->execute([$delete_id]);
    $pdo->prepare("DELETE FROM parts WHERE part_id = ?")->execute([$delete_id]);

    header("Location: parts-listing.php?vendor_id=$vendor_id");
    exit;
}

// Fetch Parts with Images & Documents
$stmt = $pdo->prepare("
    SELECT p.*, 
           (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.part_id AND pi.product_type = 'part' order by sort_order LIMIT 1) AS image_url,
           (SELECT pd.document_url FROM product_parts_documents pd WHERE pd.product_id = p.part_id LIMIT 1) AS document_url
    FROM parts p
    WHERE p.vendor_id = ?
    GROUP BY p.part_id
    ORDER BY p.status ASC
");
$stmt->execute([$vendor_id]);
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "header.php";

$active_parts = [];
$sold_parts = [];

foreach ($parts as $part) {
    if ($part['is_sold']) {
        $sold_parts[] = $part;
    } else {
        $active_parts[] = $part;
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
    <h3>Parts by <?= htmlspecialchars($vendor['business_name']); ?></h3>
    <h6>Email: <?= htmlspecialchars($vendor['business_email']); ?></h6>


<!-- ======================================================= -->
<!-- ACTIVE PARTS LISTINGS -->
<!-- ======================================================= -->

<h4 class="mt-4">Active Parts Listings</h4>

<div class="table-responsive">
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Image</th>
            <th>Part Number</th>
            <th>Condition</th>
            <th>Region</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>

<tbody>
<?php if (!empty($active_parts)): ?>
<?php foreach ($active_parts as $part): ?>

<tr>

    <td>
        <?php 
            $vendorPath = "../vendors/" . $part['image_url'];
            $clientPath = "../clients/" . $part['image_url'];
             $uploadPath  = "../vendors/" . $part['image_url'];
        ?>

        <?php if (!empty($part['image_url']) && file_exists($vendorPath)): ?>
            <img src="<?= $vendorPath ?>" width="80">
        <?php elseif (!empty($part['image_url']) && file_exists($clientPath)): ?>
            <img src="<?= $clientPath ?>" width="80">
            <?php elseif (!empty($part['image_url'])): ?>
        <img src="<?= $uploadPath ?>" width="80">
        <?php else: ?>
            <span class="text-muted">No Image</span>
        <?php endif; ?>
    </td>

    <td><?= htmlspecialchars($part['part_number']); ?></td>
    <td><?= htmlspecialchars($part['condition']); ?></td>
    <td><?= htmlspecialchars($part['region']); ?></td>

    <td>
        <?php 
        if ($part['price_label'] === 'call') {
            echo "Call for Price";
        } else {
            echo "$" . number_format($part['price'], 2);
        }
        ?>
    </td>

    <td class="<?= $part['status'] === 'approved' ? 'text-success' : 'text-warning'; ?>">
        <?= ucfirst($part['status']); ?>
    </td>

    <td>
        <!-- Mark as Sold -->
        <a href="toggle_sold.php?id=<?= $part['part_id']; ?>&type=part&current=0"
           class="btn btn-sm btn-primary">
           Mark as Sold
        </a>

        <a href="?approve_id=<?= $part['part_id']; ?>&vendor_id=<?= $vendor_id; ?>" class="btn btn-success btn-sm">Approve</a>
        <a href="?pending_id=<?= $part['part_id']; ?>&vendor_id=<?= $vendor_id; ?>" class="btn btn-warning btn-sm">Pending</a>
        <a href="?reject_id=<?= $part['part_id']; ?>&vendor_id=<?= $vendor_id; ?>" class="btn btn-danger btn-sm">Reject</a>

        <?php if (!empty($part['document_url'])): ?>
            <a href="../vendors/<?= htmlspecialchars($part['document_url']) ?>" target="_blank" class="btn btn-primary btn-sm">View Document</a>
        <?php else: ?>
            <span class="btn btn-secondary btn-sm">No Document</span>
        <?php endif; ?>

        <a href="edit-part.php?part_id=<?= $part['part_id']; ?>" class="btn btn-warning btn-sm">Edit</a>

        <a href="?delete_id=<?= $part['part_id']; ?>&vendor_id=<?= $vendor_id; ?>"
           onclick="return confirm('Are you sure you want to delete this part?');"
           class="btn btn-dark btn-sm">
           Delete
        </a>

    </td>

</tr>

<?php endforeach; ?>
<?php else: ?>
<tr><td colspan="7" class="text-center text-muted">No active parts found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>




<!-- ======================================================= -->
<!-- SOLD PARTS LISTINGS -->
<!-- ======================================================= -->

<h4 class="mt-4">Older Parts Listings (Sold)</h4>

<div class="table-responsive">
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Image</th>
            <th>Part Number</th>
            <th>Condition</th>
            <th>Region</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>

<tbody>
<?php if (!empty($sold_parts)): ?>
<?php foreach ($sold_parts as $part): ?>

<tr class="table-warning">

    <td>
        <?php 
            $vendorPath = "../vendors/" . $part['image_url'];
            $clientPath = "../clients/" . $part['image_url'];
        ?>

        <?php if (!empty($part['image_url']) && file_exists($vendorPath)): ?>
            <img src="<?= $vendorPath ?>" width="80">
        <?php elseif (!empty($part['image_url']) && file_exists($clientPath)): ?>
            <img src="<?= $clientPath ?>" width="80">
        <?php else: ?>
            <span class="text-muted">No Image</span>
        <?php endif; ?>
    </td>

    <td><?= htmlspecialchars($part['part_number']); ?></td>
    <td><?= htmlspecialchars($part['condition']); ?></td>
    <td><?= htmlspecialchars($part['region']); ?></td>

    <td>
        <?php 
        if ($part['price_label'] === 'call') {
            echo "Call for Price";
        } else {
            echo "$" . number_format($part['price'], 2);
        }
        ?>
    </td>

    <td><span class="text-muted">Sold</span></td>

    <td>
        <!-- Unmark as sold -->
        <a href="toggle_sold.php?id=<?= $part['part_id']; ?>&type=part&current=1"
           class="btn btn-sm btn-success">
           Unmark as Sold
        </a>

        <a href="edit-part.php?part_id=<?= $part['part_id']; ?>" class="btn btn-warning btn-sm">Edit</a>

        <a href="?delete_id=<?= $part['part_id']; ?>&vendor_id=<?= $vendor_id; ?>"
           onclick="return confirm('Are you sure you want to delete this part?');"
           class="btn btn-dark btn-sm">
           Delete
        </a>
    </td>

</tr>

<?php endforeach; ?>
<?php else: ?>
<tr><td colspan="7" class="text-center text-muted">No sold parts found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>


<!-- Approve All -->
<a href="?approve_all=1&vendor_id=<?= $vendor_id; ?>" class="btn btn-success mt-3">Approve All</a>

</div>

</div>

<?php include "footer.php"; ?>
