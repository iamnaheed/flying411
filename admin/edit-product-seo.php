<?php   
session_start();
include "config.php";

// Get the seo_id from URL and fetch existing SEO data
if (!isset($_GET['seo_id']) || empty($_GET['seo_id'])) {
    header("Location: admin-seo.php"); // Redirect if no seo_id is provided
    exit();
}

$seo_id = (int)$_GET['seo_id'];

// Fetch the existing SEO data for the given seo_id
$stmt = $pdo->prepare("SELECT * FROM product_seo WHERE seo_id = ?");
$stmt->execute([$seo_id]);
$seo_data = $stmt->fetch(PDO::FETCH_ASSOC);

// If no data is found for the given seo_id, redirect to the list page
if (!$seo_data) {
    header("Location: admin-seo.php");
    exit();
}

// Function to get product name based on category and product_id
function getProductName($category, $product_id, $pdo) {
    if ($category == 'aircraft') {
        $stmt = $pdo->prepare("SELECT model FROM aircrafts WHERE aircraft_id = ?");
        $stmt->execute([$product_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['model'] ?? 'Unknown Aircraft';
    } elseif ($category == 'engine') {
        $stmt = $pdo->prepare("SELECT model FROM engines WHERE engine_id = ?");
        $stmt->execute([$product_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['model'] ?? 'Unknown Engine';
    } elseif ($category == 'parts') {
        $stmt = $pdo->prepare("SELECT part_name FROM parts WHERE part_id = ?");
        $stmt->execute([$product_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['part_name'] ?? 'Unknown Part';
    } else {
        return 'Unknown Product';
    }
}

// Handle form submission for updating the SEO data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_seo'])) {
    // Collect the form data
    $category = $_POST['category'];
    $product_id = $_POST['product_id'];
    $meta_title = $_POST['meta_title'];
    $meta_keywords = $_POST['meta_keywords'];
    $meta_description = $_POST['meta_description'];

    // Prepare and execute the update query
    $updateStmt = $pdo->prepare("
        UPDATE product_seo 
        SET category = ?, product_id = ?, meta_title = ?, meta_keywords = ?, meta_description = ? 
        WHERE seo_id = ?
    ");
    $updateStmt->execute([$category, $product_id, $meta_title, $meta_keywords, $meta_description, $seo_id]);

    $_SESSION['success_message'] = "SEO data updated successfully!";
    header("Location: product_success_seo.php");
    exit();
}

include "header.php";
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Edit SEO Data</h3>
            </div>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title">Edit Product SEO</h4>

                        <!-- SEO Edit Form -->
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="aircraft" <?= ($seo_data['category'] == 'aircraft') ? 'selected' : ''; ?>>Aircraft</option>
                                    <option value="engine" <?= ($seo_data['category'] == 'engine') ? 'selected' : ''; ?>>Engine</option>
                                    <option value="parts" <?= ($seo_data['category'] == 'parts') ? 'selected' : ''; ?>>Parts</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="product_id">Product ID</label>
                                <input type="number" class="form-control" id="product_id" name="product_id" value="<?= htmlspecialchars($seo_data['product_id']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="product_name">Product Name</label>
                                <input type="text" class="form-control" id="product_name" value="<?= htmlspecialchars(getProductName($seo_data['category'], $seo_data['product_id'], $pdo)); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="meta_title">Meta Title</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title" value="<?= htmlspecialchars($seo_data['meta_title']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="<?= htmlspecialchars($seo_data['meta_keywords']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="4" required><?= htmlspecialchars($seo_data['meta_description']); ?></textarea>
                            </div>
                            <button type="submit" name="update_seo" class="btn btn-success">Update SEO</button>
                            <a href="admin-seo.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
