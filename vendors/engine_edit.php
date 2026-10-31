<?php
include "header.php";
include "config.php";

// Ensure user is logged in
if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];
$engine_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch engine details
$query = $pdo->prepare("SELECT * FROM engines WHERE engine_id = ? AND vendor_id = ?");
$query->execute([$engine_id, $vendor_id]);
$engine = $query->fetch(PDO::FETCH_ASSOC);

if (!$engine) {
    echo "<script>alert('Engine not found or you do not have permission to edit it.'); window.location.href = 'engine_list.php';</script>";
    exit();
}

// Fetch engine images
$image_query = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND product_type = 'engine'");
$image_query->execute([$engine_id]);
$images = $image_query->fetchAll(PDO::FETCH_COLUMN, 0);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manufacturer = $_POST['manufacturer'];
    $engine_model = $_POST['model'];
    $engine_type = $_POST['engine_type'];
    $power_thrust = $_POST['power_thrust'];
    $total_time_hours = $_POST['total_time_hours'];
    $hr = $_POST['hr'];
    $cycles = $_POST['cycles'];
    $cycles_remaining = $_POST['cycles_remaining'];
    $condition = $_POST['condition'];
    $year = $_POST['year'];
    $sale_type = $_POST['sale_type'];
    $tags = $_POST['tags'];
    $price_label = $_POST['price_label'];
    $currency = $_POST['currency'];
    $price = $_POST['price'];
    $country = $_POST['country'];
    $extra_details = $_POST['extra_details'];
    $warranty = $_POST['warranty'];

    // Update engine details
    $update_query = $pdo->prepare("
        UPDATE engines SET 
            manufacturer = ?, 
            model = ?,
            engine_type = ?, 
            power_thrust = ?, 
            total_time_hours = ?, 
            hr = ?, 
            cycles = ?, 
            cycles_remaining = ?, 
            `condition` = ?, 
            year = ?, 
            sale_type = ?, 
            tags = ?, 
            price_label = ?, 
            currency = ?, 
            price = ?, 
            country = ?, 
            extra_details = ?, 
            warranty = ?
        WHERE engine_id = ? AND vendor_id = ?
    ");

    $update_query->execute([
        $manufacturer, 
        $engine_model, 
        $engine_type, 
        $power_thrust, 
        $total_time_hours, 
        $hr, 
        $cycles, 
        $cycles_remaining, 
        $condition, 
        $year, 
        $sale_type, 
        $tags, 
        $price_label, 
        $currency, 
        $price, 
        $country, 
        $extra_details, 
        $warranty, 
        $engine_id, 
        $vendor_id
    ]);

    
if (!empty($_FILES['images']['name'][0])) {
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        if (is_uploaded_file($tmp_name)) {
            $original_name = basename($_FILES['images']['name'][$key]);
            $safe_name = preg_replace("/[^A-Za-z0-9\-_\.]/", "_", $original_name);
            $unique_name = uniqid('engine_') . "_" . $safe_name;
            $target_dir = "uploads/";
            $image_path = $target_dir . $unique_name;

            if (move_uploaded_file($tmp_name, $image_path)) {
                $maxSort++;
                $insert_image = $pdo->prepare("
                    INSERT INTO product_images (product_id, product_type, image_url, sort_order)
                    VALUES (?, 'engine', ?, ?)
                ");
                $insert_image->execute([$engine_id, $image_path, $maxSort]);
            }
        }
    }
}


// after this, continue redirect or render success message


    echo "<script>alert('Engine updated successfully!'); window.location.href = 'manage_products.php';</script>";
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
                <h3 class="font-weight-bold">Edit Engine</h3>
                <h6 class="font-weight-normal mb-0">Update your engine details here.</h6>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Engine Information</h4>
                        <form method="POST" enctype="multipart/form-data">
                          <input type="hidden" name="engine_id" value="<?= htmlspecialchars($engine['engine_id']); ?>">

                        <!-- Engine Model & Manufacturer Row (Text Inputs, Editable) -->
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="manufacturer">Manufacturer</label>
                                <input type="text" class="form-control" id="manufacturer" name="manufacturer" value="<?= htmlspecialchars($engine['manufacturer']); ?>" required>
                            </div>
                        
                            <div class="form-group col-md-3">
                                <label for="model">Engine Model</label>
                                <input type="text" class="form-control" id="model" name="model" value="<?= htmlspecialchars($engine['model']); ?>" required>
                            </div>
                        
                            <div class="form-group col-md-3">
                                <label for="engine_type">Engine Type</label>
                                <input type="text" class="form-control" id="engine_type" name="engine_type" value="<?= htmlspecialchars($engine['engine_type']); ?>" required>
                            </div>
                        
                            <div class="form-group col-md-3">
                                <label for="power_thrust">Power/Thrust</label>
                                <input type="text" class="form-control" id="power_thrust" name="power_thrust" value="<?= htmlspecialchars($engine['power_thrust']); ?>"  required>
                            </div>
                        </div>


    <!-- Hours & Cycles Row -->
    <div class="form-row">
        <div class="form-group col-md-3">
            <label>Total Time (Hours)</label>
            <input type="number" class="form-control" name="total_time_hours" value="<?= htmlspecialchars($engine['total_time_hours']); ?>">
        </div>
        <div class="form-group col-md-3">
            <label>Hours Remaining</label>
            <input type="number" class="form-control" name="hr" value="<?= htmlspecialchars($engine['hr']); ?>">
        </div>
        <div class="form-group col-md-3">
            <label>Total Cycles</label>
            <input type="number" class="form-control" name="cycles" value="<?= htmlspecialchars($engine['cycles']); ?>">
        </div>
        <div class="form-group col-md-3">
            <label>Cycles Remaining</label>
            <input type="number" class="form-control" name="cycles_remaining" value="<?= htmlspecialchars($engine['cycles_remaining']); ?>">
        </div>
    </div>

    <!-- Condition, Year, Sale Type, Tag Type -->
    <div class="form-row">
        <div class="form-group col-md-3">
            <label>Condition</label>
            <select class="form-control" name="condition">
                <?php
                $conditions = ["Factory New"=>"FN (Factory New)","Overhauled"=>"OH (Overhauled)","Serviceable"=>"SV (Serviceable)","As Removed"=>"AR (As Removed)","New Surplus"=>"NS (New Surplus)","Repaired"=>"RP (Repaired)","Beyond Economic Repair"=>"BER (Beyond Economic Repair)"];
                foreach($conditions as $val=>$label){
                    $selected = ($engine['condition']==$val) ? "selected" : "";
                    echo "<option value='$val' $selected>$label</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label>Year</label>
            <input type="number" class="form-control" name="year" value="<?= htmlspecialchars($engine['year']); ?>">
        </div>

        <div class="form-group col-md-3">
            <label>Sale Type</label>
            <select class="form-control" name="sale_type">
                <option value="outright" <?= $engine['sale_type']=="outright"?"selected":""; ?>>Outright</option>
                <option value="exchange" <?= $engine['sale_type']=="exchange"?"selected":""; ?>>Exchange</option>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label>Tag Type</label>
            <select class="form-control" name="tags">
                <?php
                $tags = ["Dual Release (8130/EASA Form 1)","8130","EASA Form 1","Yellow Tag","No Tag"];
                foreach($tags as $tag){
                    $selected = ($engine['tags']==$tag)?"selected":"";
                    echo "<option value='$tag' $selected>$tag</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <!-- Price, Currency, Price Label, Country -->
    <div class="form-row">
        <div class="form-group col-md-3">
            <label>Price Label (Optional)</label>
            <select class="form-control" name="price_label" id="price_label">
                <option value="" <?= $engine['price_label']==""?"selected":""; ?>>----Select---</option>
                <option value="call" <?= $engine['price_label']=="call"?"selected":""; ?>>Call for Price</option>
                <option value="sp" <?= $engine['price_label']=="sp"?"selected":""; ?>>Sale Price</option>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label>Currency</label>
            <select class="form-control" name="currency" id="currency">
                <option value="<?= htmlspecialchars($engine['currency']); ?>"><?= htmlspecialchars($engine['currency']); ?></option>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label>Price</label>
            <input type="text" class="form-control" name="price"  id="price" value="<?= htmlspecialchars($engine['price']); ?>" placeholder="Enter Price">
        </div>

        <div class="form-group col-md-3">
            <label>Country</label>
            <input type="text" class="form-control" name="country"  value="<?= htmlspecialchars($engine['country']); ?>">
        </div>
    </div>

    <!-- Details & Warranty -->
    <div class="form-group">
        <label>Details</label>
        <textarea class="form-control" name="extra_details" rows="4"><?= htmlspecialchars($engine['extra_details']); ?></textarea>
    </div>
    <div class="form-group">
        <label>Warranty</label>
        <textarea class="form-control" name="warranty" rows="4"><?= htmlspecialchars($engine['warranty']); ?></textarea>
    </div>
                            
                          <div class="form-group">
    <label for="images">Engine Images</label>
    <input type="file" id="images" name="images[]" class="form-control" multiple>
    
    <?php
    $stmt = $pdo->prepare("SELECT image_id, image_url FROM product_images WHERE product_id = ? AND product_type = 'engine' ORDER BY sort_order ASC");
    $stmt->execute([$engine_id]);
    $images = $stmt->fetchAll();
    ?>

    <?php if (!empty($images)): ?>
    <div id="sortable-engine-images" class="photo-grid mt-4">
        <?php foreach ($images as $index => $img): ?>
        <div class="photo-item<?= $index === 0 ? ' main-photo' : '' ?>" data-id="<?= $img['image_id']; ?>">
            <img src="<?= htmlspecialchars($img['image_url']); ?>" alt="Engine Image">
            <span class="delete-btn" onclick="deleteEngineImage(<?= $img['image_id'] ?>)">×</span>
            <?php if ($index === 0): ?>
            <div class="main-label">Main</div>
            <br><br>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <div class="photo-item add-photo">
            <label for="images">
                <span>&#128247;</span><br>Add
            </label>
        </div>
    </div>

    <button type="button" class="btn btn-success mt-3" onclick="saveEngineImageOrder()">Save Image Order</button>
    <?php else: ?>
    <p>No images uploaded yet.</p>
    <?php endif; ?>
</div>




                            <button type="submit" class="btn" style="background-color:#4747A1;color:white;">Update Engine</button>
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
  const grid = document.getElementById('sortable-engine-images');
  const mainInput = document.getElementById('images');
  const addInput = document.getElementById('add-engine-photo');
  const collector = document.getElementById('all-images-collector');

  let allFiles = []; // will hold all selected files

  function refreshCollector() {
    // Create a new DataTransfer to hold files
    const dt = new DataTransfer();
    allFiles.forEach(f => dt.items.add(f));
    collector.files = dt.files;
  }

  function handleFiles(files) {
    Array.from(files).forEach((file, index) => {
      if (!file.type.startsWith('image/')) return;
      allFiles.push(file);

      const reader = new FileReader();
      reader.onload = function (e) {
        const item = document.createElement('div');
        item.className = 'photo-item';
        item.dataset.id = `new-${Date.now()}-${index}`;
        item.innerHTML = `
          <img src="${e.target.result}">
          <span class="delete-btn">&times;</span>
        `;
        item.querySelector('.delete-btn').onclick = () => {
          // remove file from allFiles when deleted
          allFiles = allFiles.filter(f => f !== file);
          item.remove();
          refreshCollector();
          updateEngineMainLabel();
        };
        const addBtn = grid.querySelector('.add-photo');
        grid.insertBefore(item, addBtn);
        updateEngineMainLabel();
      };
      reader.readAsDataURL(file);
    });
    refreshCollector();
  }

  [mainInput, addInput].forEach(input => {
    input.addEventListener('change', e => handleFiles(e.target.files));
  });

  // Sortable support
  new Sortable(grid, {
    animation: 150,
    filter: '.add-photo',
    draggable: '.photo-item:not(.add-photo)',
    onEnd: updateEngineMainLabel
  });

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
  }
});

// Get IDs in sorted order
function getEngineSortedIds() {
  const items = document.querySelectorAll('#sortable-engine-images .photo-item:not(.add-photo)');
  return Array.from(items).map(item => item.dataset.id);
}

// Save order via AJAX
function saveEngineImageOrder() {
  const ids = getEngineSortedIds();
  if (ids.length === 0) {
    alert("No engine images to sort.");
    return;
  }

  fetch('update_engine_image_order.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ order: ids })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
    } else {
      alert("Error: " + data.message);
    }
  })
  .catch(error => {
    console.error(error);
    alert('Error saving engine image order.');
  });
}

// Delete one image via AJAX
function deleteEngineImage(imageId) {
  if (!confirm("Are you sure you want to delete this image?")) return;

  fetch('delete_engine_image.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ image_id: imageId })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Remove from DOM
      const elem = document.querySelector(`.photo-item[data-id='${imageId}']`);
      if (elem) elem.remove();
      updateEngineMainLabel();
    } else {
      alert("Error: " + data.message);
    }
  })
  .catch(error => {
    console.error(error);
    alert('Error deleting engine image.');
  });
}
</script>
<script>
// Call this after uploading a new image via AJAX
function appendEngineImageToGrid(imageId, imageUrl) {
  const grid = document.getElementById('sortable-engine-images');
  if (!grid) return;

  const div = document.createElement('div');
  div.className = 'photo-item';
  div.dataset.id = imageId;

  div.innerHTML = `
    <img src="${imageUrl}" alt="Uploaded Image">
    <span class="delete-btn" onclick="deleteEngineImage(${imageId})">×</span>
  `;

  // Insert before the .add-photo box if it exists
  const addPhoto = grid.querySelector('.add-photo');
  if (addPhoto) {
    grid.insertBefore(div, addPhoto);
  } else {
    grid.appendChild(div);
  }

  updateEngineMainLabel();
}
</script>
<script>
$(document).ready(function() {

    // Load Engine Models based on Manufacturer selection
    $('#manufacturer').change(function() {
        var manufacturer = $(this).val();
        $('#engine_model').html('<option value="">Loading...</option>');

        $.ajax({
            url: 'get_engine_models.php',
            type: 'POST',
            data: { manufacturer: manufacturer },
            success: function(response) {
                $('#engine_model').html('<option value="">Select Engine Model</option>' + response);
            }
        });
    });

    // Load Engine Type & Power/Thrust based on Engine Model selection
    $('#engine_model').change(function() {
        var model = $(this).val();

        $.ajax({
            url: 'get_engine_details.php',
            type: 'POST',
            data: { model: model },
            dataType: 'json',
            success: function(data) {
                $('#engine_type').val(data.engine_type);
                $('#power_thrust').val(data.power_thrust);
            }
        });
    });

    // Trigger change on page load to load models for the current manufacturer
    var currentManufacturer = '<?= addslashes($engine['manufacturer']); ?>';
    var currentModel = '<?= addslashes($engine['engine_model']); ?>';

    if(currentManufacturer) {
        $.ajax({
            url: 'get_engine_models.php',
            type: 'POST',
            data: { manufacturer: currentManufacturer },
            success: function(response) {
                $('#engine_model').html('<option value="">Select Engine Model</option>' + response);
                $('#engine_model').val(currentModel).trigger('change');
            }
        });
    }
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