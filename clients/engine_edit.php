<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "header.php";
include "config.php";

// Ensure user is logged in
if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];
$engine_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch engine details
$query = $pdo->prepare("SELECT * FROM engines WHERE engine_id = ? AND vendor_id = ?");
$query->execute([$engine_id, $vendor_id]);
$engine = $query->fetch(PDO::FETCH_ASSOC);

if (!$engine) {
    echo "<script>alert('Engine not found or you do not have permission to edit it.'); window.location.href = 'engine_list.php';</script>";
    exit();
}

// Fetch engine images
$image_query = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND product_type = 'engine'");
$image_query->execute([$engine_id]);
$images = $image_query->fetchAll(PDO::FETCH_COLUMN, 0);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = $_POST['model'];
    $manufacturer = $_POST['manufacturer'];
    $location = $_POST['location'];
    $engine_type = $_POST['engine_type'];
    $power_thrust = $_POST['power_thrust'];
    $year = $_POST['year'];
    $total_time_hours = $_POST['total_time_hours'];
    $hr = $_POST['hr'];
    $cycles = $_POST['cycles'];
    $condition = $_POST['condition'];
    $price = $_POST['price'];
    $extra_details = $_POST['extra_details'];
    $warranty = $_POST['warranty'];
    
    // Update engine details
   $update_query = $pdo->prepare("UPDATE engines SET 
        model = ?, manufacturer = ?, location = ?, engine_type = ?, power_thrust = ?,
        year = ?, total_time_hours = ?, hr = ?, cycles = ?, `condition` = ?, price = ?,
        extra_details = ?, warranty = ?
    WHERE engine_id = ? AND vendor_id = ?");
    $update_query->execute([
        $model, $manufacturer, $location, $engine_type, $power_thrust,
        $year, $total_time_hours, $hr, $cycles, $condition, $price,
        $extra_details, $warranty, $engine_id, $vendor_id
    ]);

    // Handle image uploads
    if (!empty($_FILES['images']['name'][0])) {
        $delete_images_query = $pdo->prepare("DELETE FROM product_images WHERE product_id = ? AND product_type = 'engine'");
        $delete_images_query->execute([$engine_id]);

        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $image_name = basename($_FILES['images']['name'][$key]);
            $image_path = "uploads/" . uniqid() . "_" . $image_name;
            if (move_uploaded_file($tmp_name, $image_path)) {
                $insert_image_query = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url) VALUES (?, 'engine', ?)");
                $insert_image_query->execute([$engine_id, $image_path]);
            }
        }
    }

    echo "<script>alert('Engine updated successfully!'); window.location.href = 'manage_products.php';</script>";
    exit();
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Edit Engine</h3>
                <h6 class="font-weight-normal mb-0">Update your engine details here.</h6>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Engine Information</h4>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="engine_id" value="<?= htmlspecialchars($engine['engine_id']); ?>">
                            
                            <div class="form-group">
                                <label for="model">Model</label>
                                <input type="text" class="form-control" id="model" name="model" value="<?= htmlspecialchars($engine['model']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="manufacturer">Manufacturer</label>
                                <input type="text" class="form-control" id="manufacturer" name="manufacturer" value="<?= htmlspecialchars($engine['manufacturer']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($engine['location']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="engine_type">Engine Type</label>
                                <input type="text" class="form-control" id="engine_type" name="engine_type" value="<?= htmlspecialchars($engine['engine_type']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="engine_type"> Cycles</label>
                                <input type="text" class="form-control" id="cycles" name="cycles" value="<?= htmlspecialchars($engine['cycles']); ?>" required>
                            </div>
                             <div class="form-group">
                                <label for="engine_type"> HR</label>
                                <input type="text" class="form-control" id="hr" name="hr" value="<?= htmlspecialchars($engine['hr']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="power_thrust">Power/Thrust</label>
                                <input type="text" class="form-control" id="power_thrust" name="power_thrust" value="<?= htmlspecialchars($engine['power_thrust']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="year">Year</label>
                                <input type="number" class="form-control" id="year" name="year" value="<?= htmlspecialchars($engine['year']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="total_time_hours">Total Time Hours</label>
                                <input type="number" class="form-control" id="total_time_hours" name="total_time_hours" value="<?= htmlspecialchars($engine['total_time_hours']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="condition">Condition</label>
                                <input type="text" class="form-control" id="condition" name="condition" value="<?= htmlspecialchars($engine['condition']); ?>" required>
                            </div>
                             <div class="form-group">
                                <label for="condition">price</label>
                                <input type="text" class="form-control" id="price" name="price" value="<?= htmlspecialchars($engine['price']); ?>" required>
                            </div>
                             <div class="form-group">
                                <label for="condition">Extra_details</label>
                                <input type="text" class="form-control" id="extra_details" name="extra_details" value="<?= htmlspecialchars($engine['extra_details']); ?>" required>
                            </div>
                             <div class="form-group">
                                <label for="condition">warranty</label>
                                <input type="text" class="form-control" id="warranty" name="warranty" value="<?= htmlspecialchars($engine['warranty']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="images">Engine Images</label>
                                <input type="file" class="form-control" id="images" name="images[]" multiple>
                                <small>Current Images:</small>
                                <?php foreach ($images as $image): ?>
                                    <img src="<?= htmlspecialchars($image); ?>" class="img-thumbnail" width="100" alt="Engine Image">
                                <?php endforeach; ?>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Engine</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>