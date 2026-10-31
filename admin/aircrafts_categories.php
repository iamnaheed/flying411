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
$query = "SELECT * FROM aircraft_models WHERE 1";
if (!empty($search)) {
    $query .= " AND (
        aircraft_type LIKE :search 
        OR manufacturer LIKE :search 
        OR model LIKE :search 
        OR type_designator LIKE :search
        OR model_types LIKE :search
        OR category LIKE :search
    )";
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
$aircrafts = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Handle Delete Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = (int) $_POST['delete_id']; // Ensure ID is integer
    $stmt = $pdo->prepare("DELETE FROM aircraft_models WHERE model_id = ?");
    $stmt->execute([$delete_id]);

    $_SESSION['success_message'] = "Aircraft category deleted successfully!";
    header("Location: aircrafts_categories.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_category'])) {
    $aircraft_type = $_POST['aircraft_type'];
    $manufacturer = $_POST['manufacturer'];
    $model = $_POST['model'];
    $grouped_model_display_names = $_POST['group_model'] ?? null;
    $type_designator = $_POST['type_designator'] ?? null;
    $model_types = $_POST['model_types'] ?? null;
    $category = $_POST['category'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO aircraft_models (aircraft_type, manufacturer, model, type_designator, model_types, category,grouped_model_display_names) VALUES (?, ?, ?, ?, ?, ?,?)");
    $stmt->execute([$aircraft_type, $manufacturer, $model, $type_designator, $model_types, $category,$grouped_model_display_names]);

    $_SESSION['success_message'] = "Aircraft category added successfully!";
    header("Location: aircrafts_categories.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $aircraft_type = $_POST['edit_aircraft_type'];
    $manufacturer = $_POST['edit_manufacturer'];
    $model = $_POST['edit_model'];
    $grouped_model_display_names = $_POST['edit_grouped_model_display_names'];
    $type_designator = $_POST['edit_type_designator'];
    $model_types = $_POST['edit_model_types'];
    $category = $_POST['edit_category'];

    $stmt = $pdo->prepare("UPDATE aircraft_models SET aircraft_type=?, manufacturer=?, model=?, grouped_model_display_names=?, type_designator=?, model_types=?, category=? WHERE model_id=?");
    $stmt->execute([$aircraft_type, $manufacturer, $model, $grouped_model_display_names, $type_designator, $model_types, $category, $id]);

    $_SESSION['success_message'] = "Aircraft updated successfully!";
    header("Location: aircrafts_categories.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["upload_csv"])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $csvFile = fopen($_FILES['csv_file']['tmp_name'], 'r');
        fgetcsv($csvFile); // Skip header

        while (($row = fgetcsv($csvFile)) !== false) {
            // Adjust indexes if you know column order exactly
            $manufacturer = $row[0] ?? null;
            $model = $row[1] ?? null;
            $grouped_model_display_name= $row[2] ?? null;
            $type_designator = $row[3] ?? null;
            $model_types = $row[4] ?? null;
            $category = $row[5] ?? null;

            $stmt = $pdo->prepare("INSERT INTO aircraft_models ( manufacturer, model, type_designator, model_types, aircraft_type ,	grouped_model_display_names	
) VALUES (?, ?, ?,?, ?, ?)");
            $stmt->execute([$manufacturer, $model, $type_designator, $model_types, $category,$grouped_model_display_name]);
        }

        fclose($csvFile);
        $_SESSION['success_message'] = "CSV uploaded successfully!";
        header("Location: aircrafts_categories.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to upload CSV.";
    }
}


include "header.php";
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Manage Aircraft Categories</h3>
      </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success"><?= $_SESSION['success_message']; ?></div>
      <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
      <div class="alert alert-danger"><?= $_SESSION['error_message']; ?></div>
      <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="row">
      <!-- Left Side: Add Category Form -->
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title">Add New Category</h4>
            <form action="" method="POST">
              <div class="form-group">
                <label>Aircraft Type</label>
                <select name="aircraft_type" class="form-control" required>
                  <option value="single_engine">Single Engine</option>
                  <option value="multi_engine">Multi Engine</option>
                  <option value="turbo_prop">Turbo Prop</option>
                  <option value="jet">Jet</option>
                </select>
              </div>

              <div class="form-group">
                <label>Manufacturer</label>
                <input type="text" name="manufacturer" class="form-control" required>
              </div>

              <div class="form-group">
                <label>Model</label>
                <input type="text" name="model" class="form-control" required>
              </div>
               <div class="form-group">
                <label>Grouped Model Display Names</label>
                <input type="text" name="group_model" class="form-control">
              </div>
              <div class="form-group">
  <label>Type Designator</label>
  <input type="text" name="type_designator" class="form-control">
</div>

<div class="form-group">
  <label>Model Types</label>
  <input type="text" name="model_types" class="form-control">
</div>

<div class="form-group">
  <label>Category</label>
  <input type="text" name="category" class="form-control">
</div>


              <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
            </form>

            <hr>

            <h4 class="card-title">Upload CSV</h4>
            <form action="" method="POST" enctype="multipart/form-data">
              <div class="form-group">
                <input type="file" name="csv_file" class="form-control" required>
              </div>
              <button type="submit" name="upload_csv" class="btn btn-secondary">Upload CSV</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Right Side: Categories List -->
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title">Aircraft Categories</h4>

            <!-- Search Form -->
            <form method="GET" action="">
              <div class="form-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Type, Manufacturer, Model" value="<?= htmlspecialchars($search); ?>">
              </div>
              <button type="submit" class="btn btn-info">Search</button>
              <a href="aircrafts_categories.php" class="btn btn-secondary">Reset</a>
            </form>

            <div class="table-responsive mt-3">
              <table class="table">
               <thead>
              <tr>
                <th>Aircraft Type</th>
                <th>Manufacturer</th>
                <th>Model</th>
                <th>Grouped Model Display Names</th>
                <th>Type Designator</th>
                <th>Model Types</th>
                <th>Category</th>
                <th>Action</th>
              </tr>
            </thead>


                <tbody>
                  <?php foreach ($aircrafts as $aircraft): ?>
                    <tr>
                          <td><?= !empty($aircraft['aircraft_type']) ? htmlspecialchars($aircraft['aircraft_type']) : 'N/A'; ?></td>
                            <td><?= !empty($aircraft['manufacturer']) ? htmlspecialchars($aircraft['manufacturer']) : 'N/A'; ?></td>
                            <td><?= !empty($aircraft['model']) ? htmlspecialchars($aircraft['model']) : 'N/A'; ?></td>
                            <td><?= !empty($aircraft['grouped_model_display_names']) ? htmlspecialchars($aircraft['grouped_model_display_names']) : 'N/A'; ?></td>
                            <td><?= !empty($aircraft['type_designator']) ? htmlspecialchars($aircraft['type_designator']) : 'N/A'; ?></td>
                            <td><?= !empty($aircraft['model_types']) ? htmlspecialchars($aircraft['model_types']) : 'N/A'; ?></td>
                            <td><?= !empty($aircraft['category']) ? htmlspecialchars($aircraft['category']) : 'N/A'; ?></td>

                          <td>
                              
                            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this category?');">
                              <input type="hidden" name="delete_id" value="<?= $aircraft['model_id']; ?>">
                              <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                              
                            </form>
                            <button type="button" class="btn btn-primary btn-sm" onclick='openEditModal(<?= json_encode($aircraft) ?>)'>Edit</button>
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

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Aircraft</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_id" id="edit_id">
          <div class="mb-3">
            <label>Aircraft Type</label>
            <input type="text" class="form-control" name="edit_aircraft_type" id="edit_aircraft_type">
          </div>
          <div class="mb-3">
            <label>Manufacturer</label>
            <input type="text" class="form-control" name="edit_manufacturer" id="edit_manufacturer">
          </div>
          <div class="mb-3">
            <label>Model</label>
            <input type="text" class="form-control" name="edit_model" id="edit_model">
          </div>
          <div class="mb-3">
            <label>Grouped Model Display Names</label>
            <input type="text" class="form-control" name="edit_grouped_model_display_names" id="edit_grouped_model_display_names">
          </div>
          <div class="mb-3">
            <label>Type Designator</label>
            <input type="text" class="form-control" name="edit_type_designator" id="edit_type_designator">
          </div>
          <div class="mb-3">
            <label>Model Types</label>
            <input type="text" class="form-control" name="edit_model_types" id="edit_model_types">
          </div>
          <div class="mb-3">
            <label>Category</label>
            <input type="text" class="form-control" name="edit_category" id="edit_category">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include "footer.php"; ?>
<script>
function openEditModal(aircraft) {
  document.getElementById('edit_id').value = aircraft.model_id;
  document.getElementById('edit_aircraft_type').value = aircraft.aircraft_type || '';
  document.getElementById('edit_manufacturer').value = aircraft.manufacturer || '';
  document.getElementById('edit_model').value = aircraft.model || '';
  document.getElementById('edit_grouped_model_display_names').value = aircraft.grouped_model_display_names || '';
  document.getElementById('edit_type_designator').value = aircraft.type_designator || '';
  document.getElementById('edit_model_types').value = aircraft.model_types || '';
  document.getElementById('edit_category').value = aircraft.category || '';

  const editModal = new bootstrap.Modal(document.getElementById('editModal'));
  editModal.show();
}
</script>

