<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $description = $_POST['description'];
    $discount_amount = $_POST['discount_amount'];
    $expiration_date = !empty($_POST['expiration_date']) ? $_POST['expiration_date'] : NULL;
    $vendor_id = !empty($_POST['vendor_id']) ? $_POST['vendor_id'] : NULL;

    $query = "INSERT INTO discount_coupons (code, description, discount_amount, expiration_date, vendor_id, status) 
              VALUES (:code, :description, :discount_amount, :expiration_date, :vendor_id, 'active')";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'code' => $code,
        'description' => $description,
        'discount_amount' => $discount_amount,
        'expiration_date' => $expiration_date,
        'vendor_id' => $vendor_id
    ]);

    $_SESSION['message'] = "Discount coupon added successfully!";
    header("Location: admin-discounts.php");
    exit();
}

require_once 'header.php';
?>

<div class="container content-wrapper">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <h4 class="mt-4">Add Discount Coupon</h4>
            <form method="post">
                <div class="form-group">
                    <label>Coupon Code</label>
                    <input type="text" name="code" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Discount Amount ($)</label>
                    <input type="number" name="discount_amount" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Expiration Date</label>
                    <input type="date" name="expiration_date" class="form-control">
                </div>
                <div class="form-group">
                    <label>Vendor (Optional)</label>
                    <input type="number" name="vendor_id" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Add Coupon</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
