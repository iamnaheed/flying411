<?php   
session_start();
include "config.php";

// Get the search term and category filter from the GET request (if available)
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Build the SQL query with filters if necessary
$sql = "SELECT * FROM product_seo WHERE 1";

if ($search_term) {
    // Search by product name (based on category)
    $sql .= " AND (meta_title LIKE :search OR meta_keywords LIKE :search OR meta_description LIKE :search)";
}

if ($category_filter) {
    // Filter by category
    $sql .= " AND category = :category";
}

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
if ($search_term) {
    $stmt->bindValue(':search', '%' . $search_term . '%');
}
if ($category_filter) {
    $stmt->bindValue(':category', $category_filter);
}
$stmt->execute();
$seo_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

// Handle Delete Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_seo'])) {
    $seo_id = (int)$_POST['seo_id'];
    $deleteStmt = $pdo->prepare("DELETE FROM product_seo WHERE seo_id = ?");
    $deleteStmt->execute([$seo_id]);

    $_SESSION['success_message'] = "SEO data deleted successfully!";
    header("Location: product_success_seo.php");
    exit();
}

// Handle Edit Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_seo'])) {
    $seo_id = (int)$_POST['seo_id'];
    header("Location: edit-product-seo.php?seo_id=" . $seo_id);
    exit();
}

include "header.php";
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Manage SEO Data</h3>
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
                        <h4 class="card-title">Product SEO List</h4>

                        <!-- Search and Filter Form -->
                        <form method="GET" class="form-inline mb-3">
                            <input type="text" name="search" class="form-control mr-2" placeholder="Search by name" value="<?= htmlspecialchars($search_term); ?>">
                            <select name="category" class="form-control mr-2">
                                <option value="">All Categories</option>
                                <option value="aircraft" <?= $category_filter == 'aircraft' ? 'selected' : ''; ?>>Aircraft</option>
                                <option value="engine" <?= $category_filter == 'engine' ? 'selected' : ''; ?>>Engine</option>
                                <option value="parts" <?= $category_filter == 'parts' ? 'selected' : ''; ?>>Parts</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SEO ID</th>
                                        <th>Category</th>
                                        <th>Product Name</th> <!-- Changed from Product ID to Product Name -->
                                        <th>Meta Title</th>
                                        <th>Meta Keywords</th>
                                        <th>Meta Description</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($seo_data) > 0): ?>
                                        <?php foreach ($seo_data as $seo): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($seo['seo_id']); ?></td>
                                                <td><?= htmlspecialchars($seo['category']); ?></td>
                                                <td><?= htmlspecialchars(getProductName($seo['category'], $seo['product_id'], $pdo)); ?></td> <!-- Display Product Name -->
                                                <td><?= htmlspecialchars($seo['meta_title']); ?></td>
                                                <td><?= htmlspecialchars($seo['meta_keywords']); ?></td>
                                                <td><?= htmlspecialchars($seo['meta_description']); ?></td>
                                                <td><?= htmlspecialchars($seo['created_at']); ?></td>
                                                <td>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="seo_id" value="<?= $seo['seo_id']; ?>">
                                                        <button type="submit" name="edit_seo" class="btn btn-sm btn-warning">Edit</button>
                                                    </form>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                                        <input type="hidden" name="seo_id" value="<?= $seo['seo_id']; ?>">
                                                        <button type="submit" name="delete_seo" class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="8" class="text-center">No SEO records found.</td></tr>
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

<?php include "footer.php"; ?>
