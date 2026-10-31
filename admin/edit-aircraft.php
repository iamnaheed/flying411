<?php
session_start();
include "config.php";

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Validate aircraft ID
if (!isset($_GET['aircraft_id'])) {
    header("Location: admin-aircraft.php");
    exit;
}

$aircraft_id = intval($_GET['aircraft_id']);

// Fetch aircraft
$stmt = $pdo->prepare("SELECT * FROM aircrafts WHERE aircraft_id = ?");
$stmt->execute([$aircraft_id]);
$aircraft = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aircraft) {
    echo "<p>Aircraft not found.</p>";
    exit;
}

// Fetch images
$image_query = $pdo->prepare("
    SELECT image_url FROM product_images 
    WHERE product_id = ? AND product_type = 'aircraft'
");
$image_query->execute([$aircraft_id]);
$images = $image_query->fetchAll(PDO::FETCH_COLUMN);

// =============================================
//  🔥 HANDLE ADMIN UPDATE FORM SUBMISSION
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $model = $_POST['model'];
    $location = $_POST['location'];
    $aircraft_type = $_POST['aircraft_type'];
    $manufacturer = $_POST['manufacturer'];
    $registration_number = $_POST['registration_number'];
    $serial_number = $_POST['serial_number'];
    $year = $_POST['year'];

    // Price fields
    $price_label = $_POST['price_label'];
    $price = $_POST['price'] ?? null;
    $currency = $_POST['currency'] ?? null;

    // 🔥 If "Call" or "Make Offer", clear price & currency
    if ($price_label == "call" || $price_label == "make_offer") {
        $price = null;
        $currency = null;
    }

    $description = $_POST['description'];
    $features = $_POST['features'];
    $warranty = $_POST['warranty'];
    $total_time_hours = $_POST['total_time_hours'];
    $status = $_POST['status'];

    // Engine & Prop Fields
    $engine1_status = $_POST['engine1_status'];
    $engine1_hours = $_POST['engine1_hours'];
    $engine2_status = $_POST['engine2_status'];
    $engine2_hours = $_POST['engine2_hours'];
    $prop1_status = $_POST['prop1_status'];
    $prop1_hours = $_POST['prop1_hours'];
    $prop2_status = $_POST['prop2_status'];
    $prop2_hours = $_POST['prop2_hours'];

    // 🔥 UPDATE QUERY WITH CURRENCY ADDED
    $update = $pdo->prepare("
        UPDATE aircrafts SET
            model = ?, location = ?, aircraft_type = ?, manufacturer = ?, 
            registration_number = ?, serial_number = ?, year = ?,
            price = ?, price_label = ?, currency = ?,
            description = ?, features = ?, warranty = ?, total_time_hours = ?, status = ?,
            engine1_status = ?, engine1_hours = ?, engine2_status = ?, engine2_hours = ?,
            prop1_status = ?, prop1_hours = ?, prop2_status = ?, prop2_hours = ?
        WHERE aircraft_id = ?
    ");

    $update->execute([
        $model, $location, $aircraft_type, $manufacturer,
        $registration_number, $serial_number, $year,
        $price, $price_label, $currency,
        $description, $features, $warranty, $total_time_hours, $status,
        $engine1_status, $engine1_hours, $engine2_status, $engine2_hours,
        $prop1_status, $prop1_hours, $prop2_status, $prop2_hours,
        $aircraft_id
    ]);

    // =============================================
    //  IMAGES UPLOAD (remain unchanged)
    // =============================================

    $files = $_FILES['images'] ?? null;

    if ($files && !empty($files['name'][0])) {

        $stmt2 = $pdo->prepare("
            SELECT COALESCE(MAX(sort_order), -1) AS max_order
            FROM product_images
            WHERE product_id = ? AND product_type = 'aircraft'
        ");
        $stmt2->execute([$aircraft_id]);
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
        $maxSort = isset($row['max_order']) ? (int)$row['max_order'] : -1;

        $owner = $_POST['owner'] ?? 'vendors';
        $uploadDir = "uploads/";
        $targetDir = __DIR__ . "/../" . $owner . "/" . $uploadDir;

        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        foreach ($files['tmp_name'] as $index => $tmpName) {

            $error = $files['error'][$index];
            if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmpName)) continue;

            $safeName = preg_replace("/[^A-Za-z0-9\-_\.]/", "_", basename($files['name'][$index]));
            $uniqueName = uniqid('aircraft_', true) . "_" . $safeName;

            $targetPath = $targetDir . $uniqueName;
            $relativePath = $uploadDir . $uniqueName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $maxSort++;

                $insert = $pdo->prepare("
                    INSERT INTO product_images
                    (product_id, product_type, image_url, sort_order, created_at)
                    VALUES (?, 'aircraft', ?, ?, NOW())
                ");
                $insert->execute([$aircraft_id, $relativePath, $maxSort]);
            }
        }
    }

    echo "<script>alert('Aircraft updated successfully!'); window.location.href = 'aircraft-listing.php?vendor_id=" . $aircraft['vendor_id'] . "';</script>";
    exit;
}

include "header.php";
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
        <h3>Edit Aircraft</h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
                            <!-- Hidden Fields -->
                            <input type="hidden" name="aircraft_id" value="<?= htmlspecialchars($aircraft['aircraft_id']); ?>">

                            <!-- Model -->
                            <div class="form-group">
                                <label for="model">Model</label>
                                <input type="text" class="form-control" id="model" name="model" value="<?= htmlspecialchars($aircraft['model'] ?? ''); ?>" required>
                            </div>

                            <!-- Location -->
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($aircraft['location'] ?? ''); ?>" required>
                            </div>

                            <!-- Aircraft Type -->
                            <div class="form-group">
                                <label for="aircraft_type">Aircraft Type</label>
                                <input type="text" class="form-control" id="aircraft_type" name="aircraft_type" value="<?= htmlspecialchars($aircraft['aircraft_type'] ?? ''); ?>" required>
                            </div>

                            <!-- Manufacturer -->
                            <div class="form-group">
                                <label for="manufacturer">Manufacturer</label>
                                <input type="text" class="form-control" id="manufacturer" name="manufacturer" value="<?= htmlspecialchars($aircraft['manufacturer'] ?? ''); ?>" required>
                            </div>

                           <div class="form-group">
                                <label for="total_time_hours">Total Time</label>
                                <input type="number" class="form-control" id="total_time_hours" name="total_time_hours" value="<?= htmlspecialchars($aircraft['total_time_hours'] ?? ''); ?>" required>
                            </div>
                            <!-- Condition -->
                            <div class="form-group">
                                <label for="registration_number">Registration Number</label>
                                <input type="text" class="form-control" id="registration_number" name="registration_number" value="<?= htmlspecialchars($aircraft['registration_number'] ?? ''); ?>" required>
                            </div>

  <!-- Total Time Hours -->
                            <div class="form-group">
                                <label for="serial_number">Serial Number</label>
                                <input type="text" class="form-control" id="serial_number" name="serial_number" value="<?= htmlspecialchars($aircraft['serial_number'] ?? ''); ?>" required>
                            </div>

                         <!-- Engine and Propeller Details -->
<div class="form-group">
    <label for="engine1_status">Engine 1 Status</label>
    <input type="text" class="form-control" id="engine1_status" name="engine1_status" value="<?= htmlspecialchars($aircraft['engine1_status'] ?? ''); ?>">
</div>

<div class="form-group">
    <label for="engine1_hours">Engine 1 Hours</label>
    <input type="number" class="form-control" id="engine1_hours" name="engine1_hours" value="<?= htmlspecialchars($aircraft['engine1_hours'] ?? ''); ?>">
</div>

<div class="form-group">
    <label for="engine2_status">Engine 2 Status</label>
    <input type="text" class="form-control" id="engine2_status" name="engine2_status" value="<?= htmlspecialchars($aircraft['engine2_status'] ?? ''); ?>">
</div>

<div class="form-group">
    <label for="engine2_hours">Engine 2 Hours</label>
    <input type="number" class="form-control" id="engine2_hours" name="engine2_hours" value="<?= htmlspecialchars($aircraft['engine2_hours'] ?? ''); ?>">
</div>

<div class="form-group">
    <label for="prop1_status">Propeller 1 Status</label>
    <input type="text" class="form-control" id="prop1_status" name="prop1_status" value="<?= htmlspecialchars($aircraft['prop1_status'] ?? ''); ?>">
</div>

<div class="form-group">
    <label for="prop1_hours">Propeller 1 Hours</label>
    <input type="number" class="form-control" id="prop1_hours" name="prop1_hours" value="<?= htmlspecialchars($aircraft['prop1_hours'] ?? ''); ?>">
</div>

<div class="form-group">
    <label for="prop2_status">Propeller 2 Status</label>
    <input type="text" class="form-control" id="prop2_status" name="prop2_status" value="<?= htmlspecialchars($aircraft['prop2_status'] ?? ''); ?>">
</div>

<div class="form-group">
    <label for="prop2_hours">Propeller 2 Hours</label>
    <input type="number" class="form-control" id="prop2_hours" name="prop2_hours" value="<?= htmlspecialchars($aircraft['prop2_hours'] ?? ''); ?>">
</div>

                            <!-- Year -->
                            <div class="form-group">
                                <label for="year">Year</label>
                                <input type="number" class="form-control" id="year" name="year" value="<?= htmlspecialchars($aircraft['year'] ?? ''); ?>" required>
                            </div>

                                                       <div class="form-group">
    <label>Price Label (Optional)</label>
    <select class="form-control" name="price_label" id="price_label">
        <option value="">-- Select --</option>
        <option value="sp" <?= $aircraft['price_label'] == 'sp' ? 'selected' : ''; ?>>Sales Price</option>
        <option value="call" <?= $aircraft['price_label'] == 'call' ? 'selected' : ''; ?>>Call for Price</option>
        <option value="obo" <?= $aircraft['price_label'] == 'obo' ? 'selected' : ''; ?>>OBO</option>
        <option value="starting_bid" <?= $aircraft['price_label'] == 'starting_bid' ? 'selected' : ''; ?>>Starting Bid</option>
        <option value="make_offer" <?= $aircraft['price_label'] == 'make_offer' ? 'selected' : ''; ?>>Make Offer</option>
    </select>
</div>

<div class="form-group">
    <label>Currency</label>
    <select class="form-control" name="currency" id="currency">
        <option value="">Loading...</option>
    </select>
</div>

<div class="form-group">
    <label for="price">Price</label>
    <input type="number" class="form-control" id="price" 
           name="price" 
           value="<?= htmlspecialchars($aircraft['price'] ?? ''); ?>">
</div>

                            <!-- Description -->
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($aircraft['description'] ?? ''); ?></textarea>
                            </div>

                            <!-- Features -->
                            <div class="form-group">
                                <label for="features">Features</label>
                                <textarea class="form-control" id="features" name="features" rows="4"><?= htmlspecialchars($aircraft['features'] ?? ''); ?></textarea>
                            </div>

                            <!-- Warranty -->
                            <div class="form-group">
                                <label for="warranty">Warranty</label>
                                <input type="text" class="form-control" id="warranty" name="warranty" value="<?= htmlspecialchars($aircraft['warranty'] ?? ''); ?>">
                            </div>
 <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status">
                    <option value="pending" <?= $aircraft['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?= $aircraft['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?= $aircraft['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
                            <!-- Images -->
    <!-- Aircraft Images Upload -->
<!-- include Sortable if you don't already -->


<!-- Aircraft Images -->
<div class="form-group">
  <label>Aircraft Images</label>

  <!-- Hidden single input used to pick new files -->
  <input type="file" id="new-aircraft-photos" multiple style="display:none;">

  <!-- Hidden collector that will actually be submitted to server; must be inside the <form> -->
  <input type="file" id="aircraft-collector" name="images[]" multiple style="display:none;">

  <?php 
  // Fetch aircraft images
  $stmt = $pdo->prepare("
      SELECT image_id, image_url 
      FROM product_images 
      WHERE product_id = ? AND product_type = 'aircraft' 
      ORDER BY sort_order ASC
  ");
  $stmt->execute([$aircraft_id]); 
  $images = $stmt->fetchAll(); 
  ?>
  
  <div id="sortable-images" class="photo-grid mt-4">
    <?php if (!empty($images)): ?>
      <?php foreach ($images as $index => $img): ?>
        <?php
        if (file_exists("../vendors/" . $img['image_url'])) {
            $image_path = "../vendors/" . $img['image_url'];
        } elseif (file_exists("../clients/" . $img['image_url'])) {
            $image_path = "../clients/" . $img['image_url'];
        } else {
            $image_path = "placeholder.jpg";
        }
        ?>
        <div class="photo-item<?= $index === 0 ? ' main-photo' : '' ?>" 
             data-id="<?= htmlspecialchars($img['image_id']); ?>" data-type="db">
          <img src="<?= htmlspecialchars($image_path); ?>" alt="Aircraft Image" class="aircraft-img">
          <?php if ($index === 0): ?><div class="main-label">Main</div><?php endif; ?>
          <button type="button" class="btn btn-sm btn-danger mt-1 db-delete-btn" 
                  data-id="<?= htmlspecialchars($img['image_id']); ?>">Delete</button>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Add photo box -->
    <div class="photo-item add-photo" id="add-photo-box" style="cursor:pointer;">
      <span>&#128247;</span><br>Add
    </div>
  </div>

  <button type="button" class="btn btn-success mt-3" onclick="saveAircraftImageOrder()">Save Image Order</button>
</div>

                            <button type="submit" class="btn btn-primary">Update Aircraft</button>
                        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // current aircraft id (available from PHP)
  const currentAircraftId = <?= json_encode($aircraft_id); ?>;

  const grid = document.getElementById('sortable-images');
  let newInput = document.getElementById('new-aircraft-photos');
  const collector = document.getElementById('aircraft-collector');
  const addBox = document.getElementById('add-photo-box');

  // Map fileKey -> File for new files
  const newFilesMap = new Map();

  function fileKey(f) {
    return `${f.name}|${f.size}|${f.lastModified}`;
  }

  // Refresh the hidden collector input based on DOM order
  function refreshCollectorFromDOM() {
    const dt = new DataTransfer();
    const items = grid.querySelectorAll('.photo-item:not(.add-photo)');
    items.forEach(it => {
      const type = it.dataset.type || '';
      if (type === 'new') {
        const key = it.dataset.fileKey;
        if (key && newFilesMap.has(key)) {
          dt.items.add(newFilesMap.get(key));
        }
      }
    });
    collector.files = dt.files;
  }

  // update "Main" label on first item
  function updateMainLabel() {
    const items = grid.querySelectorAll('.photo-item:not(.add-photo)');
    items.forEach((item, i) => {
      const existing = item.querySelectorAll('.main-label');
      existing.forEach(el => el.remove());
      item.classList.remove('main-photo');
      if (i === 0) {
        item.classList.add('main-photo');
        const label = document.createElement('div');
        label.className = 'main-label';
        label.textContent = 'Main';
        item.appendChild(label);
      }
    });
  }

  // Add preview for a new File
  function addNewFilePreview(file) {
    if (!file.type.startsWith('image/')) return;
    const key = fileKey(file);
    if (newFilesMap.has(key)) return; // skip duplicates

    newFilesMap.set(key, file);

    const reader = new FileReader();
    reader.onload = function (e) {
      const item = document.createElement('div');
      item.className = 'photo-item';
      item.dataset.type = 'new';
      item.dataset.fileKey = key;
      item.dataset.id = `new-${key}`;

      item.innerHTML = `
        <img src="${e.target.result}" alt="preview" style="max-width:100%; height:auto;">
        <span class="remove-new" style="cursor:pointer; position:absolute;right:6px;top:4px;font-size:18px;">&times;</span>
      `;

      // attach remove handler for new preview
      item.querySelector('.remove-new').addEventListener('click', () => {
        newFilesMap.delete(key);
        item.remove();
        refreshCollectorFromDOM();
        updateMainLabel();
      });

      // Insert before add-photo box
      const addBoxEl = grid.querySelector('.add-photo');
      grid.insertBefore(item, addBoxEl);

      refreshCollectorFromDOM();
      updateMainLabel();
    };
    reader.readAsDataURL(file);
  }

  // Proper reset helper for file input
  function resetFileInput(input) {
    input.value = null;
    if (input.value) {
      const clone = input.cloneNode(true);
      input.parentNode.replaceChild(clone, input);
      return clone; // return new node for rebinding
    }
    return input;
  }

  // Handle change from file input
  function bindInputEvents(inputEl) {
    inputEl.addEventListener('change', (e) => {
      const files = e.target.files;
      if (!files || !files.length) return;
      Array.from(files).forEach(f => addNewFilePreview(f));

      // ✅ reset so same file can be picked again
      newInput = resetFileInput(inputEl);
      if (newInput !== inputEl) {
        bindInputEvents(newInput); // rebind if replaced
      }
    });
  }

  bindInputEvents(newInput);

  // clicking the add-photo should open input
  addBox.addEventListener('click', () => newInput.click());

  // Attach handler to existing DB delete buttons
  function attachDbDeleteHandlers() {
    const dbBtns = document.querySelectorAll('.db-delete-btn');
    dbBtns.forEach(btn => {
      if (btn.dataset.bound) return;
      btn.dataset.bound = '1';
      btn.addEventListener('click', (ev) => {
        const id = btn.dataset.id;
        if (!id) return;
        if (!confirm('Delete this image?')) return;
        fetch('delete_aircraft_image.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ id: id, aircraft_id: currentAircraftId })
        })
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            const el = btn.closest('.photo-item');
            if (el) el.remove();
            updateMainLabel();
            refreshCollectorFromDOM();
            alert(res.message || 'Image deleted');
          } else {
            alert(res.message || 'Failed to delete image');
          }
        }).catch(err => {
          console.error(err);
          alert('Error deleting image');
        });
      });
    });
  }

  attachDbDeleteHandlers();

  // Make grid sortable
  if (grid) {
    new Sortable(grid, {
      animation: 150,
      filter: '.add-photo',
      draggable: '.photo-item:not(.add-photo)',
      onEnd: function () {
        refreshCollectorFromDOM();
        updateMainLabel();
      }
    });
  }

  const mo = new MutationObserver(() => attachDbDeleteHandlers());
  mo.observe(grid, { childList: true, subtree: true });

  // expose save function globally (for Save Image Order button)
  window.getAircraftSortedIds = function() {
    return Array.from(document.querySelectorAll('#sortable-images .photo-item:not(.add-photo)'))
      .map(item => item.dataset.id || '');
  };

  updateMainLabel();
  refreshCollectorFromDOM();
});
</script>

<script>
function saveAircraftImageOrder() {
  const ids = window.getAircraftSortedIds();
  if (!ids.length) {
    alert('No images to sort.');
    return;
  }

  fetch('save_aircraft_image_order.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ order: ids, aircraft_id: <?= json_encode($aircraft_id); ?> })
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) alert(res.message || 'Order saved');
    else alert(res.message || 'Failed to save order');
  })
  .catch(err => {
    console.error(err);
    alert('Error saving order');
  });
}
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const priceLabel = document.getElementById("price_label");
    const priceInput = document.getElementById("price");
    const currencyDropdown = document.getElementById("currency");

    const savedCurrency = "<?= $aircraft['currency'] ?? '' ?>";  // Saved currency from DB

    // Labels that disable price & currency
    const disableOptions = ["call", "make_offer"];

    /**
     * Enable / disable price + currency
     */
    function togglePriceFields() {
        if (disableOptions.includes(priceLabel.value)) {
            priceInput.disabled = true;
            priceInput.value = "";
            currencyDropdown.disabled = true;
            currencyDropdown.value = "";
        } else {
            priceInput.disabled = false;
            currencyDropdown.disabled = false;

            if (savedCurrency) {
                currencyDropdown.value = savedCurrency;
            }
        }
    }

    /**
     * Load currencies with symbols
     */
    fetch("https://open.er-api.com/v6/latest/USD")
        .then(response => response.json())
        .then(data => {

            currencyDropdown.innerHTML = "";

            // Currency symbols
             const currencySymbols = {
  "AED": "د.إ",      // UAE Dirham
  "AFN": "؋",       // Afghan Afghani
  "ALL": "L",       // Albanian Lek
  "AMD": "֏",       // Armenian Dram
  "AOA": "Kz",      // Angolan Kwanza
  "ARS": "$",       // Argentine Peso
  "AUD": "A$",      // Australian Dollar
  "AWG": "ƒ",       // Aruban Florin
  "AZN": "₼",       // Azerbaijan Manat
  "BAM": "KM",      // Bosnia & Herzegovina Convertible Mark
  "BBD": "B$",      // Barbados Dollar
  "BDT": "৳",       // Bangladeshi Taka
  "BHD": ".د.ب",    // Bahraini Dinar
  "BIF": "FBu",     // Burundian Franc
  "BMD": "B$",      // Bermudian Dollar
  "BND": "B$",      // Brunei Dollar
  "BOB": "Bs.",     // Bolivian Boliviano
  "BRL": "R$",      // Brazilian Real
  "BSD": "B$",      // Bahamian Dollar
  "BTN": "Nu.",     // Bhutan Ngultrum
  "BWP": "P",       // Botswana Pula
  "BZD": "BZ$",     // Belize Dollar
  "CAD": "C$",      // Canadian Dollar
  "CDF": "FC",      // Congolese Franc
  "CHF": "CHF",     // Swiss Franc
  "CLP": "CL$",     // Chilean Peso
  "CNY": "¥",       // Chinese Yuan
  "COP": "CO$",     // Colombian Peso
  "CRC": "₡",       // Costa Rican Colón
  "CUP": "CU$",     // Cuban Peso
  "CVE": "CV$",     // Cape Verde Escudo
  "CZK": "Kč",      // Czech Koruna
  "DJF": "Fdj",     // Djiboutian Franc
  "DKK": "kr",      // Danish Krone
  "DZD": "دج",      // Algerian Dinar
  "EGP": "£",       // Egyptian Pound
  "ERN": "Nfk",     // Eritrean Nakfa
  "ETB": "Br",      // Ethiopian Birr
  "EUR": "€",       // Euro
  "FJD": "FJ$",     // Fiji Dollar
  "FKP": "£",       // Falkland Islands Pound
  "GBP": "£",       // British Pound Sterling
  "GEL": "₾",       // Georgian Lari
  "GHS": "GH₵",     // Ghana Cedi
  "GIP": "£",       // Gibraltar Pound
  "GMD": "D",       // Gambian Dalasi
  "GNF": "FG",      // Guinean Franc
  "GTQ": "Q",       // Guatemalan Quetzal
  "GYD": "GY$",     // Guyanese Dollar
  "HKD": "HK$",     // Hong Kong Dollar
  "HNL": "HNL",     // Honduran Lempira
  "HRK": "kn",      // Croatian Kuna
  "HTG": "G",       // Haitian Gourde
  "HUF": "Ft",      // Hungarian Forint
  "IDR": "Rp",      // Indonesian Rupiah
  "ILS": "₪",       // Israeli New Shekel
  "INR": "₹",       // Indian Rupee
  "IQD": "ع.د",     // Iraqi Dinar
  "IRR": "﷼",       // Iranian Rial
  "ISK": "kr",      // Icelandic Króna
  "JMD": "J$",      // Jamaican Dollar
  "JOD": "JD",      // Jordanian Dinar
  "JPY": "¥",       // Japanese Yen
  "KES": "KSh",     // Kenyan Shilling
  "KGS": "лв",      // Kyrgyzstani Som
  "KHR": "៛",       // Cambodian Riel
  "KMF": "CF",      // Comorian Franc
  "KRW": "₩",       // South Korean Won
  "KWD": "KD",      // Kuwaiti Dinar
  "KYD": "KY$",     // Cayman Islands Dollar
  "KZT": "₸",       // Kazakhstani Tenge
  "LAK": "₭",       // Lao Kip
  "LBP": "ل.ل",     // Lebanese Pound
  "LKR": "Rs",      // Sri Lankan Rupee
  "LRD": "L$",      // Liberian Dollar
  "LSL": "L",       // Lesotho Loti
  "LYD": "LD",      // Libyan Dinar
  "MAD": "د.م.",    // Moroccan Dirham
  "MDL": "L",       // Moldovan Leu
  "MGA": "Ar",      // Malagasy Ariary
  "MKD": "ден",     // Macedonian Denar
  "MMK": "K",       // Myanmar Kyat
  "MNT": "₮",       // Mongolian Tugrik
  "MOP": "MOP$",    // Macanese Pataca
  "MRU": "UM",      // Mauritanian Ouguiya
  "MUR": "Rs",      // Mauritian Rupee
  "MVR": "MVR",     // Maldivian Rufiyaa
  "MWK": "MK",      // Malawian Kwacha
  "MXN": "MX$",     // Mexican Peso
  "MYR": "RM",      // Malaysian Ringgit
  "MZN": "MT",      // Mozambican Metical
  "NAD": "N$",      // Namibian Dollar
  "NGN": "₦",       // Nigerian Naira
  "NIO": "C$",      // Nicaraguan Córdoba
  "NOK": "kr",      // Norwegian Krone
  "NPR": "₨",       // Nepalese Rupee
  "NZD": "NZ$",     // New Zealand Dollar
  "OMR": "ر.ع.",    // Omani Rial
  "PAB": "B/.",     // Panamanian Balboa
  "PEN": "S/.",     // Peruvian Sol
  "PGK": "K",       // Papua New Guinean Kina
  "PHP": "₱",       // Philippine Peso
  "PKR": "₨",       // Pakistani Rupee
  "PLN": "zł",      // Polish Zloty
  "PYG": "₲",       // Paraguayan Guarani
  "QAR": "ر.ق",    // Qatari Rial
  "RON": "lei",     // Romanian Leu
  "RSD": "din",     // Serbian Dinar
  "RUB": "₽",       // Russian Ruble
  "RWF": "RF",      // Rwandan Franc
  "SAR": "﷼",       // Saudi Riyal
  "SBD": "SI$",     // Solomon Islands Dollar
  "SCR": "₨",       // Seychellois Rupee
  "SDG": "S£",      // Sudanese Pound
  "SEK": "kr",      // Swedish Krona
  "SGD": "S$",      // Singapore Dollar
  "SHP": "£",       // Saint Helena Pound
  "SLL": "Le",      // Sierra Leone Leone
  "SOS": "S",       // Somali Shilling
  "SRD": "SR$",     // Surinam Dollar
  "SSP": "SS£",     // South Sudanese Pound
  "STN": "Db",      // São Tomé and Príncipe Dobra
  "SYP": "£",       // Syrian Pound
  "SZL": "E",       // Swazi Lilangeni
  "THB": "฿",       // Thai Baht
  "TJS": "ЅМ",      // Tajikistani Somoni
  "TMT": "m",       // Turkmenistan Manat
  "TND": "د.ت",     // Tunisian Dinar
  "TOP": "T$",      // Tongan Paʻanga
  "TRY": "₺",       // Turkish Lira
  "TTD": "TT$",     // Trinidad and Tobago Dollar
  "TWD": "NT$",     // New Taiwan Dollar
  "TZS": "TSh",     // Tanzanian Shilling
  "UAH": "₴",       // Ukrainian Hryvnia
  "UGX": "USh",     // Ugandan Shilling
  "USD": "$",       // US Dollar
  "UYU": "$U",      // Uruguayan Peso
  "UZS": "лв",      // Uzbekistan Som
  "VES": "Bs.S",    // Venezuelan Bolívar Soberano
  "VND": "₫",       // Vietnamese Dong
  "VUV": "VT",      // Vanuatu Vatu
  "WST": "WS$",     // Samoan Tala
  "XAF": "FCFA",    // CFA Franc BEAC
  "XCD": "EC$",     // East Caribbean Dollar
  "XOF": "CFA",     // CFA Franc BCEAO
  "XPF": "₣",       // CFP Franc
  "YER": "﷼",      // Yemeni Rial
  "ZAR": "R",       // South African Rand
  "ZMW": "ZK"       // Zambian Kwacha
};


            // Default USD
            currencyDropdown.innerHTML += `
                <option value="$">USD ($)</option>
            `;

            // Add all currencies
            Object.keys(data.rates).forEach(code => {
                if (code !== "USD") {
                    const symbol = currencySymbols[code] || code;
                    currencyDropdown.innerHTML += `
                        <option value="${symbol}">${code} (${symbol})</option>
                    `;
                }
            });

            // Select saved currency on edit page
            if (savedCurrency) {
                currencyDropdown.value = savedCurrency;
            }

            togglePriceFields();
        });

    // Run fixer when user changes price label
    priceLabel.addEventListener("change", togglePriceFields);

    togglePriceFields(); // Initial run
});
</script>


<?php include "footer.php"; ?>
