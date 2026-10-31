<?php
session_start();
include "header.php";
include "config.php"; // Database connection

if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

// Set pagination variables
$limit = 30; // Products per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Set filters
$product_type = isset($_GET['product_type']) ? $_GET['product_type'] : '';
$approval_status = isset($_GET['approval_status']) ? $_GET['approval_status'] : '';

// Base query components
$conditions = [];
$params = [];

// Filter product type
if (!empty($product_type) && in_array($product_type, ['aircraft', 'engine', 'part'])) {
    $conditions[] = "product_type = ?";
    $params[] = $product_type;
}

// Filter approval status
if (!empty($approval_status) && in_array($approval_status, ['pending', 'approved', 'rejected'])) {
    $conditions[] = "status = ?";
    $params[] = $approval_status;
}

// Query for fetching products with pagination
$query = $pdo->prepare("
    SELECT * FROM (
        SELECT 'aircraft' AS product_type, aircraft_id AS product_id, model AS product_name, location, price, status ,price_label
        FROM aircrafts WHERE vendor_id = ?
        UNION ALL
        SELECT 'engine' AS product_type, engine_id AS product_id, model AS product_name, location, price, status 
        FROM engines WHERE vendor_id = ?
        UNION ALL
        SELECT 'part' AS product_type, part_id AS product_id, part_number AS product_name, region AS location, price, status 
        FROM parts WHERE vendor_id = ?
    ) AS combined_products
    " . (!empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "") . "
    LIMIT $limit OFFSET $offset
");

$query->execute(array_merge([$vendor_id, $vendor_id, $vendor_id], $params));
$products = $query->fetchAll(PDO::FETCH_ASSOC);

// Get total product count with filters
$countQuery = $pdo->prepare("
    SELECT COUNT(*) FROM (
        SELECT 'aircraft' AS product_type, aircraft_id AS product_id, model AS product_name, location, price, status ,price_label
        FROM aircrafts WHERE vendor_id = ?
        UNION ALL
        SELECT 'engine' AS product_type, engine_id AS product_id, model AS product_name, location, price, status 
        FROM engines WHERE vendor_id = ?
        UNION ALL
        SELECT 'part' AS product_type, part_id AS product_id, part_number AS product_name, region AS location, price, status 
        FROM parts WHERE vendor_id = ?
    ) AS total_products
    " . (!empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "")
);

$countQuery->execute(array_merge([$vendor_id, $vendor_id, $vendor_id], $params));
$total_products = $countQuery->fetchColumn();
$total_pages = ceil($total_products / $limit);
?>


<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Manage Products</h3>
        <h6 class="font-weight-normal mb-0">View and manage all your listed products.</h6>
      </div>
    </div>

    <!-- Filter Section -->
    <form method="GET">
      <div class="row">
        <div class="col-md-4">
          <label>Product Type</label>
          <select name="product_type" class="form-control">
            <option value="">All</option>
            <option value="aircraft" <?= $product_type == 'aircraft' ? 'selected' : ''; ?>>Aircraft</option>
            <option value="engine" <?= $product_type == 'engine' ? 'selected' : ''; ?>>Engine</option>
            <option value="part" <?= $product_type == 'part' ? 'selected' : ''; ?>>Part</option>
          </select>
        </div>
        <div class="col-md-4">
          <label>Approval Status</label>
          <select name="approval_status" class="form-control">
            <option value="">All</option>
            <option value="pending" <?= $approval_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="approved" <?= $approval_status == 'approved' ? 'selected' : ''; ?>>Approved</option>
            <option value="rejected" <?= $approval_status == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
          </select>
        </div>
        <div class="col-md-4 mt-4">
          <button type="submit" class="btn btn-primary">Apply Filters</button>
          <a href="manage_products.php" class="btn btn-secondary">Reset</a>
        </div>
      </div>
    </form>

    <div class="row mt-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Product Listings</h4>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead class="thead-dark">
                  <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Price ($)</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                      <tr>
                        <td><?= ucfirst(htmlspecialchars($product['product_type'])); ?></td>
                        <td><?= htmlspecialchars($product['product_name']); ?></td>
                        <td><?= htmlspecialchars($product['location']); ?></td>
                       <td>
    <?php
    if (isset($product['price_label'])) {
        switch ($product['price_label']) {
            case 'call':
                echo "Call for Price";
                break;
            case 'obo':
                echo "$" . number_format($product['price'], 2) . " (OBO)";
                break;
            case 'starting_bid':
                echo "Starting Bid: $" . number_format($product['price'], 2);
                break;
            default:
                echo "$" . number_format($product['price'], 2);
        }
    } else {
        echo "$" . number_format($product['price'], 2);
    }
    ?>
</td>

                        <td>
                          <?php 
                            $status = strtolower($product['status']);
                            $status_class = $status === 'approved' ? 'text-success' : ($status === 'rejected' ? 'text-danger' : 'text-warning');
                          ?>
                          <span class="<?= $status_class; ?>"><?= ucfirst($status); ?></span>
                        </td>
                        <td>
                          <?php
                            $edit_page = "#";
                            switch ($product['product_type']) {
                                case 'aircraft': $edit_page = "aircraft_edit.php"; break;
                                case 'engine': $edit_page = "engine_edit.php"; break;
                                case 'part': $edit_page = "part_edit.php"; break;
                                default: $edit_page = "edit_product.php";
                            }
                        ?>
                        <a href="<?= $edit_page; ?>?id=<?= $product['product_id']; ?>" class="btn btn-sm btn-warning">Edit</a>

                          <a href="delete_product.php?type=<?= $product['product_type']; ?>&id=<?= $product['product_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" class="text-center text-muted">No products found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
          <!-- Pagination -->
<nav>
  <ul class="pagination justify-content-center mt-3">
    <?php if ($page > 1): ?>
      <li class="page-item"><a class="page-link" href="?page=<?= $page - 1; ?>&product_type=<?= $product_type; ?>&approval_status=<?= $approval_status; ?>">Previous</a></li>
    <?php endif; ?>

    <?php 
      $displayedPages = [];
      for ($i = 1; $i <= $total_pages; $i++) {
        // Show first two, last two, and two pages before/after current page
        if ($i <= 2 || $i >= $total_pages - 1 || ($i >= $page - 2 && $i <= $page + 2)) {
          if (!empty($displayedPages) && $i - end($displayedPages) > 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
          }
          echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">
                  <a class="page-link" href="?page=' . $i . '&product_type=' . $product_type . '&approval_status=' . $approval_status . '">' . $i . '</a>
                </li>';
          $displayedPages[] = $i;
        }
      }
    ?>

    <?php if ($page < $total_pages): ?>
      <li class="page-item"><a class="page-link" href="?page=<?= $page + 1; ?>&product_type=<?= $product_type; ?>&approval_status=<?= $approval_status; ?>">Next</a></li>
    <?php endif; ?>
  </ul>
</nav>


          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
