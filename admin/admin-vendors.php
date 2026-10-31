<?php
session_start();
include "header.php";
include "config.php"; // Database connection

// Search functionality
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    $stmt = $pdo->prepare("
        SELECT vendor_id, business_name, business_address, business_phone_code, business_phone, business_email, 
               contact_name, contact_phone_code, contact_phone, status
        FROM vendors
        WHERE user_role = 'vendor' 
          AND (contact_name LIKE :search OR business_email LIKE :search)
        ORDER BY status ASC
    ");
    $stmt->execute(['search' => "%$searchQuery%"]);
} else {
    $stmt = $pdo->query("
        SELECT vendor_id, business_name, business_address, business_phone_code, business_phone, business_email, 
               contact_name, contact_phone_code, contact_phone, status
        FROM vendors
        WHERE user_role = 'vendor'
        ORDER BY status ASC
    ");
}

$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
function formatPhoneNumber($number) {
    // Remove non-digit characters
    $digits = preg_replace('/\D/', '', $number);

    // Format only if it's 10 digits
    if (strlen($digits) === 10) {
        return '(' . substr($digits, 0, 3) . ') ' .
                     substr($digits, 3, 3) . '-' .
                     substr($digits, 6);
    }
    return $number; // fallback to original
}
?>


<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Manage Vendors</h3>
        <h6 class="font-weight-normal mb-0">View and manage vendor details.</h6>
      </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-3">
      <div class="col-md-6">
        <form method="GET" action="">
          <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by Contact Name or Email" value="<?= htmlspecialchars($searchQuery); ?>" style="margin-right:20px;">
            <button type="submit" class="btn btn-primary">Search</button>
          </div>
        </form>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Vendors List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Business Name</th>
                    <th>Business Address</th>
                    <th>Business Phone</th>
                    <th>Email</th>
                    <th>Contact Name</th>
                    <th>Contact Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                 <tbody>
                  <?php if (count($vendors) > 0): ?>
                    <?php foreach ($vendors as $vendor): ?>
                      <tr>
                        <td>#<?= htmlspecialchars($vendor['vendor_id']); ?></td>
                        <td><?= htmlspecialchars($vendor['business_name']); ?></td>
                        <td><?= htmlspecialchars($vendor['business_address']); ?></td>
                        <td><?= htmlspecialchars(formatPhoneNumber($vendor['business_phone'])); ?></td>
                        <td><?= htmlspecialchars($vendor['business_email']); ?></td>
                        <td><?= htmlspecialchars($vendor['contact_name']); ?></td>
                        <td><?= htmlspecialchars(formatPhoneNumber($vendor['contact_phone'])); ?></td>
                        <td>
                          <span class="<?= $vendor['status'] === 'active' ? 'text-success' : 'text-danger'; ?>">
                            <?= ucfirst($vendor['status']); ?>
                          </span>
                        </td>
                        <td>
                          <a href="vendor-details.php?vendor_id=<?= $vendor['vendor_id']; ?>" class="btn btn-info btn-sm">View</a>
                          <?php if ($vendor['status'] === 'inactive'): ?>
                            <a href="change_status.php?vendor_id=<?= $vendor['vendor_id']; ?>&status=active" class="btn btn-success btn-sm">Activate</a>
                          <?php else: ?>
                            <a href="change_status.php?vendor_id=<?= $vendor['vendor_id']; ?>&status=inactive" class="btn btn-warning btn-sm">Deactivate</a>
                          <?php endif; ?>
                          <a href="edit_vendor.php?vendor_id=<?= $vendor['vendor_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                          <a href="delete_vendor.php?vendor_id=<?= $vendor['vendor_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this vendor?');">Delete</a>
                          
                        <!-- Trigger Button -->
<button type="button" class="btn btn-warning btn-sm" onclick="openChangeRoleModal(<?= $vendor['vendor_id']; ?>)">Change Role</button>


                        
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="9" class="text-center">No vendors found.</td>
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

<!-- Role Change Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1" role="dialog" aria-labelledby="changeRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="change-vendor-role.php">
      <input type="hidden" name="vendor_id" id="modal_vendor_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Change Client Role</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Select Role</label>
            <select class="form-control" name="user_role" required>
              <option value="">-- Select --</option>
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



<!-- Script to open and fill modal -->
<script>
  function openChangeRoleModal(vendorId) {
  document.getElementById('modal_vendor_id').value = vendorId;
  $('#changeRoleModal').modal('show');
}

</script>


<?php include "footer.php"; ?>
