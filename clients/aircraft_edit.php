<?php


include "header.php";
include "config.php";

session_start();

// Ensure user is logged in
if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];
$aircraft_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch aircraft details
$query = $pdo->prepare("SELECT * FROM aircrafts WHERE aircraft_id = ? AND vendor_id = ?");
$query->execute([$aircraft_id, $vendor_id]);
$aircraft = $query->fetch(PDO::FETCH_ASSOC);

if (!$aircraft) {
    echo "<script>alert('Aircraft not found or access denied.'); window.location.href='aircraft_list.php';</script>";
    exit();
}

// Fetch aircraft images
$image_query = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND product_type = 'aircraft'");
$image_query->execute([$aircraft_id]);
$images = $image_query->fetchAll(PDO::FETCH_COLUMN, 0);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $aircraft_id = $_POST['aircraft_id'];
    $model = $_POST['model'];
    $location = $_POST['location'];
    $aircraft_type = $_POST['aircraft_type'];
    $manufacturer = $_POST['manufacturer'];
    $registration_number = $_POST['registration_number'];
    $serial_number = $_POST['serial_number'];
    $total_time_hours = $_POST['total_time_hours'];
    $year = $_POST['year'];

    $price_label = $_POST['price_label'];
    $price = $_POST['price'] ?? null;
    $currency = $_POST['currency'] ?? null;

    // 🔥 If Call for Price OR Make Offer → clear price & currency
    if ($price_label == "call" || $price_label == "make_offer") {
        $price = null;
        $currency = null;
    }

    $description = $_POST['description'];
    $features = $_POST['features'];
    $warranty = $_POST['warranty'];

    // Engine & Propellers
    $engine1_status = $_POST['engine1_status'] ?? null;
    $engine1_hours = $_POST['engine1_hours'] ?? null;
    $engine2_status = $_POST['engine2_status'] ?? null;
    $engine2_hours = $_POST['engine2_hours'] ?? null;

    $prop1_status = $_POST['prop1_status'] ?? null;
    $prop1_hours = $_POST['prop1_hours'] ?? null;
    $prop2_status = $_POST['prop2_status'] ?? null;
    $prop2_hours = $_POST['prop2_hours'] ?? null;

    // Update aircraft record
    $update_query = $pdo->prepare("UPDATE aircrafts SET 
        model = ?, location = ?, aircraft_type = ?, manufacturer = ?, 
        registration_number = ?, serial_number = ?, year = ?, 
        price = ?, price_label = ?, currency = ?, 
        description = ?, features = ?, warranty = ?,
        engine1_status = ?, engine1_hours = ?, engine2_status = ?, engine2_hours = ?,
        prop1_status = ?, prop1_hours = ?, prop2_status = ?, prop2_hours = ?, 
        total_time_hours = ?
        WHERE aircraft_id = ? AND vendor_id = ?");

    $update_query->execute([
        $model, $location, $aircraft_type, $manufacturer,
        $registration_number, $serial_number, $year,
        $price, $price_label, $currency,
        $description, $features, $warranty,
        $engine1_status, $engine1_hours, $engine2_status, $engine2_hours,
        $prop1_status, $prop1_hours, $prop2_status, $prop2_hours,
        $total_time_hours,
        $aircraft_id, $vendor_id
    ]);

    // Handle image uploads
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {

            $image_name = basename($_FILES['images']['name'][$key]);
            $image_path = "uploads/" . uniqid() . "_" . $image_name;

            if (move_uploaded_file($tmp_name, $image_path)) {
                $insert_image_query = $pdo->prepare("
                    INSERT INTO product_images 
                    (product_id, product_type, image_url, sort_order)
                    VALUES (?, 'aircraft', ?, 
                        (SELECT IFNULL(MAX(sort_order),0)+1 
                         FROM product_images 
                         WHERE product_id = ? AND product_type='aircraft')
                    )
                ");

                $insert_image_query->execute([$aircraft_id, $image_path, $aircraft_id]);
            }
        }
    }

    echo "<script>alert('Aircraft updated successfully!'); window.location.href = 'manage_products.php';</script>";
    exit();
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
                <h3 class="font-weight-bold">Edit Aircraft</h3>
                <h6 class="font-weight-normal mb-0">
                    Update your aircraft details here.
                </h6>
            </div>
        </div>

        <!-- Aircraft Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Aircraft Information</h4>
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

                            <!-- Images -->
    <!-- Aircraft Images Upload -->
<div class="form-group">
  <label>Aircraft Images</label>

  <!-- Hidden input for selecting new files -->
  <input type="file" id="new-aircraft-photos" multiple style="display:none;">

  <!-- Hidden collector (inside form) -->
  <input type="file" id="aircraft-collector" name="images[]" multiple style="display:none;">

  <?php
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
        <div class="photo-item<?= $index === 0 ? ' main-photo' : '' ?>"
             data-id="<?= htmlspecialchars($img['image_id']); ?>" data-type="db">
          <img src="<?= htmlspecialchars($img['image_url']); ?>" alt="Aircraft Image" class="aircraft-img">
          <?php if ($index === 0): ?><div class="main-label">Main</div><?php endif; ?>
          <button type="button" class="btn btn-sm btn-danger mt-1 db-delete-btn" data-id="<?= htmlspecialchars($img['image_id']); ?>">Delete</button>

        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Add photo box -->
    <div class="photo-item add-photo" id="add-photo-box" style="cursor:pointer;">
      <span>&#128247;</span><br>Add
    </div>
  </div>

  <button type="button" class="btn btn-success mt-3" onclick="saveImageOrder()">Save Image Order</button>
</div>




                            <button type="submit" class="btn" style="background-color:#4747A1;color:white;">Update Aircraft</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- SortableJS -->


<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const grid = document.getElementById('sortable-images');
  const newInput = document.getElementById('new-aircraft-photos');
  const collector = document.getElementById('aircraft-collector');
  const addBox = document.getElementById('add-photo-box');
  const newFilesMap = new Map();

  function fileKey(f) {
    return `${f.name}|${f.size}|${f.lastModified}`;
  }

  function refreshCollectorFromDOM() {
    const dt = new DataTransfer();
    grid.querySelectorAll('.photo-item:not(.add-photo)').forEach(it => {
      if (it.dataset.type === 'new') {
        const key = it.dataset.fileKey;
        if (newFilesMap.has(key)) {
          dt.items.add(newFilesMap.get(key));
        }
      }
    });
    collector.files = dt.files;
  }

  function updateMainLabel() {
    const items = grid.querySelectorAll('.photo-item:not(.add-photo)');
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
  }

  function addNewFilePreview(file) {
    if (!file.type.startsWith('image/')) return;
    const key = fileKey(file);
    if (newFilesMap.has(key)) return;

    newFilesMap.set(key, file);
    const reader = new FileReader();
    reader.onload = function (e) {
      const item = document.createElement('div');
      item.className = 'photo-item';
      item.dataset.type = 'new';
      item.dataset.fileKey = key;
      item.dataset.id = `new-${key}`;
      item.innerHTML = `
        <img src="${e.target.result}" alt="preview" class="aircraft-img">
        <span class="remove-new" style="cursor:pointer;position:absolute;right:6px;top:4px;font-size:18px;">&times;</span>
      `;
      item.querySelector('.remove-new').addEventListener('click', () => {
        newFilesMap.delete(key);
        item.remove();
        refreshCollectorFromDOM();
        updateMainLabel();
      });
      grid.insertBefore(item, addBox);
      refreshCollectorFromDOM();
      updateMainLabel();
    };
    reader.readAsDataURL(file);
  }

  newInput.addEventListener('change', (e) => {
    Array.from(e.target.files).forEach(f => addNewFilePreview(f));
    newInput.value = null; // reset properly
  });

  addBox.addEventListener('click', () => newInput.click());

  if (grid) {
    new Sortable(grid, {
      animation: 150,
      filter: '.add-photo',
      draggable: '.photo-item:not(.add-photo)',
      onEnd: () => {
        refreshCollectorFromDOM();
        updateMainLabel();
      }
    });
  }

  // ✅ Delete handler (now inside same scope, so it can use updateMainLabel)
  document.addEventListener("click", function(e) {
    if (e.target.classList.contains("db-delete-btn")) {
      const id = e.target.dataset.id;
      if (confirm("Delete this image?")) {
        fetch("delete_image.php", {
          method: "POST",
          headers: {"Content-Type": "application/x-www-form-urlencoded"},
          body: "image_id=" + encodeURIComponent(id)
        })
        .then(res => res.text())
        .then(msg => {
          alert(msg);
          if (msg.includes("Deleted")) {
            e.target.closest(".photo-item").remove();
            updateMainLabel();
          }
        })
        .catch(err => alert("Error deleting image: " + err));
      }
    }
  });

  // ✅ Save order
  window.saveImageOrder = function () {
    const ids = Array.from(grid.querySelectorAll('.photo-item:not(.add-photo)'))
      .map(item => item.dataset.id);
    fetch('save_image_order.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ order: ids, aircraft_id: <?= json_encode($aircraft_id); ?> })
    })
    .then(r => r.text())
    .then(msg => alert(msg))
    .catch(err => alert('Error saving image order.'));
  };

  updateMainLabel();
  refreshCollectorFromDOM();
});
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