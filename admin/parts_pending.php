<?php
ob_start();
session_start();
include "config.php";

// Approve part functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve'])) {
    $part_id = intval($_POST['part_id']);
    
    $update_stmt = $pdo->prepare("UPDATE parts SET status = 'approved' WHERE part_id = ?");
    if ($update_stmt->execute([$part_id])) {
        echo "<script>alert('Part approved successfully!'); window.location.href='parts_pending.php';</script>";
    } else {
        echo "<script>alert('Failed to approve the part. Try again.'); window.location.href='parts_pending.php';</script>";
    }
}

// Fetch all pending parts
$pending_parts_query = $pdo->query("SELECT * FROM parts WHERE status = 'pending' ORDER BY created_at DESC");
$pending_parts = $pending_parts_query->fetchAll(PDO::FETCH_ASSOC);

include "header.php";
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Pending Parts</h3>
        <h6 class="font-weight-normal mb-0">Approve or reject pending parts.</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Pending Parts List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Part Name</th>
                    <th>Part Number</th>
                    <th>Type</th>
                    <th>Condition</th>
                    <th>Region</th>
                    <th>Price (€)</th>
                    <th>Warranty</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (count($pending_parts) > 0): ?>
                    <?php foreach ($pending_parts as $part): ?>
                      <tr>
                        <td>#<?= htmlspecialchars($part['part_id']); ?></td>
                        <td><?= htmlspecialchars($part['part_name']); ?></td>
                        <td><?= htmlspecialchars($part['part_number']); ?></td>
                        <td><?= htmlspecialchars($part['type']); ?></td>
                        <td><?= htmlspecialchars($part['condition']); ?></td>
                        <td><?= htmlspecialchars($part['region']); ?></td>
                        <td>€<?= number_format($part['price'], 2); ?></td>
                        <td><?= htmlspecialchars($part['warranty']); ?></td>
                        <td>
                          <form method="POST" style="display:inline;">
                            <input type="hidden" name="part_id" value="<?= $part['part_id']; ?>">
                            <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="9" class="text-center">No pending parts found.</td>
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
