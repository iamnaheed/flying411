<?php
ob_start();
session_start();
include "config.php"; // Database connection

// Approve aircraft when the button is clicked
if (isset($_GET['approve_id'])) {
    $approve_id = $_GET['approve_id'];
    $update_stmt = $pdo->prepare("UPDATE aircrafts SET status = 'approved' WHERE aircraft_id = ?");
    $update_stmt->execute([$approve_id]);
    header("Location: aircraft_pending.php");
    exit;
}

// Fetch all pending aircraft products
$aircrafts_query = $pdo->query("
    SELECT * FROM aircrafts 
    WHERE status = 'pending' 
    ORDER BY created_at DESC
");
$aircrafts = $aircrafts_query->fetchAll(PDO::FETCH_ASSOC);
include "header.php";
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Pending Aircraft Products</h3>
        <h6 class="font-weight-normal mb-0">Review and approve pending aircraft products.</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Pending Aircraft List</h4>
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
                          <!-- Approve Button -->
                          <a href="aircraft_pending.php?approve_id=<?= $aircraft['aircraft_id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this aircraft?');">Approve</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="11" class="text-center">No pending aircraft found.</td>
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

<?php include "footer.php";
obs_end();
?>
