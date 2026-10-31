<?php
session_start();
include "header.php";
include "config.php"; // Database connection

// Fetch all approved engine products
$engines_query = $pdo->query("
    SELECT * FROM engines 
    WHERE status = 'approved' 
    ORDER BY created_at DESC
");
$engines = $engines_query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Approved Engine Products</h3>
        <h6 class="font-weight-normal mb-0">View all engine products that are approved.</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Engine List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Model</th>
                    <th>Manufacturer</th>
                    <th>Location</th>
                    <th>Engine Type</th>
                    <th>Power Thrust</th>
                    <th>Year</th>
                    <th>Total Time (Hours)</th>
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
                        <td><?= htmlspecialchars($engine['engine_type']); ?></td>
                        <td><?= htmlspecialchars($engine['power_thrust']); ?></td>
                        <td><?= htmlspecialchars($engine['year']); ?></td>
                        <td><?= htmlspecialchars($engine['total_time_hours']); ?></td>
                        <td><?= htmlspecialchars($engine['condition']); ?></td>
                        <td>€<?= number_format($engine['price'], 2); ?></td>
                        <td>
                          <!-- View Details Button -->
                          <a href="engine_view.php?engine_id=<?= $engine['engine_id']; ?>" class="btn btn-info btn-sm">View</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="11" class="text-center">No approved engines found.</td>
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
