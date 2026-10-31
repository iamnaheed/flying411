<?php
ob_start();
include "config.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle Search Query
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Pagination Logic
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure page number is at least 1
$offset = ($page - 1) * $limit;

// Base Query
$query = "SELECT * FROM engines_details WHERE 1";

// Append Search Filter
if (!empty($search)) {
    $query .= " AND (engine_model LIKE :search OR manufacturer LIKE :search OR engine_type LIKE :search)";
}

// Get total number of records
$totalQuery = $pdo->prepare(str_replace("SELECT *", "SELECT COUNT(*)", $query));
if (!empty($search)) {
    $totalQuery->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$totalQuery->execute();
$totalRows = $totalQuery->fetchColumn();
$total_pages = ceil($totalRows / $limit);

// Fetch paginated and filtered data
$query .= " LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);

if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
$stmt->execute();
$engines = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Handle Delete Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = (int) $_POST['delete_id']; // Ensure ID is integer
    $stmt = $pdo->prepare("DELETE FROM aircraft_models WHERE model_id = ?");
    $stmt->execute([$delete_id]);

    $_SESSION['success_message'] = "Aircraft category deleted successfully!";
    header("Location: aircrafts_categories.php");
    exit();
}



include "header.php";

?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Manage Engine Categories</h3>
      </div>
    </div>

    <!-- Success & Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
      <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
      <div class="alert alert-danger"><?= $_SESSION['error_message']; ?></div>
      <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="row">
      <!-- Left Side: Add Engine Category Form -->
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title">Add New Engine</h4>
            <form action="" method="POST">
              <div class="form-group">
                <label>Engine Type</label>
                <select name="engine_type" class="form-control" required>
                  <option value="turbofan">Turbofan</option>
                  <option value="turbojet">Turbojet</option>
                  <option value="turboprop">Turboprop</option>
                  <option value="piston">Piston</option>
                </select>
              </div>

              <div class="form-group">
                <label>Manufacturer</label>
                <input type="text" name="manufacturer" class="form-control" required>
              </div>

              <div class="form-group">
                <label>Model</label>
                <input type="text" name="engine_model" class="form-control" required>
              </div>

              <div class="form-group">
                <label>Power/Thrust</label>
                <input type="text" name="power_thrust" class="form-control" required>
              </div>

              <button type="submit" name="add_category" class="btn btn-primary">Add Engine</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Right Side: Engine List -->
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title">Engine Categories</h4>

            <!-- Search Form -->
            <form method="GET" action="">
              <div class="form-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Model, Manufacturer, Type" value="<?= htmlspecialchars($search); ?>">
              </div>
              <button type="submit" class="btn" style="background-color:#4747A1;color:white;">Search</button>
              <a href="engines_categories.php" class="btn btn-secondary">Reset</a>
            </form>

            <div class="table-responsive mt-3">
              <table class="table">
                <thead>
                  <tr>
                    <th>Engine Type</th>
                    <th>Manufacturer</th>
                    <th>Model</th>
                    <th>Power/Thrust</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($engines as $engine): ?>
                    <tr>
                      <td><?= htmlspecialchars($engine['engine_type']); ?></td>
                      <td><?= htmlspecialchars($engine['manufacturer']); ?></td>
                      <td><?= htmlspecialchars($engine['engine_model']); ?></td>
                      <td><?= htmlspecialchars($engine['power_thrust']); ?></td>
                      <td>
                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this engine?');">
                          <input type="hidden" name="delete_id" value="<?= $engine['id']; ?>">
                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
                <nav>
    <ul class="pagination">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page - 1; ?>">&laquo; Prev</a>
            </li>
        <?php endif; ?>

        <?php
        // Always show first two pages
        for ($i = 1; $i <= 2; $i++) {
            if ($i <= $total_pages) {
                echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">
                        <a class="page-link" href="?page=' . $i . '">' . $i . '</a>
                      </li>';
            }
        }

        // Show "..." if needed
        if ($page > 4) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        // Show 2 pages before and after current page
        for ($i = max(3, $page - 1); $i <= min($total_pages - 2, $page + 1); $i++) {
            echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">
                    <a class="page-link" href="?page=' . $i . '">' . $i . '</a>
                  </li>';
        }

        // Show "..." if needed
        if ($page < $total_pages - 3) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        // Always show last two pages
        for ($i = $total_pages - 1; $i <= $total_pages; $i++) {
            if ($i > 2) {
                echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">
                        <a class="page-link" href="?page=' . $i . '">' . $i . '</a>
                      </li>';
            }
        }

        // Next button
        if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page + 1; ?>">Next &raquo;</a>
            </li>
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

