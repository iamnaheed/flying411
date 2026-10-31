<?php
session_start();
include "header.php";
include "config.php"; // Database connection

if (!isset($_GET['aircraft_id'])) {
    echo "<script>alert('Invalid request.'); window.location.href='aircraft_approved_product.php';</script>";
    exit;
}

$aircraft_id = intval($_GET['aircraft_id']);

// Fetch aircraft details
$stmt = $pdo->prepare("SELECT * FROM aircrafts WHERE aircraft_id = ?");
$stmt->execute([$aircraft_id]);
$aircraft = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aircraft) {
    echo "<script>alert('Aircraft not found.'); window.location.href='aircraft_approved_product.php';</script>";
    exit;
}

// Fetch product images
$image_stmt = $pdo->prepare("
    SELECT image_url FROM product_images 
    WHERE product_id = ? AND product_type = 'aircraft'
");
$image_stmt->execute([$aircraft_id]);
$images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch product documents
$document_stmt = $pdo->prepare("
    SELECT document_url FROM product_aircraft_documents 
    WHERE product_id = ?
");
$document_stmt->execute([$aircraft_id]);
$documents = $document_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Aircraft Details</h3>
        <h6 class="font-weight-normal mb-0">Detailed view of the aircraft product.</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title"><?= htmlspecialchars($aircraft['model']); ?> - Details</h4>
            <table class="table">
              <tr>
                <th>Model</th>
                <td><?= htmlspecialchars($aircraft['model']); ?></td>
              </tr>
              <tr>
                <th>Category</th>
                <td><?= htmlspecialchars($aircraft['category']); ?></td>
              </tr>
              <tr>
                <th>Location</th>
                <td><?= htmlspecialchars($aircraft['location']); ?></td>
              </tr>
              <tr>
                <th>Manufacturer</th>
                <td><?= htmlspecialchars($aircraft['manufacturer']); ?></td>
              </tr>
              <tr>
                <th>Condition</th>
                <td><?= htmlspecialchars($aircraft['condition']); ?></td>
              </tr>
              <tr>
                <th>Year</th>
                <td><?= htmlspecialchars($aircraft['year']); ?></td>
              </tr>
              <tr>
                <th>Total Time (Hours)</th>
                <td><?= htmlspecialchars($aircraft['total_time_hours']); ?></td>
              </tr>
              <tr>
                <th>Engine SMH</th>
                <td><?= htmlspecialchars($aircraft['engine_smh_hours']); ?></td>
              </tr>
              <tr>
                <th>Price</th>
                <td>€<?= number_format($aircraft['price'], 2); ?></td>
              </tr>
              <tr>
                <th>Description</th>
                <td><?= htmlspecialchars($aircraft['description']); ?></td>
              </tr>
              <tr>
                <th>Features</th>
                <td><?= htmlspecialchars($aircraft['features']); ?></td>
              </tr>
              <tr>
                <th>Warranty</th>
                <td><?= htmlspecialchars($aircraft['warranty']); ?></td>
              </tr>
            </table>

            <h4 class="mt-4">Aircraft Images</h4>
            <div class="row">
              <?php if (count($images) > 0): ?>
                <?php foreach ($images as $image): ?>
                  <div class="col-md-3 mb-3">
                    <img src="../vendors/<?= htmlspecialchars($image['image_url']); ?>" class="img-fluid rounded shadow" alt="Aircraft Image">
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p>No images available for this aircraft.</p>
              <?php endif; ?>
            </div>

            <h4 class="mt-4">Documents</h4>
            <ul>
              <?php if (count($documents) > 0): ?>
                <?php foreach ($documents as $document): ?>
                  <li><a href="../vendors/<?= htmlspecialchars($document['document_url']); ?>" target="_blank">View Document</a></li>
                <?php endforeach; ?>
              <?php else: ?>
                <p>No documents available for this aircraft.</p>
              <?php endif; ?>
            </ul>

            <a href="aircraft_approved_product.php" class="btn btn-secondary mt-3">Back to List</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
