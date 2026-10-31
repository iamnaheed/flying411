<?php
session_start();
include "header.php";
include "config.php"; // Database connection

// Check if client_id is provided
if (!isset($_GET['client_id']) || empty($_GET['client_id'])) {
    echo "<script>alert('Invalid Client ID!'); window.location.href='admin-clients.php';</script>";
    exit;
}

$client_id = $_GET['client_id'];

// Fetch client details from vendors table with user_role = 'client'
$query = $pdo->prepare("SELECT * FROM vendors WHERE vendor_id = ? AND user_role = 'client'");
$query->execute([$client_id]);
$client = $query->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    echo "<script>alert('Client not found!'); window.location.href='admin-clients.php';</script>";
    exit;
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
        <h3 class="font-weight-bold">Client Details</h3>
        <h6 class="font-weight-normal mb-0">View full details of the client.</h6>
      </div>
    </div>

  <div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">

                        <!-- Left: Profile Image -->
                        <div class="col-md-4 text-center">
                                <img src="uploads/<?= htmlspecialchars($client['profile_picture'] ?: '../uploads/default.png'); ?>" 
                                     alt="Profile Picture" 
                                     class="img-fluid rounded-circle border shadow" 
                                     style="width: 180px; height: 180px; object-fit: cover;">

                            <h5 class="mt-3"><?= htmlspecialchars($client['business_name']); ?></h5>
                        </div>

                        <!-- Right: Client Info -->
                        <div class="col-md-8">
                            <h4 class="mb-3">Client Information</h4>
                            <p><strong>ID:</strong> #<?= htmlspecialchars($client['vendor_id']); ?></p>
                             <p><strong>Name:</strong> <?= htmlspecialchars($client['business_name']); ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($client['business_email']); ?></p>
                            <p><strong>Phone:</strong> <?= formatPhoneNumber($client['business_phone']); ?></p>
                           
                            <p><strong>Created At:</strong> <?= date("d M Y, h:i A", strtotime($client['created_at'])); ?></p>
                            <a href="admin-clients.php" class="btn btn-secondary mt-2">Back to List</a>
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
