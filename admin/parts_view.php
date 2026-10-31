<?php
session_start();
include "header.php";
include "config.php"; // Database connection

if (!isset($_GET['part_id'])) {
    echo "<script>alert('Invalid request.'); window.location.href='parts_approved_product.php';</script>";
    exit;
}

$part_id = intval($_GET['part_id']);

// Fetch part details
$stmt = $pdo->prepare("SELECT * FROM parts WHERE part_id = ?");
$stmt->execute([$part_id]);
$part = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$part) {
    echo "<script>alert('Part not found.'); window.location.href='parts_approved_product.php';</script>";
    exit;
}

// Fetch product images
$image_stmt = $pdo->prepare("
    SELECT image_url FROM product_images 
    WHERE product_id = ? AND product_type = 'part'
");
$image_stmt->execute([$part_id]);
$images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch product documents
$document_stmt = $pdo->prepare("
    SELECT document_url FROM product_parts_documents 
    WHERE product_id = ?
");
$document_stmt->execute([$part_id]);
$documents = $document_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Part Details</h3>
        <h6 class="font-weight-normal mb-0">Detailed view of the part product.</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title"><?= htmlspecialchars($part['part_name']); ?> - Details</h4>
            <table class="table">
              <tr><th>Part Name</th><td><?= htmlspecialchars($part['part_name']); ?></td></tr>
              <tr><th>Part Number</th><td><?= htmlspecialchars($part['part_number']); ?></td></tr>
              <tr><th>Type</th><td><?= htmlspecialchars($part['type']); ?></td></tr>
              <tr><th>Condition</th><td><?= htmlspecialchars($part['condition']); ?></td></tr>
              <tr><th>Region</th><td><?= htmlspecialchars($part['region']); ?></td></tr>
              <tr><th>Price</th><td>€<?= number_format($part['price'], 2); ?></td></tr>
              <tr><th>Warranty</th><td><?= htmlspecialchars($part['warranty']); ?></td></tr>
            </table>

            <h4 class="mt-4">Part Images</h4>
            <div class="row">
              <?php foreach ($images as $image): ?>
                <div class="col-md-3 mb-3">
                  <img src="../vendors/<?= htmlspecialchars($image['image_url']); ?>" class="img-fluid rounded shadow" alt="Part Image">
                </div>
              <?php endforeach; ?>
            </div>

            <h4 class="mt-4">Documents</h4>
            <ul>
              <?php foreach ($documents as $document): ?>
                <li><a href="../vendors/<?= htmlspecialchars($document['document_url']); ?>" target="_blank">View Document</a></li>
              <?php endforeach; ?>
            </ul>

            <a href="parts_approved_product.php" class="btn btn-secondary mt-3">Back to List</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
