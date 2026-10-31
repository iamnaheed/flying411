<?php
session_start();
include "header.php";
include "config.php"; // Database connection

// Fetch rejected engines (status = 'rejected')
$stmt = $pdo->prepare("SELECT * FROM engines WHERE status = 'rejected' ORDER BY created_at DESC");
$stmt->execute();
$rejected_engines = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Rejected Engines</h3>
        <h6 class="font-weight-normal mb-0">List of engines that have been rejected.</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Rejected Engine Products</h4>

            <?php if (count($rejected_engines) > 0): ?>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Engine ID</th>
                      <th>Model</th>
                      <th>Manufacturer</th>
                      <th>Location</th>
                      <th>Engine Type</th>
                      <th>Power/Thrust</th>
                      <th>Condition</th>
                      <th>Price</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($rejected_engines as $engine): ?>
                      <tr>
                        <td><?= htmlspecialchars($engine['engine_id']); ?></td>
                        <td><?= htmlspecialchars($engine['model']); ?></td>
                        <td><?= htmlspecialchars($engine['manufacturer']); ?></td>
                        <td><?= htmlspecialchars($engine['location']); ?></td>
                        <td><?= htmlspecialchars($engine['engine_type']); ?></td>
                        <td><?= htmlspecialchars($engine['power_thrust']); ?></td>
                        <td><?= htmlspecialchars($engine['condition']); ?></td>
                        <td>€<?= number_format($engine['price'], 2); ?></td>
                        <td>
                          <a href="engine_view.php?engine_id=<?= $engine['engine_id']; ?>" class="btn btn-info btn-sm">View</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p>No rejected engine products found.</p>
            <?php endif; ?>

            <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
