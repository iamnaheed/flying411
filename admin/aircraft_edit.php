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

$vendor_id = $_SESSION['vendor_id']; // Corrected session variable
$aircraft_id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Get aircraft ID from URL

// Fetch aircraft details
$query = $pdo->prepare("SELECT 
        aircraft_id, vendor_id, model, category, location, aircraft_type, manufacturer, 
        `condition`, `year`, total_time_hours, engine_smh, price, description, features, warranty, status
    FROM aircrafts 
    WHERE aircraft_id = ? AND vendor_id = ?");
$query->execute([$aircraft_id, $vendor_id]);
$aircraft = $query->fetch(PDO::FETCH_ASSOC);

if (!$aircraft) {
    echo "<script>alert('Aircraft not found or you do not have permission to edit it.'); window.location.href = 'aircraft_list.php';</script>";
    exit();
}

// Fetch aircraft images
$image_query = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND product_type = 'aircraft'");
$image_query->execute([$aircraft_id]);
$images = $image_query->fetchAll(PDO::FETCH_COLUMN, 0);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $model = $_POST['model'];
    $location = $_POST['location'];
    $aircraft_type = $_POST['aircraft_type'];
    $manufacturer = $_POST['manufacturer'];
    $condition = $_POST['condition'];
    $year = $_POST['year'];
    $total_time_hours = $_POST['total_time_hours'];
    $engine_smh = $_POST['engine_smh'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $features = $_POST['features'];
    $warranty = $_POST['warranty'];

    // Update aircraft details
    $update_query = $pdo->prepare("UPDATE aircrafts SET 
            model = ?, location = ?, aircraft_type = ?, manufacturer = ?, 
            `condition` = ?, `year` = ?, total_time_hours = ?, engine_smh = ?, price = ?, 
            description = ?, features = ?, warranty = ?
        WHERE aircraft_id = ? AND vendor_id = ?");
    $update_query->execute([
        $model, $location, $aircraft_type, $manufacturer, $condition, $year, 
        $total_time_hours, $engine_smh, $price, $description, $features, $warranty, 
        $aircraft_id, $vendor_id
    ]);

    // Handle image uploads
    if (!empty($_FILES['images']['name'][0])) {
        // Delete old images
        $delete_images_query = $pdo->prepare("DELETE FROM product_images WHERE product_id = ? AND product_type = 'aircraft'");
        $delete_images_query->execute([$aircraft_id]);

        // Upload new images
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $image_name = basename($_FILES['images']['name'][$key]);
            $image_path = "uploads/" . uniqid() . "_" . $image_name;
            if (move_uploaded_file($tmp_name, $image_path)) {
                $insert_image_query = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url) VALUES (?, 'aircraft', ?)");
                $insert_image_query->execute([$aircraft_id, $image_path]);
            }
        }
    }

    echo "<script>alert('Aircraft updated successfully!'); window.location.href = 'manage_products.php';</script>";
    exit();
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Edit Aircraft</h3>
                <h6 class="font-weight-normal mb-0">
                    Update your aircraft details here.
                </h6>
            </div>
        </div>

        <!-- Aircraft Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Aircraft Information</h4>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <!-- Hidden Fields -->
                            <input type="hidden" name="aircraft_id" value="<?= htmlspecialchars($aircraft['aircraft_id']); ?>">

                            <!-- Model -->
                            <div class="form-group">
                                <label for="model">Model</label>
                                <input type="text" class="form-control" id="model" name="model" value="<?= htmlspecialchars($aircraft['model'] ?? ''); ?>" required>
                            </div>

                            <!-- Location -->
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($aircraft['location'] ?? ''); ?>" required>
                            </div>

                            <!-- Aircraft Type -->
                            <div class="form-group">
                                <label for="aircraft_type">Aircraft Type</label>
                                <input type="text" class="form-control" id="aircraft_type" name="aircraft_type" value="<?= htmlspecialchars($aircraft['aircraft_type'] ?? ''); ?>" required>
                            </div>

                            <!-- Manufacturer -->
                            <div class="form-group">
                                <label for="manufacturer">Manufacturer</label>
                                <input type="text" class="form-control" id="manufacturer" name="manufacturer" value="<?= htmlspecialchars($aircraft['manufacturer'] ?? ''); ?>" required>
                            </div>

                            <!-- Condition -->
                            <div class="form-group">
                                <label for="condition">Condition</label>
                                <input type="text" class="form-control" id="condition" name="condition" value="<?= htmlspecialchars($aircraft['condition'] ?? ''); ?>" required>
                            </div>

                            <!-- Year -->
                            <div class="form-group">
                                <label for="year">Year</label>
                                <input type="number" class="form-control" id="year" name="year" value="<?= htmlspecialchars($aircraft['year'] ?? ''); ?>" required>
                            </div>

                            <!-- Total Time Hours -->
                            <div class="form-group">
                                <label for="total_time_hours">Total Time Hours</label>
                                <input type="number" class="form-control" id="total_time_hours" name="total_time_hours" value="<?= htmlspecialchars($aircraft['total_time_hours'] ?? ''); ?>" required>
                            </div>

                            <!-- Engine SMH -->
                            <div class="form-group">
                                <label for="engine_smh">Engine SMH</label>
                                <input type="number" class="form-control" id="engine_smh" name="engine_smh" value="<?= htmlspecialchars($aircraft['engine_smh'] ?? ''); ?>" required>
                            </div>

                            <!-- Price -->
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($aircraft['price'] ?? ''); ?>" required>
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($aircraft['description'] ?? ''); ?></textarea>
                            </div>

                            <!-- Features -->
                            <div class="form-group">
                                <label for="features">Features</label>
                                <textarea class="form-control" id="features" name="features" rows="4"><?= htmlspecialchars($aircraft['features'] ?? ''); ?></textarea>
                            </div>

                            <!-- Warranty -->
                            <div class="form-group">
                                <label for="warranty">Warranty</label>
                                <input type="text" class="form-control" id="warranty" name="warranty" value="<?= htmlspecialchars($aircraft['warranty'] ?? ''); ?>">
                            </div>

                            <!-- Images -->
                            <div class="form-group">
                                <label for="images">Aircraft Images</label>
                                <input type="file" class="form-control" id="images" name="images[]" multiple>
                                <small>Current Images:</small>
                                <?php if (!empty($images)): ?>
                                    <div class="mt-2">
                                        <?php foreach ($images as $image): ?>
                                            <img src="<?= htmlspecialchars($image); ?>" class="img-thumbnail" width="100" alt="Aircraft Image">
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p>No images uploaded.</p>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Aircraft</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>