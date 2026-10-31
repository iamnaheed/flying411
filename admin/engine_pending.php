<?php
session_start();
include "config.php"; // Database connection

// Fetch all pending engine products
$engines_query = $pdo->query("
    SELECT * FROM engines 
    WHERE status = 'pending' 
    ORDER BY created_at DESC
");
$engines = $engines_query->fetchAll(PDO::FETCH_ASSOC);

// Approve engine product
if (isset($_GET['approve']) && isset($_GET['engine_id'])) {
    $engine_id = $_GET['engine_id'];
    $pdo->prepare("UPDATE engines SET status = 'approved' WHERE engine_id = ?")->execute([$engine_id]);
    header("Location: engine_pending.php"); // Refresh page after approval
    exit();
}
include "header.php";
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Pending Engine Products</h3>
        <h6 class="font-weight-normal mb-0">Review and approve pending engine products.</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Pending Engine List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Model</th>
                    <th>Manufacturer</th>
                    <th>Location</th>
                    <th>Year</th>
                    <th>Condition</th>
                    <th>Price (€)</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (count($engines) > 0): ?>
                    <?php foreach ($engines as $engine): ?>
                      <tr>
                        <td>#<?= htmlspecialchars($engine['engine_id']); ?></td>
                        <td><?= htmlspecialchars($engine['model']); ?></td>
                        <td><?= htmlspecialchars($engine['manufacturer']); ?></td>
                        <td><?= htmlspecialchars($engine['location']); ?></td>
                        <td><?= htmlspecialchars($engine['year']); ?></td>
                        <td><?= htmlspecialchars($engine['condition']); ?></td>
                        <td>€<?= number_format($engine['price'], 2); ?></td>
                        <td>
                          <!-- Approve Button -->
                          <a href="engine_pending.php?approve=true&engine_id=<?= $engine['engine_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="8" class="text-center">No pending engines found.</td>
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

<?php include "footer.php"; ?>
