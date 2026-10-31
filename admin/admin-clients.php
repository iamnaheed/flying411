<?php
session_start();
include "header.php";
include "config.php"; // Database connection

$search = "";
$whereClause = "WHERE user_role = 'client'";
$params = [];

if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $whereClause .= " AND (business_name LIKE :search OR business_email LIKE :search)";
    $params[':search'] = "%$search%";
}

$query = $pdo->prepare("
    SELECT 
        vendor_id,
        business_name,
        business_email,
        business_phone,
        business_address,
        created_at
    FROM vendors 
    $whereClause
    ORDER BY created_at DESC
");

$query->execute($params);
$clients = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
function formatPhoneNumber($number) {
    // Remove all non-digit characters
    $digits = preg_replace('/\D/', '', $number);

    // Check if number is 10 digits (U.S. format)
    if (strlen($digits) === 10) {
        return '(' . substr($digits, 0, 3) . ') ' . substr($digits, 3, 3) . '-' . substr($digits, 6);
    } else {
        return $number; // Return original if not 10 digits
    }
}
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Manage Clients</h3>
        <h6 class="font-weight-normal mb-0">View and manage client details.</h6>
      </div>
    </div>

    <!-- Search Form -->
    <form method="GET" class="mb-3">
      <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Search by Name or Email" value="<?= htmlspecialchars($search); ?>">
        <div class="input-group-append">
          <button type="submit" class="btn btn-primary">Search</button>
          <a href="admin-clients.php" class="btn btn-secondary">Reset</a>
        </div>
      </div>
    </form>

    <div class="row">
      <div class="col-md-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title">Clients List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Created At</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (count($clients) > 0): ?>
                    <?php foreach ($clients as $client): ?>
                      <tr>
                        <td>#<?= htmlspecialchars($client['vendor_id']); ?></td>
                        <td><?= htmlspecialchars($client['business_name']); ?></td>
                        <td><?= htmlspecialchars($client['business_email']); ?></td>
                       <td><?= htmlspecialchars(formatPhoneNumber($client['business_phone'])); ?></td>
                        <td><?= htmlspecialchars($client['business_address']); ?></td>
                        <td><?= date("d M Y, h:i A", strtotime($client['created_at'])); ?></td>
                        <td>
                          <!-- View Button -->
                          <a href="client_view.php?client_id=<?= $client['vendor_id']; ?>" class="btn btn-info btn-sm">View</a>
                         
                           <a href="edit-client.php?client_id=<?= $client['vendor_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                      <form action="delete-client.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this client?');">
                              <input type="hidden" name="vendor_id" value="<?= $client['vendor_id']; ?>">
                              <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                       <!-- Change Role Button -->
<button type="button" class="btn btn-warning btn-sm" onclick="openChangeRoleModal(<?= $client['vendor_id']; ?>)">Change Role</button>

                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="7" class="text-center">No clients found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div> <!-- End Table -->
          </div>
        </div>
      </div>
    </div> <!-- End Row -->
  </div>
</div>
<!-- Change Role Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1" role="dialog" aria-labelledby="changeRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="change-vendor-role.php">
      <input type="hidden" name="vendor_id" id="modal_vendor_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="changeRoleModalLabel">Change Client Role</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="user_role">Select Role</label>
            <select class="form-control" name="user_role" required>
              <option value="client">Client</option>
              <option value="vendor">Vendor</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
  function openChangeRoleModal(vendorId) {
    document.getElementById('modal_vendor_id').value = vendorId;
    $('#changeRoleModal').modal('show');
  }
</script>


<?php include "footer.php"; ?>
