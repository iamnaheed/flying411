<?php
session_start();
include "header.php";
include "config.php"; // Database connection

if (!isset($_GET['engine_id'])) {
    echo "<script>alert('Invalid request.'); window.location.href='engine_approved_product.php';</script>";
    exit;
}

$engine_id = intval($_GET['engine_id']);

// Fetch engine details
$stmt = $pdo->prepare("SELECT * FROM engines WHERE engine_id = ?");
$stmt->execute([$engine_id]);
$engine = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$engine) {
    echo "<script>alert('Engine not found.'); window.location.href='engine_approved_product.php';</script>";
    exit;
}

// Fetch product images
$image_stmt = $pdo->prepare("
    SELECT image_url FROM product_images 
    WHERE product_id = ? AND product_type = 'engine'
");
$image_stmt->execute([$engine_id]);
$images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch product documents
$document_stmt = $pdo->prepare("
    SELECT document_url FROM product_documents 
    WHERE product_id = ? AND product_type = 'engine'
");
$document_stmt->execute([$engine_id]);
$documents = $document_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Engine Details</h3>
        <h6 class="font-weight-normal mb-0">Detailed view of the engine product.</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title"><?= htmlspecialchars($engine['model']); ?> - Details</h4>
            <table class="table">
              <tr>
                <th>Model</th>
                <td><?= htmlspecialchars($engine['model']); ?></td>
              </tr>
              <tr>
                <th>Manufacturer</th>
                <td><?= htmlspecialchars($engine['manufacturer']); ?></td>
              </tr>
              <tr>
                <th>Location</th>
                <td><?= htmlspecialchars($engine['location']); ?></td>
              </tr>
              <tr>
                <th>Engine Type</th>
                <td><?= htmlspecialchars($engine['engine_type']); ?></td>
              </tr>
              <tr>
                <th>Power/Thrust</th>
                <td><?= htmlspecialchars($engine['power_thrust']); ?></td>
              </tr>
              <tr>
                <th>Condition</th>
                <td><?= htmlspecialchars($engine['condition']); ?></td>
              </tr>
              <tr>
                <th>Year</th>
                <td><?= htmlspecialchars($engine['year']); ?></td>
              </tr>
              <tr>
                <th>Total Time (Hours)</th>
                <td><?= htmlspecialchars($engine['total_time_hours']); ?></td>
              </tr>
              <tr>
                <th>HR</th>
                <td><?= htmlspecialchars($engine['hr']); ?></td>
              </tr>
              <tr>
                <th>Cycles</th>
                <td><?= htmlspecialchars($engine['cycles']); ?></td>
              </tr>
              <tr>
                <th>Price</th>
                <td>€<?= number_format($engine['price'], 2); ?></td>
              </tr>
              <tr>
                <th>Extra Details</th>
                <td><?= htmlspecialchars($engine['extra_details']); ?></td>
              </tr>
              <tr>
                <th>Tags</th>
                <td><?= htmlspecialchars($engine['tags']); ?></td>
              </tr>
              <tr>
                <th>Warranty</th>
                <td><?= htmlspecialchars($engine['warranty']); ?></td>
              </tr>
            </table>

            <h4 class="mt-4">Engine Images</h4>
            <div class="row">
              <?php if (count($images) > 0): ?>
                <?php foreach ($images as $image): ?>
                  <div class="col-md-3 mb-3">
                    <img src="../vendors/<?= htmlspecialchars($image['image_url']); ?>" class="img-fluid rounded shadow" alt="Engine Image">
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p>No images available for this engine.</p>
              <?php endif; ?>
            </div>

            <h4 class="mt-4">Documents</h4>
            <ul>
              <?php if (count($documents) > 0): ?>
                <?php foreach ($documents as $document): ?>
                  <li><a href="../vendors/<?= htmlspecialchars($document['document_url']); ?>" target="_blank">View Document</a></li>
                <?php endforeach; ?>
              <?php else: ?>
                <p>No documents available for this engine.</p>
              <?php endif; ?>
            </ul>

            <a href="engine_approved_product.php" class="btn btn-secondary mt-3">Back to List</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
