<?php
session_start();
include "header.php";
include "config.php"; // Database connection

// Validate client ID from URL
if (!isset($_GET['client_id']) || !is_numeric($_GET['client_id'])) {
    echo "<script>alert('Invalid request.'); window.location.href='admin-clients.php';</script>";
    exit;
}

$client_id = $_GET['client_id'];

// Fetch client from vendors table where user_role = 'client'
$query = $pdo->prepare("SELECT * FROM vendors WHERE vendor_id = :client_id AND user_role = 'client'");
$query->execute(['client_id' => $client_id]);
$client = $query->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    echo "<script>alert('Client not found.'); window.location.href='admin-clients.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $business_name   = trim($_POST['business_name']);
    $business_email  = trim($_POST['business_email']);
    $business_phone  = trim($_POST['business_phone']);
    $business_address= trim($_POST['business_address']);
   
    $password = !empty($_POST['password']) 
                ? password_hash($_POST['password'], PASSWORD_BCRYPT) 
                : $client['password']; // keep old password if blank

    // Update vendor info
    $update = $pdo->prepare("
        UPDATE vendors SET

            business_name = :business_name,
            business_email = :business_email,
            business_phone = :business_phone,
            business_address = :business_address,
        
            password = :password
        WHERE vendor_id = :client_id AND user_role = 'client'
    ");

    $update->execute([
        
        'business_name'   => $business_name,
        'business_email'  => $business_email,
        'business_phone'  => $business_phone,
        'business_address'=> $business_address,
        
        'password'        => $password,
        'client_id'       => $client_id
    ]);

    echo "<script>alert('Client updated successfully!'); window.location.href='admin-clients.php';</script>";
    exit;
}
?>


<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Edit Client</h3>
                <h6 class="font-weight-normal mb-0">Modify client details and update the record.</h6>
            </div>
        </div>
      <div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Edit Client</h4>
                    <form method="POST">
                        <div class="form-group mb-3">
                            <label>Business Name</label>
                            <input type="text" name="business_name" class="form-control" value="<?= htmlspecialchars($client['business_name']); ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Business Email</label>
                            <input type="email" name="business_email" class="form-control" value="<?= htmlspecialchars($client['business_email']); ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Business Phone</label>
                            <input type="text" name="business_phone" class="form-control" value="<?= htmlspecialchars($client['business_phone']); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label>Business Address</label>
                            <input type="text" name="business_address" class="form-control" value="<?= htmlspecialchars($client['business_address']); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label>Password (Leave blank to keep current password)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Client</button>
                        <a href="admin-clients.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


    </div>
</div>

<?php include "footer.php"; ?>
