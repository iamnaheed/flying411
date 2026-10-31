<?php
session_start();
include "header.php";
include "config.php"; // Database connection

// Fetch all approved aircraft products
$aircrafts_query = $pdo->query("
    SELECT * FROM aircrafts 
    WHERE status = 'approved' 
    ORDER BY created_at DESC
");
$aircrafts = $aircrafts_query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Approved Aircraft Products</h3>
        <h6 class="font-weight-normal mb-0">View all aircraft products that are approved.</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Aircraft List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Model</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Manufacturer</th>
                    <th>Condition</th>
                    <th>Year</th>
                    <th>Total Time (Hours)</th>
                    <th>Engine SMH</th>
                    <th>Price (€)</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (count($aircrafts) > 0): ?>
                    <?php foreach ($aircrafts as $aircraft): ?>
                      <tr>
                        <td>#<?= htmlspecialchars($aircraft['aircraft_id']); ?></td>
                        <td><?= htmlspecialchars($aircraft['model']); ?></td>
                        <td><?= htmlspecialchars($aircraft['category']); ?></td>
                        <td><?= htmlspecialchars($aircraft['location']); ?></td>
                        <td><?= htmlspecialchars($aircraft['manufacturer']); ?></td>
                        <td><?= htmlspecialchars($aircraft['condition']); ?></td>
                        <td><?= htmlspecialchars($aircraft['year']); ?></td>
                        <td><?= htmlspecialchars($aircraft['total_time_hours']); ?></td>
                        <td><?= htmlspecialchars($aircraft['engine_smh_hours']); ?></td>
                        <td>€<?= number_format($aircraft['price'], 2); ?></td>
                        <td>
                          <!-- View Details Button -->
                          <a href="aircraft_view.php?aircraft_id=<?= $aircraft['aircraft_id']; ?>" class="btn btn-info btn-sm">View</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="11" class="text-center">No approved aircraft found.</td>
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
