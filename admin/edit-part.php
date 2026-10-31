<?php
ob_start();
include "header.php";
include "config.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check if part_id is set
if (!isset($_GET['part_id'])) {
    header("Location: admin-parts.php");
    exit;
}

$part_id = $_GET['part_id'];

// Fetch part details
$stmt = $pdo->prepare("SELECT * FROM parts WHERE part_id = ?");
$stmt->execute([$part_id]);
$part = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$part) {
    echo "<p>Part not found.</p>";
    exit;
}

// Fetch existing images
$image_stmt = $pdo->prepare("
    SELECT image_id, image_url 
    FROM product_images 
    WHERE product_id = ? AND product_type = 'part' 
    ORDER BY sort_order ASC
");
$image_stmt->execute([$part_id]);
$images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect ALL form fields
    $part_number            = $_POST['part_number'];
    $part_name              = $_POST['part_name'];
    $description            = $_POST['description'];
    $weight                 = $_POST['weight'];
    $dimensions             = $_POST['dimensions'];
    $package_weight         = $_POST['package_weight'];
    $package_dimensions     = $_POST['package_dimensions'];
    $compatible_aircraft    = $_POST['compatible_aircraft'];
    $manufacturer           = $_POST['manufacturer'];
    $owned_type             = $_POST['owned_type'];
    $material               = $_POST['material'];
    $eccn_number            = $_POST['eccn_number'];
    $condition              = $_POST['condition'];
    $region                 = $_POST['region'];
    $price                  = $_POST['price'];
    $tagged_with_easa_form_1= $_POST['tagged_with_easa_form_1'];
    $extra_details          = $_POST['extra_details'];
    $warranty               = $_POST['warranty'];
    $status                 = $_POST['status'];

    // Update with new fields
    $stmt = $pdo->prepare("
        UPDATE parts 
        SET 
            part_number = ?, 
            part_name = ?, 
            description = ?, 
            weight = ?, 
            dimensions = ?, 
            package_weight = ?, 
            package_dimensions = ?, 
            compatible_aircraft = ?, 
            manufacturer = ?, 
            owned_type = ?, 
            material = ?, 
            eccn_number = ?, 
            `condition` = ?, 
            region = ?, 
            price = ?, 
            tagged_with_easa_form_1 = ?, 
            extra_details = ?, 
            warranty = ?, 
            status = ?
        WHERE part_id = ?
    ");

    $updated = $stmt->execute([
        $part_number,
        $part_name,
        $description,
        $weight,
        $dimensions,
        $package_weight,
        $package_dimensions,
        $compatible_aircraft,
        $manufacturer,
        $owned_type,
        $material,
        $eccn_number,
        $condition,
        $region,
        $price,
        $tagged_with_easa_form_1,
        $extra_details,
        $warranty,
        $status,
        $part_id
    ]);

    if ($updated) {

        // ------- IMAGE UPLOAD HANDLING ----------
        if (!empty($_FILES['images']['name'][0])) {

            $uploadDir = "uploads/";
            $targetDir = __DIR__ . "/../vendors/" . $uploadDir;

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {

                $error = $_FILES['images']['error'][$key];
                $name  = $_FILES['images']['name'][$key];

                if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmpName)) {
                    continue;
                }

                $safeName = preg_replace("/[^A-Za-z0-9\-_\.]/", "_", basename($name));
                $uniqueName = uniqid('part_', true) . "_" . $safeName;

                $targetPath   = $targetDir . $uniqueName;
                $relativePath = $uploadDir . $uniqueName;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    $stmt = $pdo->prepare("
                        INSERT INTO product_images 
                        (product_id, product_type, image_url, created_at)
                        VALUES (?, 'part', ?, NOW())
                    ");
                    $stmt->execute([$part_id, $relativePath]);
                }
            }
        }

        header("Location: parts-listing.php?vendor_id=" . $part['vendor_id']);
        exit;
    } else {
        $error = "Failed to update part.";
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
        <h3>Edit Part</h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST"   enctype="multipart/form-data">
           <div class="form-group">
                        <label>Part Number</label>
                        <input type="text" class="form-control" name="part_number" value="<?= htmlspecialchars($part['part_number']); ?>" required>
                    </div>

                    <!-- PART NAME -->
                    <div class="form-group">
                        <label>Part Name</label>
                        <input type="text" class="form-control" name="part_name" value="<?= htmlspecialchars($part['part_name']); ?>" required>
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="15"><?= htmlspecialchars($part['description']); ?></textarea>
                    </div>

                    <!-- WEIGHT -->
                    <div class="form-group">
                        <label>Weight</label>
                        <input type="text" class="form-control" name="weight" value="<?= htmlspecialchars($part['weight']); ?>">
                    </div>

                    <!-- DIMENSIONS -->
                    <div class="form-group">
                        <label>Dimensions</label>
                        <input type="text" class="form-control" name="dimensions" value="<?= htmlspecialchars($part['dimensions']); ?>">
                    </div>

                    <!-- PACKAGE WEIGHT -->
                    <div class="form-group">
                        <label>Package Weight</label>
                        <input type="text" class="form-control" name="package_weight" value="<?= htmlspecialchars($part['package_weight']); ?>">
                    </div>

                    <!-- PACKAGE DIMENSIONS -->
                    <div class="form-group">
                        <label>Package Dimensions</label>
                        <input type="text" class="form-control" name="package_dimensions" value="<?= htmlspecialchars($part['package_dimensions']); ?>">
                    </div>

                    <!-- COMPATIBLE AIRCRAFT -->
                    <div class="form-group">
                        <label>Compatible Aircraft</label>
                        <input type="text" class="form-control" name="compatible_aircraft" value="<?= htmlspecialchars($part['compatible_aircraft']); ?>">
                    </div>

                    <!-- NEW FIELDS (MANUFACTURER, OWNED TYPE, MATERIAL, ECCN NUMBER) -->
                    <div class="form-group">
                        <label>Manufacturer</label>
                        <input type="text" class="form-control" name="manufacturer" value="<?= htmlspecialchars($part['manufacturer']); ?>" >
                    </div>

                    <div class="form-group">
                        <label>Owned Type</label>
                        <input type="text" class="form-control" name="owned_type" value="<?= htmlspecialchars($part['owned_type']); ?>" >
                    </div>

                    <div class="form-group">
                        <label>Material</label>
                        <input type="text" class="form-control" name="material" value="<?= htmlspecialchars($part['material']); ?>" >
                    </div>

                    <div class="form-group">
                        <label>ECCN Number</label>
                        <input type="text" class="form-control" name="eccn_number" value="<?= htmlspecialchars($part['eccn_number']); ?>" >
                    </div>

                    <!-- CONDITION -->
                    <div class="form-group">
                        <label>Condition</label>
                        <input type="text" class="form-control" name="condition" value="<?= htmlspecialchars($part['condition']); ?>" >
                    </div>

                    <!-- REGION -->
                    <div class="form-group">
                        <label>Region</label>
                        <input type="text" class="form-control" name="region" value="<?= htmlspecialchars($part['region']); ?>" >
                    </div>

                    <!-- PRICE -->
                    <div class="form-group">
                        <label>Price</label>
                        <input type="text" class="form-control" name="price" value="<?= htmlspecialchars($part['price']); ?>" >
                    </div>

                    <!-- TAG -->
                    <div class="form-group">
                        <label>Tagged with EASA Form 1</label>
                        <select class="form-control" name="tagged_with_easa_form_1">
                            <option value="No Tag" <?= $part['tagged_with_easa_form_1'] == 'No Tag' ? 'selected' : ''; ?>>No Tag</option>
                            <option value="8130" <?= $part['tagged_with_easa_form_1'] == '8130' ? 'selected' : ''; ?>>8130</option>
                            <option value="EASA Form 1" <?= $part['tagged_with_easa_form_1'] == 'EASA Form 1' ? 'selected' : ''; ?>>EASA Form 1</option>
                            <option value="Dual Release 8130/EASA Form 1" <?= $part['tagged_with_easa_form_1'] == 'Dual Release 8130/EASA Form 1' ? 'selected' : ''; ?>>Dual Release 8130/EASA Form 1</option>
                        </select>
                    </div>

                    <!-- EXTRA DETAILS -->
                    <div class="form-group">
                        <label>Extra Details</label>
                        <textarea class="form-control" name="extra_details"><?= htmlspecialchars($part['extra_details']); ?></textarea>
                    </div>

                    <!-- WARRANTY -->
                    <div class="form-group">
                        <label>Warranty</label>
                        <input type="text" class="form-control" name="warranty" value="<?= htmlspecialchars($part['warranty']); ?>">
                    </div>  

            <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status">
                    <option value="pending" <?= $part['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?= $part['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?= $part['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
          <div class="form-group">
    <label for="images">Part Images</label>
    <!-- Hidden main file input -->
    <input type="file" class="form-control" id="images" name="images[]" multiple style="display:none;">
    <br>

    <!-- Always render sortable grid -->
    <div id="sortable-part-images" class="photo-grid mt-4">
        <?php if (!empty($images)): ?>
            <?php foreach ($images as $index => $img): ?>
                <div class="photo-item<?= $index === 0 ? ' main-photo' : '' ?>" data-id="<?= $img['image_id']; ?>">
                    <img src="../vendors/<?= htmlspecialchars($img['image_url']); ?>" alt="Part Image" style="max-width: 100%; height: auto;">
                    <?php if ($index === 0): ?>
                        <div class="main-label">Main</div>
                        <div class="photo-warning">Must be over 500 pixels wide</div>
                    <?php endif; ?>
                    <a href="delete_part_image.php?id=<?= $img['image_id']; ?>&part_id=<?= $part_id ?>" class="btn btn-sm btn-danger mt-1">Delete</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Always show Add Photo box -->
        <div class="photo-item add-photo">
            <label for="add-part-photo">
                <span>&#128247;</span><br>Add
            </label>
            <input type="file" name="images[]" id="add-part-photo" style="display: none;" multiple>
        </div>
    </div>

    <button type="button" class="btn btn-success mt-3" onclick="savePartImageOrder()">Save Image Order</button>
</div>


            <button type="submit" class="btn btn-primary">Update Part</button>
            <a href="parts-listing.php?vendor_id=<?= $part['vendor_id']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {
  const grid = document.getElementById('sortable-part-images');
  const mainInput = document.getElementById('images');
  const addInput = document.getElementById('add-part-photo');

  let allFiles = []; // all chosen files

  function refreshMainInput() {
    const dt = new DataTransfer();
    allFiles.forEach(f => dt.items.add(f));
    mainInput.files = dt.files;
  }

  function handleFiles(files) {
    Array.from(files).forEach((file, index) => {
      if (!file.type.startsWith('image/')) return;

      // ✅ prevent exact duplicates
      const exists = allFiles.some(f => f.name === file.name && f.size === file.size);
      if (exists) return;

      allFiles.push(file);

      const reader = new FileReader();
      reader.onload = function (e) {
        const item = document.createElement('div');
        item.className = 'photo-item';
        item.dataset.id = `new-${Date.now()}-${index}`;
        item.innerHTML = `
          <img src="${e.target.result}" alt="Preview Image">
          <span class="delete-btn">&times;</span>
        `;
        item.querySelector('.delete-btn').onclick = () => {
          allFiles = allFiles.filter(f => f !== file);
          item.remove();
          refreshMainInput();
          updatePartMainLabel();
        };

        const addBtn = grid.querySelector('.add-photo');
        grid.insertBefore(item, addBtn);
        updatePartMainLabel();
      };
      reader.readAsDataURL(file);
    });

    refreshMainInput();
  }

  function onInputChange(e) {
    handleFiles(e.target.files);
    // 🧹 clear the value so same files don't re-trigger
    e.target.value = '';
  }

  mainInput.addEventListener('change', onInputChange);
  addInput.addEventListener('change', onInputChange);

  if (grid) {
    new Sortable(grid, {
      animation: 150,
      filter: '.add-photo',
      draggable: '.photo-item:not(.add-photo)',
      onEnd: updatePartMainLabel
    });
  }

  function updatePartMainLabel() {
    const items = document.querySelectorAll('#sortable-part-images .photo-item:not(.add-photo)');
    items.forEach((item, i) => {
      item.querySelectorAll('.main-label, .photo-warning').forEach(el => el.remove());
      item.classList.remove('main-photo');
      if (i === 0) {
        item.classList.add('main-photo');
        const label = document.createElement('div');
        label.className = 'main-label';
        label.textContent = 'Main';
        item.appendChild(label);

        const warning = document.createElement('div');
        warning.className = 'photo-warning';
        warning.textContent = 'Must be over 500 pixels wide';
        item.appendChild(warning);
      }
    });
  }
});
// Get sorted image IDs
function getPartSortedIds() {
    return [...document.querySelectorAll('#sortable-part-images .photo-item:not(.add-photo)')]
        .map(item => item.dataset.id);
}

// Save order to server
function savePartImageOrder() {
    const ids = getPartSortedIds();

    if (ids.length === 0) {
        alert("No part images to sort.");
        return;
    }

    fetch('save_parts_image_order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ order: ids, type: 'part' })
    })
    .then(response => response.text())
    .then(result => alert(result))
    .catch(error => {
        console.error(error);
        alert('Error saving part image order.');
    });
}


</script>
<?php include "footer.php"; ?>
