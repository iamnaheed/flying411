<?php
ob_start();
include "header.php";
include "config.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check if engine_id is set
if (!isset($_GET['engine_id'])) {
    header("Location: admin-engines.php");
    exit;
}

$engine_id = $_GET['engine_id'];

// Fetch engine details
$stmt = $pdo->prepare("SELECT * FROM engines WHERE engine_id = ?");
$stmt->execute([$engine_id]);
$engine = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch existing images
$image_stmt = $pdo->prepare("
    SELECT image_id AS image_id, image_url 
    FROM product_images 
    WHERE product_id = ? AND product_type = 'engine' 
    ORDER BY sort_order ASC
");
$image_stmt->execute([$engine_id]);
$images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$engine) {
    echo "<p>Engine not found.</p>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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
        $tags = $_POST['tags'];
        $extra_details = $_POST['extra_details'];
        $warranty = $_POST['warranty'];
        $status = $_POST['status'];

        $stmt = $pdo->prepare("
            UPDATE engines 
            SET model = ?, manufacturer = ?, location = ?, engine_type = ?, power_thrust = ?, year = ?, 
                total_time_hours = ?, hr = ?, cycles = ?, `condition` = ?, price = ?, tags = ?, 
                extra_details = ?, warranty = ?, status = ? 
            WHERE engine_id = ?
        ");
        
        if ($stmt->execute([
            $model, $manufacturer, $location, $engine_type, $power_thrust, $year, 
            $total_time_hours, $hr, $cycles, $condition, $price, $tags, 
            $extra_details, $warranty, $status, $engine_id
        ])) {
            
       // Check if new images are uploaded OR if there are no existing images
// --- Handle image upload logic ---
// --- Handle image upload logic ---
$files = $_FILES['images'] ?? $_FILES['all-images-collector'] ?? null;

if ($files && !empty($files['name'][0])) {
    // Case A: New images uploaded

    // Step 1: Get current max sort_order
    $stmt2 = $pdo->prepare("
        SELECT COALESCE(MAX(sort_order), -1) AS max_order
        FROM product_images
        WHERE product_id = ? AND product_type = 'engine'
    ");
    $stmt2->execute([$engine_id]);
    $row = $stmt2->fetch(PDO::FETCH_ASSOC);
    $maxSort = isset($row['max_order']) ? (int)$row['max_order'] : -1;

    // Step 2: Upload directory
    $uploadDir = "uploads/";
    $targetDir = __DIR__ . "/../vendors/" . $uploadDir;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Step 3: Loop through uploads
    foreach ($files['tmp_name'] as $index => $tmpName) {
        $error = $files['error'][$index];
        $name  = $files['name'][$index];

        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmpName)) {
            continue;
        }

        $safeName   = preg_replace("/[^A-Za-z0-9\-_\.]/", "_", basename($name));
        $uniqueName = uniqid('engine_', true) . "_" . $safeName;
        $targetPath = $targetDir . $uniqueName;
        $relativePath = $uploadDir . $uniqueName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $maxSort++;
            $insert = $pdo->prepare("
                INSERT INTO product_images
                (product_id, product_type, image_url, sort_order, created_at)
                VALUES (?, 'engine', ?, ?, NOW())
            ");
            $insert->execute([$engine_id, $relativePath, $maxSort]);
        }
    }

} else {
    // Case B & C: No new images uploaded
    if (empty($images)) {
        // Case C: No existing images either → insert placeholder
        $placeholder = "uploads/default-engine.jpg"; // make sure this file exists
        $insert = $pdo->prepare("
            INSERT INTO product_images
            (product_id, product_type, image_url, sort_order, created_at)
            VALUES (?, 'engine', ?, 0, NOW())
        ");
        $insert->execute([$engine_id, $placeholder]);
    }
    // Case B: existing images found → do nothing
}



            
            header("Location: engines-listing.php?vendor_id=" . $engine['vendor_id']);
            exit;
        } else {
            $error = "Failed to update engine.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}


 ?>


<head>
    <style>
     .photo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 15px;
}

.photo-item {
  position: relative;
  width: 100%;
}

/* Default small image */
.photo-item img {
  width: 100%;
  height: 100px;
  object-fit: cover;
  border-radius: 8px;
}

/* Main image spans more grid space */
.main-photo {
  grid-column: span 3;   /* Span 3 columns */
  grid-row: span 2;      /* Span 2 rows */
}

.main-photo img {
  width: 100%;
  height: 100%;
  min-height: 300px;
  object-fit: cover;
  border: 2px solid red;
  border-radius: 8px;
}

.main-label {
  position: absolute;
  bottom: 10px;
  left: 10px;
  background-color: #ccc;
  color: #333;
  font-size: 14px;
  padding: 3px 10px;
  border-radius: 12px;
}
.add-photo {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  border: 2px dashed #ccc;
  border-radius: 8px;
  height: 100px;
  cursor: pointer;
  text-align: center;
  color: #888;
}

.add-photo:hover {
  background: #eee;
}

    </style>
</head>
<div class="main-panel">
    <div class="content-wrapper">
        <h3 class="text-center">Edit Engine</h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="form-container" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <label>Model</label>
                    <input type="text" class="form-control" name="model" value="<?= htmlspecialchars($engine['model']); ?>" required>

                    <label>Manufacturer</label>
                    <input type="text" class="form-control" name="manufacturer" value="<?= htmlspecialchars($engine['manufacturer']); ?>">

                    <label>Location</label>
                    <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($engine['location']); ?>">

                    <label>Engine Type</label>
                    <input type="text" class="form-control" name="engine_type" value="<?= htmlspecialchars($engine['engine_type']); ?>">

                    <label>Power/Thrust</label>
                    <input type="text" class="form-control" name="power_thrust" value="<?= htmlspecialchars($engine['power_thrust']); ?>">

                    <label>Year</label>
                    <input type="number" class="form-control" name="year" value="<?= htmlspecialchars($engine['year']); ?>">
                </div>

                <div class="col-md-6">
                    <label>Total Time (Hours)</label>
                    <input type="text" class="form-control" name="total_time_hours" value="<?= htmlspecialchars($engine['total_time_hours']); ?>">

                    <label>HR</label>
                    <input type="text" class="form-control" name="hr" value="<?= htmlspecialchars($engine['hr']); ?>">

                    <label>Cycles</label>
                    <input type="number" class="form-control" name="cycles" value="<?= htmlspecialchars($engine['cycles']); ?>">

                    <label>Condition</label>
                    <input type="text" class="form-control" name="condition" value="<?= htmlspecialchars($engine['condition']); ?>">

                    <label>Price ($)</label>
                    <input type="text" class="form-control" name="price" value="<?= htmlspecialchars($engine['price']); ?>">

                    <label>Tags</label>
                    <input type="text" class="form-control" name="tags" value="<?= htmlspecialchars($engine['tags']); ?>">
                </div>
            </div>

            <label>Extra Details</label>
            <textarea class="form-control" name="extra_details"><?= htmlspecialchars($engine['extra_details']); ?></textarea>

            <label>Warranty</label>
            <textarea class="form-control" name="warranty"><?= htmlspecialchars($engine['warranty']); ?></textarea>

            <label>Status</label>
            <select class="form-control" name="status">
                <option value="pending" <?= $engine['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?= $engine['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                <option value="rejected" <?= $engine['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>

           
               <div class="form-group">
    <label for="images">Engine Images</label>
    <input type="file" id="images" class="form-control" multiple>
    <input type="file" id="add-engine-photo" style="display:none;" multiple>

    <div id="sortable-engine-images" class="photo-grid mt-4">
        <?php if (!empty($images)): ?>
            <?php foreach ($images as $index => $img): ?>
                <div class="photo-item<?= $index === 0 ? ' main-photo' : '' ?>" data-id="<?= $img['image_id']; ?>">
                    <img src="../vendors/<?= htmlspecialchars($img['image_url']); ?>" alt="Engine Image" style="max-width: 100%; height: auto;">
                    <?php if ($index === 0): ?>
                        <div class="main-label">Main</div>
                        <div class="photo-warning"></div>
                    <?php endif; ?>
                    <a href="delete_image.php?id=<?= $img['image_id']; ?>&engine_id=<?= $engine_id ?>" class="btn btn-sm btn-danger mt-1">Delete</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Always show Add Photo box -->
        <div class="photo-item add-photo">
            <label for="add-engine-photo">
                <span>&#128247;</span><br>Add
            </label>
            <input type="file" id="all-images-collector" name="images[]" style="display:none;" multiple>
        </div>
    </div>

    <button type="button" class="btn btn-success mt-3" onclick="saveEngineImageOrder()">Save Image Order</button>
</div>

 <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Update Engine</button>
                <a href="engines-listing.php?vendor_id=<?= htmlspecialchars($engine['vendor_id']); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
 document.addEventListener('DOMContentLoaded', function () {
  const grid = document.getElementById('sortable-engine-images');
  const mainInput = document.getElementById('images');
  const addInput  = document.getElementById('add-engine-photo');
  const collector = document.getElementById('all-images-collector');

  let allFiles = []; // Master array

  function refreshCollector() {
    const dt = new DataTransfer();
    allFiles.forEach(f => dt.items.add(f));
    collector.files = dt.files;
  }

  function fileAlreadyAdded(file) {
    // compare name + size + lastModified (fallback to name+size)
    return allFiles.some(f =>
      f.name === file.name &&
      f.size === file.size &&
      ( ('lastModified' in f && 'lastModified' in file) ? f.lastModified === file.lastModified : true )
    );
  }

  function handleFiles(files) {
    Array.from(files).forEach((file) => {
      if (!file.type.startsWith('image/')) return;

      if (fileAlreadyAdded(file)) {
        // skip duplicates
        return;
      }

      allFiles.push(file);

      const reader = new FileReader();
      reader.onload = function (e) {
        const item = document.createElement('div');
        item.className = 'photo-item';
        // mark new files with prefix so server-side sorting (if used) can distinguish
        item.dataset.id = `new-${Date.now()}-${Math.random().toString(36).substr(2,6)}`;
        item.innerHTML = `
          <img src="${e.target.result}">
          <span class="delete-btn" style="cursor:pointer;">&times;</span>
        `;

        // delete button for new file
        item.querySelector('.delete-btn').addEventListener('click', () => {
          // remove the file object from allFiles
          allFiles = allFiles.filter(f => !(f.name === file.name && f.size === file.size && f.lastModified === file.lastModified));
          item.remove();
          refreshCollector();
          updateEngineMainLabel();
        });

        // insert before "add" box if it exists
        const addBtn = grid ? grid.querySelector('.add-photo') : null;
        if (addBtn) grid.insertBefore(item, addBtn);
        else if (grid) grid.appendChild(item);

        updateEngineMainLabel();
      };
      reader.readAsDataURL(file);
    });

    // update hidden collector
    refreshCollector();

    // clear visible inputs so selecting same file again triggers change event if needed
    try { mainInput.value = ''; } catch(e) {}
    try { addInput.value = ''; } catch(e) {}
  }

  // wire up both visible inputs
  [mainInput, addInput].forEach(input => {
    if (!input) return;
    input.addEventListener('change', e => {
      if (e.target.files && e.target.files.length) handleFiles(e.target.files);
    });
  });

  // allow clicking the add-photo label to open addInput (label[for="add-engine-photo"] already exists)
  // Sortable
  if (grid) {
    new Sortable(grid, {
      animation: 150,
      filter: '.add-photo',
      draggable: '.photo-item:not(.add-photo)',
      onEnd: updateEngineMainLabel
    });
  }

  function updateEngineMainLabel() {
    const items = document.querySelectorAll('#sortable-engine-images .photo-item:not(.add-photo)');
    items.forEach((item, i) => {
      item.querySelectorAll('.main-label').forEach(el => el.remove());
      item.classList.remove('main-photo');
      if (i === 0) {
        item.classList.add('main-photo');
        const label = document.createElement('div');
        label.className = 'main-label';
        label.textContent = 'Main';
        item.appendChild(label);
      }
    });

    // When user reorders previews, we should reorder allFiles to match the preview order
    // Rebuild allFiles so upload order matches preview order (only affects new files)
    const newOrderFiles = [];
    const newItems = document.querySelectorAll('#sortable-engine-images .photo-item:not(.add-photo)');
    newItems.forEach(it => {
      const id = it.dataset.id || '';
      if (id && id.startsWith('new-')) {
        // find matching File in allFiles by unique key (name+size+lastModified)
        const match = allFiles.find(f => {
          // compare by name+size+lastModified
          return f.name + '|' + f.size + '|' + f.lastModified === (it._fileKey || (f.name + '|' + f.size + '|' + f.lastModified));
        });
        if (match) newOrderFiles.push(match);
      }
    });
    // append any remaining files that are not in the preview order (safe fallback)
    allFiles.forEach(f => {
      if (!newOrderFiles.includes(f)) newOrderFiles.push(f);
    });
    allFiles = newOrderFiles;
    refreshCollector();
  }

});
function getEngineSortedIds() { return [...document.querySelectorAll('#sortable-engine-images .photo-item:not(.add-photo)')] .map(item => item.dataset.id); }
// Save order to server
function saveEngineImageOrder() {
    const ids = getEngineSortedIds();

    if (ids.length === 0) {
        alert("No engine images to sort.");
        return;
    }

    fetch('save_engine_image_order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({order: ids, type: 'engine'}) // Pass type to differentiate on backend
    })
    .then(response => response.text())
    .then(result => alert(result))
    .catch(error => {
        console.error(error);
        alert('Error saving engine image order.');
    });
}


</script>


<?php include "footer.php"; ?>
