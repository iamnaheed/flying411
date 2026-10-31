<?php 
include "header.php";
include "config.php"; // Database connection

if (!isset($_GET['vendor_id'])) {
    die("Vendor ID is required.");
}

$vendor_id = $_GET['vendor_id'];

$stmt = $pdo->prepare("SELECT * FROM vendors WHERE vendor_id = ?");
$stmt->execute([$vendor_id]);
$vendor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendor) {
    die("Vendor not found.");
}

function formatPhoneNumber($number) {
    $digits = preg_replace('/\D/', '', $number);
    if (strlen($digits) === 10) {
        return '(' . substr($digits, 0, 3) . ') ' . substr($digits, 3, 3) . '-' . substr($digits, 6);
    }
    return $number;
}
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Vendor Details</h3>
        <h6 class="font-weight-normal mb-0">View full details of the vendor.</h6>
      </div>
    </div>

    <div class="container py-4">
      <div class="row">
        <div class="col-md-12">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="row align-items-center">

                <!-- Profile Image -->
                <div class="col-md-4 text-center">
                  <img src="../vendors/<?= htmlspecialchars($vendor['profile_picture'] ?: 'uploads/default.png'); ?>" 
                       alt="Profile Picture" 
                       class="img-fluid rounded-circle border shadow" 
                       style="width: 180px; height: 180px; object-fit: cover;">
                  <h5 class="mt-3"><?= htmlspecialchars($vendor['business_name']); ?></h5>
                </div>

                <!-- Vendor Information -->
                <div class="col-md-8">
                  <h4 class="mb-3">Business Information</h4>
                  <p><strong>ID:</strong> #<?= htmlspecialchars($vendor['vendor_id']); ?></p>
                  <p><strong>Business Name:</strong> <?= htmlspecialchars($vendor['business_name']); ?></p>
                  <p><strong>Email:</strong> <?= htmlspecialchars($vendor['business_email']); ?></p>
                  <p><strong>Phone:</strong><?= htmlspecialchars($vendor['business_phone_code']); ?> <?= formatPhoneNumber($vendor['business_phone']); ?></p>
                

                  <h4 class="mt-4">Bank Information</h4>
                  <p><strong>Bank Name:</strong> <?= htmlspecialchars($vendor['bank_name']); ?></p>
                  <p><strong>Account Number:</strong> <?= htmlspecialchars($vendor['account_number']); ?></p>
                  <p><strong>IBAN:</strong> <?= htmlspecialchars($vendor['iban']); ?></p>
                  <p><strong>SWIFT Code:</strong> <?= htmlspecialchars($vendor['swift_code']); ?></p>

                  <h4 class="mt-4">Status</h4>
                  <p>
                    <strong>Current Status:</strong> 
                    <span class="badge <?= $vendor['status'] === 'active' ? 'badge-success' : 'badge-danger'; ?>">
                      <?= ucfirst($vendor['status']); ?>
                    </span>
                  </p>

                  <a href="admin-vendors.php" class="btn btn-secondary mt-3">Back to List</a>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include "footer.php"; ?>
