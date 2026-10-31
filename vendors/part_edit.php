<?php
session_start();
require_once "config.php";
include "header.php";

/* ===============================
   AUTH CHECK
================================ */
if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];
$part_id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* ===============================
   FETCH PART
================================ */
$stmt = $pdo->prepare("
    SELECT * FROM parts 
    WHERE part_id = ? AND vendor_id = ?
");
$stmt->execute([$part_id, $vendor_id]);
$part = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$part) {
    echo "<script>alert('Part not found'); window.location='part_list.php';</script>";
    exit;
}

/* ===============================
   FETCH SELECTED AIRCRAFT (IDs → MODELS)
================================ */
$selectedAircraft = [];

$stmt = $pdo->prepare("
    SELECT a.aircraft_id AS aircraft_id, a.manufacturer, a.model
    FROM part_aircraft pa
    JOIN aircrafts a ON a.aircraft_id = pa.aircraft_id
    WHERE pa.part_id = ?
");

$stmt->execute([$part_id]);
$selectedAircraft = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   FETCH MANUFACTURER
================================ */
$stmt = $pdo->prepare("
    SELECT manufacturer 
    FROM part_manufacturer 
    WHERE part_id = ?
");
$stmt->execute([$part_id]);
$manufacturerRow = $stmt->fetch(PDO::FETCH_ASSOC);
$part['manufacturer'] = $manufacturerRow['manufacturer'] ?? '';

/* ===============================
   FETCH IMAGES
================================ */
$stmt = $pdo->prepare("
    SELECT image_id, image_url 
    FROM product_images 
    WHERE product_id = ? AND product_type = 'part'
    ORDER BY sort_order ASC
");
$stmt->execute([$part_id]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   HANDLE UPDATE
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ---------- UPDATE PART ---------- */
    $stmt = $pdo->prepare("
        UPDATE parts SET
            part_number = ?,
            part_name = ?,
            `condition` = ?,
            weight = ?,
            dimensions = ?,
            package_weight = ?,
            package_dimensions = ?,
            description = ?,
            price = ?,
            price_label = ?,
            currency = ?,
            owned_type = ?,
            material = ?,
            eccn_number = ?,
            tagged_with_easa_form_1 = ?,
            extra_details = ?,
            warranty = ?,
            updated_at = NOW()
        WHERE part_id = ? AND vendor_id = ?
    ");

    $stmt->execute([
        $_POST['part_number'],
        $_POST['part_name'],
        $_POST['condition'],
        $_POST['weight'],
        $_POST['dimensions'],
        $_POST['package_weight'],
        $_POST['package_dimensions'],
        $_POST['description'],
        $_POST['price'],
        $_POST['price_label'],
        $_POST['currency'],
        $_POST['owned_type'],
        $_POST['material'],
        $_POST['eccn_number'],
        $_POST['tagged_with_easa_form_1'],
        $_POST['extra_details'],
        $_POST['warranty'],
        $part_id,
        $vendor_id
    ]);

    /* ---------- UPDATE AIRCRAFT RELATION ---------- */
    $pdo->prepare("DELETE FROM part_aircraft WHERE part_id = ?")
        ->execute([$part_id]);

    if (!empty($_POST['compatible_aircraft'])) {
        $aircraftIds = array_filter(array_map('intval', explode(',', $_POST['compatible_aircraft'])));

        $stmt = $pdo->prepare("
            INSERT INTO part_aircraft (part_id, aircraft_id, created_at)
            VALUES (?, ?, NOW())
        ");

        foreach ($aircraftIds as $aid) {
            $stmt->execute([$part_id, $aid]);
        }
    }

    /* ---------- UPDATE MANUFACTURER ---------- */
    $pdo->prepare("DELETE FROM part_manufacturer WHERE part_id = ?")
        ->execute([$part_id]);

    if (!empty($_POST['manufacturer'])) {
        $stmt = $pdo->prepare("
            INSERT INTO part_manufacturer (part_id, manufacturer, created_at)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$part_id, trim($_POST['manufacturer'])]);
    }


    // ===============================
    // IMAGE UPLOAD HANDLING
    // ===============================
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = "uploads/";
        $targetDir = __DIR__ . "/../vendors/" . $uploadDir;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            $error = $_FILES['images']['error'][$key];
            $name = $_FILES['images']['name'][$key];

            if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmpName)) {
                continue;
            }

            // CLEAN FILENAME
            $safeName = preg_replace("/[^A-Za-z0-9\-_\.]/", "_", basename($name));
            $uniqueName = uniqid('part_', true) . "_" . $safeName;

            // PATHS
            $targetPath = $targetDir . $uniqueName;
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
    echo "<script>alert('Part updated successfully'); window.location='manage_products.php';</script>";
    exit;
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

<!-- ROW 1 -->
<div class="form-row">
  <div class="form-group col-md-4">
    <label>Part Number</label>
    <input type="text" class="form-control" name="part_number"
           value="<?= htmlspecialchars($part['part_number']); ?>">
  </div>

 <div class="form-group col-md-4">
    <label>Part Name</label>
    <input type="text" class="form-control" name="part_name"
           value="<?= htmlspecialchars($part['part_name']); ?>">
  </div>
  
  <div class="form-group col-md-4">
    <label>Condition</label>
    <select class="form-control" name="condition">
      <?php
      $conditions = [
        "Factory New","Overhauled","Serviceable",
        "As Removed","New Surplus","Repaired",
        "Beyond Economic Repair"
      ];
      foreach ($conditions as $c) {
        $sel = ($part['condition'] === $c) ? 'selected' : '';
        echo "<option value='$c' $sel>$c</option>";
      }
      ?>
    </select>
  </div>
</div>

<!-- ROW 2 -->
<div class="form-row">
  <div class="form-group col-md-4">
    <label>Weight</label>
    <input type="text" class="form-control" name="weight"
           value="<?= htmlspecialchars($part['weight']); ?>">
  </div>

  <div class="form-group col-md-4">
    <label>Dimensions</label>
    <input type="text" class="form-control" name="dimensions"
           value="<?= htmlspecialchars($part['dimensions']); ?>">
  </div>

  <div class="form-group col-md-4">
    <label>Package Weight</label>
    <input type="text" class="form-control" name="package_weight"
           value="<?= htmlspecialchars($part['package_weight']); ?>">
  </div>
</div>

<!-- ROW 3 -->
<div class="form-row">
  <div class="form-group col-md-4">
    <label>Package Dimensions</label>
    <input type="text" class="form-control" name="package_dimensions"
           value="<?= htmlspecialchars($part['package_dimensions']); ?>">
  </div>

  <div class="form-group col-md-4">
    <label>Manufacturer</label>
    <input type="text" class="form-control" id="manufacturerField"
           name="manufacturer"
           value="<?= htmlspecialchars($part['manufacturer']); ?>">
    <div id="manufacturerResults" class="list-group mt-1"
         style="display:none;max-height:200px;overflow:auto;"></div>
  </div>

  <div class="form-group col-md-4">
    <label>Material</label>
    <input type="text" class="form-control" name="material"
           value="<?= htmlspecialchars($part['material']); ?>">
  </div>
</div>

<!-- ROW 4 -->
<div class="form-row">
  <div class="form-group col-md-4">
    <label>ECCN Number</label>
    <input type="text" class="form-control" name="eccn_number"
           value="<?= htmlspecialchars($part['eccn_number']); ?>">
  </div>

  <div class="form-group col-md-4">
    <label>Owned Type</label>
    <select class="form-control" name="owned_type">
      <option value="">-- Select --</option>
      <option value="outright" <?= $part['owned_type']=='outright'?'selected':'' ?>>Outright</option>
      <option value="exchange" <?= $part['owned_type']=='exchange'?'selected':'' ?>>Exchange</option>
    </select>
  </div>
<div class="form-group col-md-4">
    <label>Tagged with EASA Form 1</label>
    <select class="form-control" name="tagged_with_easa_form_1">
      <option value="No Tag" <?= $part['tagged_with_easa_form_1']=='No Tag'?'selected':'' ?>>No Tag</option>
      <option value="8130" <?= $part['tagged_with_easa_form_1']=='8130'?'selected':'' ?>>8130</option>
      <option value="EASA Form 1" <?= $part['tagged_with_easa_form_1']=='EASA Form 1'?'selected':'' ?>>EASA Form 1</option>
      <option value="Dual Release (8130/EASA Form 1)"
        <?= str_contains($part['tagged_with_easa_form_1'],'Dual')?'selected':'' ?>>
        Dual Release (8130/EASA Form 1)
      </option>
    </select>
  </div>
  
</div>

<!-- ROW 5 -->
<div class="form-row">
  

  <div class="form-group col-md-4">
    <label>Price Label</label>
    <select class="form-control" name="price_label" id="price_label">
      <option value="">-- Select --</option>
      <option value="call" <?= $part['price_label']=='call'?'selected':'' ?>>Call for Price</option>
      <option value="sp" <?= $part['price_label']=='sp'?'selected':'' ?>>Sale Price</option>
    </select>
  </div>
  <div class="form-group col-md-4">
    <label>Currency</label>
    <select class="form-control" name="currency" id="currency">
      <option value="USD" <?= $part['currency']=='USD'?'selected':'' ?>>USD</option>
      <option value="EUR" <?= $part['currency']=='EUR'?'selected':'' ?>>EUR</option>
      <option value="GBP" <?= $part['currency']=='GBP'?'selected':'' ?>>GBP</option>
    </select>
  </div>
  <div class="form-group col-md-4">
    <label>Price</label>
    <input type="text" class="form-control" name="price" id="price"
           value="<?= htmlspecialchars($part['price']); ?>">
  </div>

  
</div>

<!-- DESCRIPTION -->
<div class="form-group">
  <label>Description</label>
  <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($part['description']); ?></textarea>
</div>

<!-- COMPATIBLE AIRCRAFT -->
<div class="form-group">
  <label>Compatible Aircraft</label>

  <input type="text" id="aircraftSearch" class="form-control"
         placeholder="Type aircraft model...">

  <div id="aircraftResults" class="list-group mt-1"
       style="display:none;max-height:200px;overflow:auto;"></div>

  <div id="selectedAircraft" class="mt-2">
    <?php foreach ($selectedAircraft as $a): ?>
      <span class="badge badge-primary mr-1">
        <?= htmlspecialchars($a['manufacturer'].' '.$a['model']) ?>
        <span style="cursor:pointer"
              onclick="removeAircraft(<?= $a['aircraft_id'] ?>)">&times;</span>
      </span>
    <?php endforeach; ?>
  </div>

  <input type="hidden" name="compatible_aircraft"
         id="aircraftIds"
         value="<?= implode(',', array_column($selectedAircraft,'aircraft_id')) ?>">
</div>



<!-- EXTRA -->
<div class="form-group">
  <label>Extra Details</label>
  <textarea class="form-control" name="extra_details"><?= htmlspecialchars($part['extra_details']); ?></textarea>
</div>

<div class="form-group">
  <label>Warranty</label>
  <input type="text" class="form-control" name="warranty"
         value="<?= htmlspecialchars($part['warranty']); ?>">
</div>


                    <div class="form-group">
                    <label for="images">Part Images</label>
                    <!-- Hidden file input for initial image uploads -->
                    <input type="file" class="form-control" id="images" name="images[]" multiple style="display:none;">
                
                    <?php
                    // Fetch part images from database
                    $stmt = $pdo->prepare("
                        SELECT image_id, image_url 
                        FROM product_images 
                        WHERE product_id = ? 
                          AND product_type = 'part' 
                        ORDER BY sort_order ASC
                    ");
                    $stmt->execute([$part_id]); // Make sure $part_id is defined
                    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                
                    <?php if (!empty($images)): ?>
                        <div id="sortable-part-images" class="photo-grid mt-4">
                            <?php foreach ($images as $index => $img): ?>
                                <div class="photo-item<?= $index === 0 ? ' main-photo' : '' ?>" data-id="<?= $img['image_id']; ?>">
                                    <img src="../vendors/<?= htmlspecialchars($img['image_url']); ?>" alt="Part Image" style="max-width: 100%; height: auto;">
                                    
                                    <?php if ($index === 0): ?>
                                        <div class="main-label">Main</div>
                                        <div class="photo-warning">Must be over 500 pixels wide</div>
                                    <?php endif; ?>
                                    
                                    <a href="delete_part_image.php?id=<?= $img['image_id']; ?>&part_id=<?= $part_id ?>" 
                                       class="btn btn-sm btn-danger mt-1">Delete</a>
                                </div>
                            <?php endforeach; ?>
                
                            <!-- Add new photo button -->
                            <div class="photo-item add-photo">
                                <label for="add-part-photo">
                                    <span>&#128247;</span><br>Add
                                </label>
                                <input type="file" name="images[]" id="add-part-photo" style="display: none;" multiple>
                            </div>
                        </div>
                
                        <button type="button" class="btn btn-success mt-3" onclick="savePartImageOrder()">Save Image Order</button>
                    <?php else: ?>
                        <div id="sortable-part-images" class="photo-grid mt-4">
                            <!-- No images, still show Add button -->
                            <div class="photo-item add-photo">
                                <label for="add-part-photo">
                                    <span>&#128247;</span><br>Add
                                </label>
                                <input type="file" name="images[]" id="add-part-photo" style="display: none;" multiple>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                    <button type="submit" class="btn" style="background-color:#4747a1;color:white;">Update Part</button>

                </form>
            </div>
        </div>
    </div>
</div>

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
<script>
// ===============================
// Aircraft Search & Selection
// ===============================
const searchInput = document.getElementById("aircraftSearch");
const resultsBox = document.getElementById("aircraftResults");
const selectedBox = document.getElementById("selectedAircraft");
const hiddenInput = document.getElementById("aircraftIds");

let selectedAircraft = [];

// Preload existing aircraft from hidden input
const existingIds = hiddenInput.value;
if (existingIds) {
    fetch("fetch_aircraft_by_ids.php?ids=" + existingIds)
        .then(res => res.json())
        .then(data => {
            selectedAircraft = data;
            renderAircraft();
        });
}

// Search for aircraft
searchInput.addEventListener("keyup", () => {
    const q = searchInput.value.trim();
    if (q.length < 2) {
        resultsBox.style.display = "none";
        return;
    }

    fetch("fetch_compatible_aircraft.php?q=" + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
            resultsBox.innerHTML = "";
            resultsBox.style.display = "block";

            data.forEach(item => {
                const el = document.createElement("a");
                el.href = "#";
                el.className = "list-group-item list-group-item-action";
                el.textContent = item.manufacturer + " " + item.model;

                el.onclick = e => {
                    e.preventDefault();
                    if (!selectedAircraft.some(a => a.aircraft_id == item.aircraft_id)) {
                        selectedAircraft.push(item);
                        renderAircraft();
                    }
                    searchInput.value = "";
                    resultsBox.style.display = "none";
                };

                resultsBox.appendChild(el);
            });
        });
});

// Render selected aircraft
function renderAircraft() {
    selectedBox.innerHTML = "";
    selectedAircraft.forEach(a => {
        selectedBox.innerHTML += `
            <span class="badge badge-primary mr-1">
                ${a.manufacturer ?? ''} ${a.model ?? ''}
                <span style="cursor:pointer" onclick="removeAircraft(${a.aircraft_id})">&times;</span>
            </span>`;
    });
    hiddenInput.value = selectedAircraft.map(a => a.aircraft_id).join(",");
}

// Remove aircraft
function removeAircraft(id) {
    selectedAircraft = selectedAircraft.filter(a => a.aircraft_id != id);
    renderAircraft();
}
</script>

<script>
// ===============================
// Manufacturer Autocomplete
// ===============================
const manufacturerInput = document.getElementById('manufacturerField');
const manufacturerResults = document.getElementById('manufacturerResults');

manufacturerInput.addEventListener('keyup', () => {
    const q = manufacturerInput.value.trim();
    if (q.length < 1) {
        manufacturerResults.style.display = 'none';
        return;
    }

    fetch('fetch_manufacturer.php?q=' + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
            manufacturerResults.innerHTML = '';
            manufacturerResults.style.display = 'block';

            data.forEach(item => {
                const el = document.createElement('a');
                el.href = '#';
                el.className = 'list-group-item list-group-item-action';
                el.textContent = item.manufacturer;

                el.onclick = e => {
                    e.preventDefault();
                    manufacturerInput.value = item.manufacturer;
                    manufacturerResults.style.display = 'none';
                };

                manufacturerResults.appendChild(el);
            });
        });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const priceLabel = document.getElementById('price_label');
    const priceInput = document.getElementById('price');
    const currency = document.getElementById('currency');

    function togglePriceInputs() {
        // Labels where price & currency must be disabled
        const disableOptions = ['call', 'make_offer'];

        if (disableOptions.includes(priceLabel.value)) {
            priceInput.disabled = true;
            priceInput.value = '';
            currency.disabled = true;
            currency.value = '';
        } else {
            priceInput.disabled = false;
            currency.disabled = false;
        }
    }

    // Run once on page load
    togglePriceInputs();

    // Listen for dropdown change
    priceLabel.addEventListener('change', togglePriceInputs);
});
</script>

<?php include "footer.php"; ?>
