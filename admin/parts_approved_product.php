<?php
session_start();
include "header.php";
include "config.php"; // Database connection

// Fetch all approved parts
$parts_query = $pdo->query("
    SELECT * FROM parts 
    WHERE status = 'approved' 
    ORDER BY created_at DESC
");
$parts = $parts_query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Approved Parts</h3>
        <h6 class="font-weight-normal mb-0">View all approved parts.</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Parts List</h4>
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
                  <?php if (count($parts) > 0): ?>
                    <?php foreach ($parts as $part): ?>
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
                          <a href="parts_view.php?part_id=<?= $part['part_id']; ?>" class="btn btn-info btn-sm">View</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="9" class="text-center">No approved parts found.</td>
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
