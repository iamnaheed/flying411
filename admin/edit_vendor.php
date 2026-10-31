<?php
session_start();
include "header.php";
include "config.php"; // Database connection

// Check if vendor_id is provided
if (!isset($_GET['vendor_id']) || empty($_GET['vendor_id'])) {
    echo "<script>alert('Invalid vendor ID.'); window.location.href='manage_vendors.php';</script>";
    exit;
}

$vendor_id = $_GET['vendor_id'];

// Fetch vendor details
$stmt = $pdo->prepare("SELECT * FROM vendors WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
    echo "<script>alert('Vendor not found.'); window.location.href='manage_vendors.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $business_name = $_POST['business_name'];
    $business_phone_code = $_POST['business_phone_code'];
    $business_phone = $_POST['business_phone'];
    $business_email = $_POST['business_email'];
    $contact_name = $_POST['contact_name'];
    $contact_phone_code = $_POST['contact_phone_code'];
    $contact_phone = $_POST['contact_phone'];
    $status = $_POST['status'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $update_stmt = $pdo->prepare("UPDATE vendors SET 
            business_name=?,  business_phone_code=?, business_phone=?, 
            business_email=?, contact_name=?, contact_phone_code=?, contact_phone=?, status=?, password=?
            WHERE vendor_id=?");
        $update_params = [$business_name,  $business_phone_code, $business_phone, 
                          $business_email, $contact_name, $contact_phone_code, $contact_phone, $status, 
                          $hashed_password, $vendor_id];
    } else {
        $update_stmt = $pdo->prepare("UPDATE vendors SET 
            business_name=?,  business_phone_code=?, business_phone=?, 
            business_email=?, contact_name=?, contact_phone_code=?, contact_phone=?, status=?
            WHERE vendor_id=?");
        $update_params = [$business_name,  $business_phone_code, $business_phone, 
                          $business_email, $contact_name, $contact_phone_code, $contact_phone, $status, $vendor_id];
    }

    if ($update_stmt->execute($update_params)) {
        echo "<script>alert('Vendor updated successfully!'); window.location.href='admin-vendors.php';</script>";
    } else {
        echo "<script>alert('Error updating vendor.');</script>";
    }
}
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Edit Vendor</h3>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Vendor Details</h4>
            <form method="POST">
              <div class="form-group">
                <label>Business Name</label>
                <input type="text" name="business_name" class="form-control" value="<?= htmlspecialchars($vendor['business_name']); ?>" required>
              </div>
              <div class="form-group">
                <label>Business Phone</label>
                <div class="d-flex">
                  <input type="text" name="business_phone_code" class="form-control" style="width: 20%; margin-right: 10px;" value="<?= htmlspecialchars($vendor['business_phone_code']); ?>" required>
                  <input type="text" name="business_phone" class="form-control" value="<?= htmlspecialchars($vendor['business_phone']); ?>" required>
                </div>
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="business_email" class="form-control" value="<?= htmlspecialchars($vendor['business_email']); ?>" required>
              </div>
              <div class="form-group">
                <label>Contact Name</label>
                <input type="text" name="contact_name" class="form-control" value="<?= htmlspecialchars($vendor['contact_name']); ?>" required>
              </div>
              <div class="form-group">
                <label>Contact Phone</label>
                <div class="d-flex">
                  <input type="text" name="contact_phone_code" class="form-control" style="width: 20%; margin-right: 10px;" value="<?= htmlspecialchars($vendor['contact_phone_code']); ?>" required>
                  <input type="text" name="contact_phone" class="form-control" value="<?= htmlspecialchars($vendor['contact_phone']); ?>" required>
                </div>
              </div>
              <div class="form-group">
                <label>Password (Leave blank to keep current)</label>
                <input type="password" name="password" class="form-control" placeholder="Enter new password if changing">
              </div>
              <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                  <option value="active" <?= $vendor['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                  <option value="inactive" <?= $vendor['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary">Update Vendor</button>
              <a href="admin-vendors.php" class="btn btn-secondary">Cancel</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
