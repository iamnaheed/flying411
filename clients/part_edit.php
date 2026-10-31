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
$part_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch part details
$query = $pdo->prepare("SELECT * FROM parts WHERE part_id = ? AND vendor_id = ?");
$query->execute([$part_id, $vendor_id]);
$part = $query->fetch(PDO::FETCH_ASSOC);

if (!$part) {
    echo "<script>alert('Part not found or you do not have permission to edit it.'); window.location.href = 'part_list.php';</script>";
    exit();
}

// Fetch part images
$image_query = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND product_type = 'part'");
$image_query->execute([$part_id]);
$images = $image_query->fetchAll(PDO::FETCH_COLUMN, 0);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $part_number = $_POST['part_number'];
    $type = $_POST['type'];
    $condition = $_POST['condition'];
    $region = $_POST['region'];
    $price = $_POST['price'];
    $tagged_with_easa_form_1 = $_POST['tagged_with_easa_form_1'];
    $extra_details = $_POST['extra_details'];
    $warranty = $_POST['warranty'];
   

    // Update part details
    $update_query = $pdo->prepare("UPDATE parts SET 
            part_number = ?, type = ?, `condition` = ?, region = ?, price = ?, 
            tagged_with_easa_form_1 = ?, extra_details = ?, warranty = ?
        WHERE part_id = ? AND vendor_id = ?");
    $update_query->execute([
        $part_number, $type, $condition, $region, $price,
        $tagged_with_easa_form_1, $extra_details, $warranty, 
        $part_id, $vendor_id
    ]);

    // Handle image uploads
    if (!empty($_FILES['images']['name'][0])) {
        $delete_images_query = $pdo->prepare("DELETE FROM product_images WHERE product_id = ? AND product_type = 'part'");
        $delete_images_query->execute([$part_id]);

        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $image_name = basename($_FILES['images']['name'][$key]);
            $image_path = "uploads/" . uniqid() . "_" . $image_name;
            if (move_uploaded_file($tmp_name, $image_path)) {
                $insert_image_query = $pdo->prepare("INSERT INTO product_images (product_id, product_type, image_url) VALUES (?, 'part', ?)");
                $insert_image_query->execute([$part_id, $image_path]);
            }
        }
    }

    echo "<script>alert('Part updated successfully!'); window.location.href = 'manage_products.php';</script>";
    exit();
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Edit Part</h3>
                <h6 class="font-weight-normal mb-0">Update your part details here.</h6>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Part Information</h4>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="part_id" value="<?= htmlspecialchars($part['part_id']); ?>">
                            
                            <div class="form-group">
                                <label for="part_number">Part Number</label>
                                <input type="text" class="form-control" id="part_number" name="part_number" value="<?= htmlspecialchars($part['part_number']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="type">Type</label>
                                <input type="text" class="form-control" id="type" name="type" value="<?= htmlspecialchars($part['type']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="condition">Condition</label>
                                <input type="text" class="form-control" id="condition" name="condition" value="<?= htmlspecialchars($part['condition']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="region">Region</label>
                                <input type="text" class="form-control" id="region" name="region" value="<?= htmlspecialchars($part['region']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="text" class="form-control" id="price" name="price" value="<?= htmlspecialchars($part['price']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="tagged_with_easa_form_1">Tagged with EASA Form 1</label>
                                <select class="form-control" id="tagged_with_easa_form_1" name="tagged_with_easa_form_1">
                                    <option value="Yes" <?= $part['tagged_with_easa_form_1'] == 'Yes' ? 'selected' : ''; ?>>Yes</option>
                                    <option value="No" <?= $part['tagged_with_easa_form_1'] == 'No' ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="extra_details">Extra Details</label>
                                <textarea class="form-control" id="extra_details" name="extra_details" required><?= htmlspecialchars($part['extra_details']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="warranty">Warranty</label>
                                <input type="text" class="form-control" id="warranty" name="warranty" value="<?= htmlspecialchars($part['warranty']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="images">Part Images</label>
                                <input type="file" class="form-control" id="images" name="images[]" multiple>
                                <small>Current Images:</small>
                                <?php foreach ($images as $image): ?>
                                    <img src="<?= htmlspecialchars($image); ?>" class="img-thumbnail" width="100" alt="Part Image">
                                <?php endforeach; ?>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Part</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
