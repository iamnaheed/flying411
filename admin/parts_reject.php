<?php
session_start();
include "header.php";
include "config.php";

// Fetch rejected parts
$rejected_parts_query = $pdo->query("SELECT * FROM parts WHERE status = 'rejected'");
$rejected_parts = $rejected_parts_query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Rejected Parts</h3>
        <h6 class="font-weight-normal mb-0">List of parts that have been rejected.</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Rejected Parts List</h4>
            <?php if (count($rejected_parts) > 0): ?>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Part Name</th>
                      <th>Part Number</th>
                      <th>Type</th>
                      <th>Condition</th>
                      <th>Region</th>
                      <th>Price</th>
                      <th>Rejection Reason</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($rejected_parts as $index => $part): ?>
                      <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= htmlspecialchars($part['part_name']); ?></td>
                        <td><?= htmlspecialchars($part['part_number']); ?></td>
                        <td><?= htmlspecialchars($part['type']); ?></td>
                        <td><?= htmlspecialchars($part['condition']); ?></td>
                        <td><?= htmlspecialchars($part['region']); ?></td>
                        <td>€<?= number_format($part['price'], 2); ?></td>
                        <td>
                          <?= (strpos($part['extra_details'], 'Rejection Reason:') !== false) 
                              ? explode('Rejection Reason: ', $part['extra_details'])[1] 
                              : 'No reason provided'; ?>
                        </td>
                        <td>
                          <a href="parts_view.php?part_id=<?= $part['part_id']; ?>" class="btn btn-info btn-sm">View</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <p>No rejected parts available.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
