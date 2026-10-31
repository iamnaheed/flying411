<?php

include "config.php";
session_start();

if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

try {
    $stmt = $pdo->prepare("SELECT aircraft_id, model FROM aircrafts WHERE vendor_id = ?");
    $stmt->execute([$vendor_id]);
    $aircrafts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching aircrafts: " . $e->getMessage());
}

include "header.php";
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row mb-5">
      <div class="col-12 col-xl-5 mb-4 mb-xl-0">
        <h3 class="font-weight-bold">My Backlinks</h3>
        <h6 class="font-weight-normal mb-0">View and use backlinks for your aircraft listings to boost SEO.</h6>
      </div>
       <div class="col-12 col-xl-7 pages-links">
        <div class="d-flex justify-content-between flex-wrap">
          <a href="addaircraft.php" class="btn  mb-2" style="background-color:#4747A1;color:white;" >List An Aircraft</a>
           <a href="addparts.php" class="btn  mb-2" style="background-color:#4747A1;color:white;">List A Part</a>
          <a href="addengines.php" class="btn  mb-2" style="background-color:#4747A1;color:white;">List An Engine</a>
         
          <a href="add_services.php" class="btn  mb-2" style="background-color:#4747A1;color:white;">List A Service</a>
          <a href="manage_products.php" class="btn  mb-2" style="background-color:#4747A1;color:white;">My Listings</a>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Backlink List</h4>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Model</th>
                    <th>Backlink URL</th>
                    <th>Copy</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (count($aircrafts) > 0): ?>
                    <?php foreach ($aircrafts as $aircraft): ?>
                      <?php
                        $link = "https://flying411.com/{$aircraft['model']}";
                      ?>
                      <tr>
                        <td><?= htmlspecialchars($aircraft['model']); ?></td>
                        <td><a href="<?= $link ?>" target="_blank"><?= $link ?></a></td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('<?= $link ?>')">Copy</button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="3" class="text-center">No aircraft listings found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(function () {
    alert("Link copied to clipboard!");
  }, function () {
    alert("Failed to copy.");
  });
}
</script>

<?php include "footer.php"; ?>
