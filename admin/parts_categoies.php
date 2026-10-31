<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');
set_time_limit(0);
ob_start();

include "config.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle Search Query
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Pagination Logic
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Base Query
$query = "SELECT * FROM parts_master WHERE 1";
if (!empty($search)) {
    $query .= " AND (
        part_name LIKE :search 
        OR part_number LIKE :search 
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
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Delete Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = (int) $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM parts_master WHERE id = ?");
    $stmt->execute([$delete_id]);

    $_SESSION['success_message'] = "Part deleted successfully!";
    header("Location: parts_categoies.php");
    exit();
}


// Handle Edit Part
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {

    $id = $_POST['edit_id'];

    $data = [
        'part_number'          => $_POST['edit_part_number'],
        'part_name'            => $_POST['edit_part_name'],
        'manufacturer'         => $_POST['edit_manufacturer'],
        'image_src'            => $_POST['edit_image_src'],
        'description'          => $_POST['edit_description'],
        'unit_of_measure'      => $_POST['edit_unit_of_measure'],
        'part_link'            => $_POST['edit_part_link'],
        'eccn'                 => $_POST['edit_eccn'],
        'part_type'            => $_POST['edit_part_type'],
        'weight'               => $_POST['edit_weight'],
        'dimensions'           => $_POST['edit_dimensions'],
        'packaged_weight'      => $_POST['edit_packaged_weight'],
        'packaged_dimensions'  => $_POST['edit_packaged_dimensions'],
        'compatible_aircraft'  => $_POST['edit_compatible_aircraft'],
        'notes'                => $_POST['edit_notes'],
        'status'               => $_POST['edit_status']
    ];

    $sql = "
        UPDATE parts_master SET 
            part_number=?, part_name=?, manufacturer=?,
            image_src=?, description=?, unit_of_measure=?, part_link=?, eccn=?, 
            part_type=?, weight=?, dimensions=?, packaged_weight=?, 
            packaged_dimensions=?, compatible_aircraft=?, notes=?,status=?
        WHERE id=?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $data['part_number'],
        $data['part_name'],
        $data['manufacturer'],
        $data['image_src'],
        $data['description'],
        $data['unit_of_measure'],
        $data['part_link'],
        $data['eccn'],
        $data['part_type'],
        $data['weight'],
        $data['dimensions'],
        $data['packaged_weight'],
        $data['packaged_dimensions'],
        $data['compatible_aircraft'],
        $data['notes'],
        $data['status'],
        $id
    ]);

    $_SESSION['success_message'] = "Part updated successfully!";
    header("Location: parts_categoies.php");
    exit();
}

// ==========================
// CSV UPLOAD
// ==========================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["upload_csv"])) {

    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {

        $csvFile = fopen($_FILES['csv_file']['tmp_name'], 'r');
        $header = fgetcsv($csvFile);

        $expected = [
            "Part No","Part Title","Manufacturer","Image src","Description","Unit of Measure",
            "Part Link","ECCN","Part Type","Weight","Dimensions",
            "Packaged Weight","Packaged Dimensions","Compatible Aircraft",
            "Notes","Status"
        ];

        $headerMap = array_flip($header);

        $batchSize = 500;
        $added = 0;
        $duplicates_db = 0;
        $duplicates_csv = 0;
        $existingCsvParts = [];

        $insertValues = [];
        $placeholders = [];

        $pdo->beginTransaction();

        while (($row = fgetcsv($csvFile)) !== false) {

            $data = [];
            foreach ($expected as $colName) {
                if (isset($headerMap[$colName])) {
                    $value = $row[$headerMap[$colName]];
                    if ($colName === "Status" && empty($value)) {
                        $value = "active";
                    }
                    $data[] = $value;
                } else {
                    $data[] = ($colName === "Status") ? "active" : null;
                }
            }

            // CSV duplicate
            $partKey = $data[0] . '||' . $data[1];
            if (isset($existingCsvParts[$partKey])) {
                $duplicates_csv++;
                continue;
            }
            $existingCsvParts[$partKey] = true;

            // DB duplicate
            $checkStmt = $pdo->prepare(
                "SELECT 1 FROM parts_master WHERE part_number = ? AND part_name = ? LIMIT 1"
            );
            $checkStmt->execute([$data[0], $data[1]]);
            if ($checkStmt->fetch()) {
                $duplicates_db++;
                continue;
            }

            // Batch store
            $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insertValues = array_merge($insertValues, $data);
            $added++;

            // 🔥 Execute chunk
            if ($added % $batchSize === 0) {

                $sql = "
                    INSERT INTO parts_master (
                        part_number, part_name, manufacturer, image_src, description,
                        unit_of_measure, part_link, eccn, part_type,
                        weight, dimensions, packaged_weight,
                        packaged_dimensions, compatible_aircraft, notes, status
                    ) VALUES " . implode(',', $placeholders);

                $stmt = $pdo->prepare($sql);
                $stmt->execute($insertValues);

                // reset
                $placeholders = [];
                $insertValues = [];
            }
        }

        // Remaining records
        if (!empty($placeholders)) {
            $sql = "
                INSERT INTO parts_master (
                    part_number, part_name, manufacturer, image_src, description,
                    unit_of_measure, part_link, eccn, part_type,
                    weight, dimensions, packaged_weight,
                    packaged_dimensions, compatible_aircraft, notes, status
                ) VALUES " . implode(',', $placeholders);

            $stmt = $pdo->prepare($sql);
            $stmt->execute($insertValues);
        }

        $pdo->commit();
        fclose($csvFile);

        $message = "$added records inserted successfully. ";
        if ($duplicates_db)  $message .= "$duplicates_db DB duplicates skipped. ";
        if ($duplicates_csv) $message .= "$duplicates_csv CSV duplicates skipped.";

        echo "<script>alert('".addslashes($message)."'); window.location='parts_categoies.php';</script>";
        exit();

    } else {
        echo "<script>alert('CSV upload failed'); window.location='parts_categoies.php';</script>";
        exit();
    }
}






// ==========================
// MANUAL FORM SUBMISSION
// ==========================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_part'])) {
    $part_number         = $_POST['part_number'] ?? null;
    $part_name           = $_POST['part_name'] ?? null;
    $manufacturer           = $_POST['manufacturer'] ?? null;
    $image_src           = $_POST['image_src'] ?? null;
    $description         = $_POST['description'] ?? null;
    $unit_of_measure     = $_POST['unit_of_measure'] ?? null;
    $part_link           = $_POST['part_link'] ?? null;
    $eccn                = $_POST['eccn'] ?? null;
    $part_type           = $_POST['part_type'] ?? null;
    $weight              = $_POST['weight'] ?? null;
    $dimensions          = $_POST['dimensions'] ?? null;
    $packaged_weight     = $_POST['packaged_weight'] ?? null;
    $packaged_dimensions = $_POST['packaged_dimensions'] ?? null;
    $compatible_aircraft = $_POST['compatible_aircraft'] ?? null;
    $notes               = $_POST['notes'] ?? null;
    $status              = $_POST['status'] ?? 'active';

    // Check duplicate in DB
    $checkStmt = $pdo->prepare("
        SELECT * FROM parts_master 
        WHERE part_number = ? AND part_type = ?
    ");
    $checkStmt->execute([$part_number, $part_type]);
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        echo "<script>alert('❌ Cannot add part. Part Number {$part_number} with Type {$part_type} already exists!'); window.location='parts_categoies.php';</script>";
        exit();
    }

    // Insert new part
    $stmt = $pdo->prepare("
        INSERT INTO parts_master 
        (part_number, part_name,manufacturer, image_src, description, unit_of_measure, part_link, eccn, part_type, weight, dimensions, packaged_weight, packaged_dimensions, compatible_aircraft, notes, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");

    try {
        $stmt->execute([
            $part_number, $part_name,$manufacturer,$image_src, $description, $unit_of_measure, $part_link,
            $eccn, $part_type, $weight, $dimensions, $packaged_weight, $packaged_dimensions,
            $compatible_aircraft, $notes, $status
        ]);

        echo "<script>alert('✅ Part {$part_number} added successfully!'); window.location='parts_categoies.php';</script>";
        exit();

    } catch (PDOException $e) {
        echo "<script>alert('Database error: ".addslashes($e->getMessage())."'); window.location='parts_categoies.php';</script>";
        exit();
    }
}

include "header.php";
?>
<?php
// Generate smart pagination HTML
$pagination_html = '';
if ($total_pages > 1) {
    $pagination_html .= '<nav><ul class="pagination justify-content-center mt-3">';

    // Previous button
    $prev_page = max(1, $page - 1);
    $pagination_html .= '<li class="page-item ' . ($page == 1 ? 'disabled' : '') . '">';
    $pagination_html .= '<a class="page-link" href="?page=' . $prev_page . '&search=' . urlencode($search) . '">Previous</a></li>';

    $range = 2; // Pages around current
    $show_ellipsis = false;

    for ($i = 1; $i <= $total_pages; $i++) {
        // Always show first 2 and last page
        if ($i <= 2 || $i == $total_pages || ($i >= $page - $range && $i <= $page + $range)) {
            $active = ($i == $page) ? 'active' : '';
            $pagination_html .= '<li class="page-item ' . $active . '">';
            $pagination_html .= '<a class="page-link" href="?page=' . $i . '&search=' . urlencode($search) . '">' . $i . '</a></li>';
            $show_ellipsis = true;
        } else {
            if ($show_ellipsis) {
                $pagination_html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
                $show_ellipsis = false;
            }
        }
    }

    // Next button
    $next_page = min($total_pages, $page + 1);
    $pagination_html .= '<li class="page-item ' . ($page == $total_pages ? 'disabled' : '') . '">';
    $pagination_html .= '<a class="page-link" href="?page=' . $next_page . '&search=' . urlencode($search) . '">Next</a></li>';

    $pagination_html .= '</ul></nav>';
}
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Manage Parts Master Catalog</h3>
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
<!-- LEFT SIDE – ADD NEW PART -->
<div class="col-md-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="card-title">Add New Part</h4>
      <form action="" method="POST">

        <div class="form-group"><label>Part Number</label><input type="text" name="part_number" class="form-control" required></div>
        <div class="form-group"><label>Part Name</label><input type="text" name="part_name" class="form-control" required></div>
        <div class="form-group"><label>Manufacturer</label><input type="text" name="manufacturer" class="form-control" required></div>
        <div class="form-group"><label>Image src</label><input type="text" name="image_src" class="form-control"></div>
        <div class="form-group"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
        <div class="form-group"><label>Unit of Measure</label><input type="text" name="unit_of_measure" class="form-control"></div>
        <div class="form-group"><label>Part Link</label><input type="text" name="part_link" class="form-control"></div>
        <div class="form-group"><label>ECCN</label><input type="text" name="eccn" class="form-control"></div>
        <div class="form-group"><label>Part Type</label><input type="text" name="part_type" class="form-control"></div>
        <div class="form-group"><label>Weight</label><input type="text" name="weight" class="form-control"></div>
        <div class="form-group"><label>Dimensions</label><input type="text" name="dimensions" class="form-control"></div>
        <div class="form-group"><label>Packaged Weight</label><input type="text" name="packaged_weight" class="form-control"></div>
        <div class="form-group"><label>Packaged Dimensions</label><input type="text" name="packaged_dimensions" class="form-control"></div>
        <div class="form-group"><label>Compatible Aircraft</label><input type="text" name="compatible_aircraft" class="form-control"></div>
        <div class="form-group"><label>Notes</label><input type="text" name="notes" class="form-control"></div>

        <div class="form-group">
          <label>Status</label>
          <select name="status" class="form-control">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>

        <button type="submit" name="add_part" class="btn btn-primary">Add Part</button>
      </form>

      <hr>
      <h4 class="card-title">Upload CSV</h4>

      <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" name="csv_file" class="form-control mb-2" required>
        <button type="submit" name="upload_csv" class="btn btn-secondary">Upload CSV</button>
        <a href="download_sample_parts.php" class="btn btn-info">Download Sample CSV</a>
      </form>

    </div>
  </div>
</div>

<!-- RIGHT SIDE – PARTS LIST -->
<div class="col-md-8">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between mb-2">
  <h4 class="card-title">Parts List</h4>
  <a href="download_all_parts.php" class="btn btn-success">Download All Parts CSV</a>
</div>


      <!-- Search -->
      <form method="GET" action="">
        <input type="text" name="search" class="form-control mb-2" placeholder="Search Part Number, Name" value="<?= htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-info">Search</button>
        <a href="parts_categoies.php" class="btn btn-secondary">Reset</a>
      </form>

      <div class="table-responsive mt-3">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Part No</th>
              <th>Name</th>
              <th>Manufacturer</th>
              <th>Type</th>
              <th>ECCN</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($parts as $p): ?>
            <tr>
              <td><?= $p['part_number'] ?></td>
              <td><?= $p['part_name'] ?></td>
              <td><?= $p['manufacturer'] ?></td>
              <td><?= $p['part_type'] ?></td>
              <td><?= $p['eccn'] ?></td>
              <td><?= $p['status'] ?></td>
              <td>
                <button class="btn btn-primary btn-sm" onclick='openEditModal(<?= json_encode($p) ?>)'>Edit</button>

                <form method="POST" action="" style="display:inline-block" onsubmit="return confirm('Delete?');">
                  <input type="hidden" name="delete_id" value="<?= $p['id']; ?>">
                  <button class="btn btn-danger btn-sm">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?= $pagination_html ?? '' ?>

    </div>
  </div>
</div>
</div>

</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5>Edit Part</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="edit_id" id="edit_id">

          <div class="row">
            <div class="col-md-6 mb-2"><label>Part Number</label><input class="form-control" name="edit_part_number" id="edit_part_number"></div>
            <div class="col-md-6 mb-2"><label>Part Name</label><input class="form-control" name="edit_part_name" id="edit_part_name"></div>
            <div class="col-md-6 mb-2"><label>Manufacturer</label><input class="form-control" name="edit_manufacturer" id="edit_manufacturer"></div>

            <div class="col-md-6 mb-2"><label>Image Src</label><input class="form-control" name="edit_image_src" id="edit_image_src"></div>
            <div class="col-md-6 mb-2"><label>Unit of Measure</label><input class="form-control" name="edit_unit_of_measure" id="edit_unit_of_measure"></div>

            <div class="col-md-12 mb-2"><label>Description</label><textarea class="form-control" name="edit_description" id="edit_description"></textarea></div>

            <div class="col-md-6 mb-2"><label>Part Link</label><input class="form-control" name="edit_part_link" id="edit_part_link"></div>
            <div class="col-md-6 mb-2"><label>ECCN</label><input class="form-control" name="edit_eccn" id="edit_eccn"></div>

            <div class="col-md-6 mb-2"><label>Part Type</label><input class="form-control" name="edit_part_type" id="edit_part_type"></div>
            <div class="col-md-6 mb-2"><label>Weight</label><input class="form-control" name="edit_weight" id="edit_weight"></div>

            <div class="col-md-6 mb-2"><label>Dimensions</label><input class="form-control" name="edit_dimensions" id="edit_dimensions"></div>
            <div class="col-md-6 mb-2"><label>Packaged Weight</label><input class="form-control" name="edit_packaged_weight" id="edit_packaged_weight"></div>

            <div class="col-md-6 mb-2"><label>Packaged Dimensions</label><input class="form-control" name="edit_packaged_dimensions" id="edit_packaged_dimensions"></div>
            <div class="col-md-6 mb-2"><label>Compatible Aircraft</label><input class="form-control" name="edit_compatible_aircraft" id="edit_compatible_aircraft"></div>

            <div class="col-md-12 mb-2"><label>Notes</label><input class="form-control" name="edit_notes" id="edit_notes"></div>

            <div class="col-md-6 mb-2">
              <label>Status</label>
              <select class="form-control" name="edit_status" id="edit_status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-success">Save Changes</button>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
function openEditModal(p) {
  document.getElementById('edit_id').value = p.id;

  [
    'part_number','part_name', 'manufacturer', 'image_src','description','unit_of_measure',
    'part_link','eccn','part_type','weight','dimensions',
    'packaged_weight','packaged_dimensions','compatible_aircraft','notes','status'
  ].forEach(key => {
    let field = document.getElementById("edit_" + key);
    if (field) field.value = p[key] ?? '';
  });

  new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php include "footer.php"; ?>
