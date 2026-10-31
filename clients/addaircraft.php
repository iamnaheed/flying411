<?php
include "header.php";
include "config.php"; // Database connection

// Start session and check vendor authentication
session_start();
if (!isset($_SESSION['vendor_id'])) {
   header("Location: ../login.php");
    exit;
}
$vendor_id = $_SESSION['vendor_id'];

try {
    // Fetch unique aircraft types from the database
    $query = "SELECT DISTINCT aircraft_type FROM aircraft_models";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $aircraft_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging: Check if data is fetched
    if (!$aircraft_types) {
        echo "<p style='color: red;'>No aircraft types found in the database.</p>";
    }
} catch (PDOException $e) {
    die("<p style='color: red;'>Query Failed: " . $e->getMessage() . "</p>");
}
?>

<head>
    <meta charset="UTF-8">
    <style>
.upload-container {
       border: 3px dashed #ccc;
    display: flex;
    gap: 30px;
    padding: 35px;
    max-width: 100%;
  }

  .main-preview {
    width: 350px;
    height: 250px;
    border: 2px solid #ccc;
    border-radius: 10px;
    overflow: hidden;
    background: #f7f7f7;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .main-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
  }

  .gallery {
    display: grid;
    grid-template-columns: repeat(4, 100px);
    gap: 10px;
  }

  .thumb {
    width: 100px;
    height: 80px;
    border: 2px solid #ddd;
    border-radius: 8px;
    position: relative;
    background: #fff;
    overflow: hidden;
  }

  .thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: grab;
  }

  .thumb .delete {
    position: absolute;
    top: 2px;
    right: 2px;
    background: black;
    color: white;
    font-size: 12px;
    padding: 2px 5px;
    border-radius: 50%;
    cursor: pointer;
  }

  .add-box {
    width: 100px;
    height: 80px;
    border: 2px dashed #999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #999;
    cursor: pointer;
    border-radius: 8px;
    transition: 0.3s ease;
  }

  .add-box:hover {
    background: #f0f0f0;
  }

  input[type="file"].hidden-input {
    display: none;
  }

  .drag-over {
    border: 2px dashed blue !important;
  }
  
  
  
  .drag-drop-area {
    border: 2px dashed #ccc;
    padding: 70px 0px;
    text-align: center;
    border-radius: 10px;
    cursor: pointer;
    transition: border-color 0.3s ease;
  }

  .drag-drop-area.dragover {
    border-color: #4CAF50;
  }

  .drag-drop-area input[type="file"] {
    display: none;
  }

  .upload-buttons {
    margin-top: 15px;
    display: flex;
    flex-direction: column;
    width: 300px;
    margin: auto;
  }
  .drag-drop-area img{
      width:70px;
  } 

  .upload-buttons button {
       margin: 10px;
    padding: 12px 22px;
    border: 1px solid gray;
    border-radius: 32px;
  }
    </style>
</head>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <h3 class="font-weight-bold">Add Aircraft</h3>
        <h6 class="font-weight-normal mb-0">Fill in the details below to add a new aircraft.</h6>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Aircraft Details</h4>
            <form method="POST" action="submit_aircraft.php" enctype="multipart/form-data">
              <input type="hidden" name="vendor_id" value="<?= $vendor_id ?>">

              <!-- Aircraft Type -->
                         <div class="form-group">
                        <label>Aircraft Type</label>
                        <select class="form-control" id="aircraft_type" name="aircraft_type" required>
                            <option value="">Select Aircraft Type</option>
                            <?php foreach ($aircraft_types as $type) : ?>
                                <option value="<?php echo htmlspecialchars($type['aircraft_type']); ?>">
                                    <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $type['aircraft_type']))); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                
                <div class="form-group">
                    <label>Manufacturer</label>
                    <select class="form-control" id="manufacturer" name="manufacturer" required>
                        <option value="">Select Manufacturer</option>
                    </select>
                </div>
                
                <div class="form-group" id="modelGroup">
                    <label>Model</label>
                    <select class="form-control" id="model" name="model" required>
                        <option value="">Select Model</option>
                    </select>
                </div>
                <div class="form-group"> 
                      <label>Total Time</label>
                      <input type="number" class="form-control" name="total_time_hours"  min="1" required>
                    </div>

                <div class="form-group">
                  <label>Registration Number</label>
                  <input type="text" class="form-control" name="registration_number" required>
                </div>
                <!-- Serial Number -->
                <div class="form-group">
                  <label>Serial Number</label>
                  <input type="text" class="form-control" name="serial_number">
                </div>

             
        
<!-- Common Engine Fields -->
<div id="engine-fields" class="engine-group" style="display:none;">
  <div class="form-row">
    <div class="form-group col-md-3">
      <label class="lb1">Engine</label>
      <select class="form-control engine-status" name="engine1_status">
        <option value="">Select</option>
        <option value="SNEW">New (SNEW)</option>
        <option value="OH">Overhauled (OH)</option>
        <option value="SMOH">Since Major Overhaul (SMOH)</option>
        <option value="STOH">Since Top Overhaul (STOH)</option>
        <option value="FOH">Factory Overhauled (FOH)</option>
        <option value="SBOH">Since Bottom Overhaul (SBOH)</option>
      </select>
    </div>
    <div class="form-group col-md-3">
      <label class="lb2">Engine Hours</label>
      <input type="number" class="form-control engine-hours" name="engine1_hours" placeholder="e.g., 1200">
    </div>
    <div class="form-group col-md-3"  id="prop1-group">
      <label class="lb3">Prop</label>
      <select class="form-control prop-status" name="prop1_status">
        <option value="">Select</option>
        <option value="SNEW">Sense New (SNEW)</option>
        <option value="SOH">Sense Overhauled (SOH)</option>
      </select>
    </div>
    <div class="form-group col-md-3"  id="prop2-group">
      <label class="lb4">Prop Hours</label>
      <input type="number" class="form-control prop-hours" name="prop1_hours" placeholder="e.g., 800">
    </div>
  </div>
</div>

<!-- Multi Engine Block (Only visible when necessary) -->
<div id="multi-engine-block" class="engine-group" style="display:none;">
  <div class="form-row">
    <div class="form-group col-md-3">
      <label>Engine 2</label>
      <select class="form-control engine-status" name="engine2_status">
        <option value="">Select</option>
        <option value="SNEW">New (SNEW)</option>
        <option value="OH">Overhauled (OH)</option>
        <option value="SMOH">Since Major Overhaul (SMOH)</option>
        <option value="STOH">Since Top Overhaul (STOH)</option>
        <option value="FOH">Factory Overhauled (FOH)</option>
        <option value="SBOH">Since Bottom Overhaul (SBOH)</option>
      </select>
    </div>
    <div class="form-group col-md-3">
      <label>Engine 2 Hours</label>
      <input type="number" class="form-control engine-hours" name="engine2_hours" placeholder="e.g., 1300">
    </div>
    <div class="form-group col-md-3" id="prop3-group">
      <label>Prop 2</label>
      <select class="form-control prop-status" name="prop2_status">
        <option value="">Select</option>
        <option value="SNEW">Sense New (SNEW)</option>
        <option value="SOH">Sense Overhauled (SOH)</option>
      </select>
    </div>
    <div class="form-group col-md-3" id="prop4-group">
      <label>Prop 2 Hours</label>
      <input type="number" class="form-control prop-hours" name="prop2_hours" placeholder="e.g., 850">
    </div>
  </div>
</div>



<!-- Helicopter Engine Block (Only visible when necessary) -->
<div id="helicopter-engine-block" class="engine-group" style="display:none;">
  <div class="form-row">
    <div class="form-group col-md-3">
      <label>Engine</label>
      <select class="form-control engine-status" name="enginestatus">
        <option value="">Select</option>
        <option value="SNEW">New (SNEW)</option>
        <option value="OH">Overhauled (OH)</option>
        <option value="SMOH">Since Major Overhaul (SMOH)</option>
        <option value="STOH">Since Top Overhaul (STOH)</option>
        <option value="FOH">Factory Overhauled (FOH)</option>
        <option value="SBOH">Since Bottom Overhaul (SBOH)</option>
      </select>
    </div>
    <div class="form-group col-md-3">
      <label>Engine Hours</label>
      <input type="number" class="form-control engine-hours" name="enginehours" placeholder="e.g., 1300">
    </div>
  </div>
</div>



            <!-- Year -->
            
              <div class="form-group">
                <label>Year</label>
                <input type="number" class="form-control" name="year" required>
              </div>
              <!-- Price -->
             <!-- Price and Price Label -->
    <!-- Price and Price Label -->
<div class="form-row">
  <!-- Price Label Dropdown -->
  <div class="form-group col-md-4">
    <label>Price Label (Optional)</label>
    <select class="form-control" name="price_label" id="price_label">
      <option value="">---Select---</option>
      <option value="sp">Sales Price</option>
      <option value="call">Call for Price</option>
      <option value="obo">OBO</option>
      <option value="starting_bid">Starting Bid</option>
      <option value="make_offer">Make Offer</option>
    </select>
  </div>
  <div class="form-group col-md-4">
    <label>Currency</label>
    <select class="form-control" name="currency" id="currency"></select>
</div>

  <!-- Price Input -->
  <div class="form-group col-md-4">
    <label>Price</label>
    <input type="text" class="form-control" name="price" id="price" placeholder="Enter Price">
  </div>
</div>




              
         
                                    <div class="form-row">
  <div class="form-group col-md-4">
    <label>City</label>
    <input type="text" id="searchBox" class="form-control" name="city" required>
  </div>

  <div class="form-group col-md-4">
    <label>State</label>
    <input type="text" id="stateInput" class="form-control" name="state" required>
  </div>

  <div class="form-group col-md-4">
    <label>Country</label>
    <input type="text" id="countryInput" class="form-control" name="country" required>
  </div>
</div>



             

              <!-- Description -->
              <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" name="description" rows="4" ></textarea>
              </div>

              <!-- Features -->
              <div class="form-group">
                <label>Features</label>
                <textarea class="form-control" name="features" rows="4" ></textarea>
              </div>

              <!-- Warranty -->
              <div class="form-group">
                <label>Warranty</label>
                <textarea class="form-control" name="warranty" rows="4" ></textarea>
              </div>

              <!-- Location (Auto-fetch from API) -->
             

              <!-- Product Images -->
  <div class="form-group">
  <h4>Upload Images</h4>
  <p>You can upload up to 25 images. Click an image to set it as the main image, click again to update it, and drag to rearrange the order.</p>

  <!-- Initial Upload Prompt -->
  <div id="initialUpload" class="drag-drop-area">
    <img src="images/upload.png" alt="Upload Icon">
    <p>Drag and drop files here</p>
    <div class="upload-buttons">
      <button type="button" onclick="resetAndClick()">Upload from computer</button>
      <button type="button" onclick="resetAndClick()">Upload from mobile</button>
    </div>
  </div>

  <!-- Main Upload Interface -->
  <div id="uploadContainer" class="upload-container" style="display: none;">
    <div class="main-preview" id="mainPreview">
      <span>Main Image</span>
    </div>
    <div class="gallery" id="gallery">
      <div class="add-box" id="addBox">add+</div>
    </div>
  </div>

  <!-- Hidden File Input (critical for backend) -->
  <input type="file" id="imageInput" name="images[]" multiple  style="display: none;" accept="image/*">
  <input type="hidden" id="imageCheck" required>
</div>




<div class="form-group">
  <label>Upload Documents (Optional)</label>

  <div class="drag-drop-area" id="document-drop-area" style="border: 2px dashed #ccc; padding: 60px 20px; border-radius: 10px;">
        <img src="images/upload (1).png" style="width:50px;">
    <p>Drag and drop files here</p>
    <div class="upload-buttons">
      <button type="button" class="btn btn-outline-primary" style="color:black;" onclick="document.getElementById('document-input').click()">Upload from computer</button>
    </div>

    <!-- Actual file input -->
    <input type="file" class="form-control" name="documents[]" id="document-input" multiple style="display: none;">
  </div>

  <ul id="document-list" class="mt-2"></ul>
  <p id="document-count" class="text-muted mt-1"></p>

  <small class="form-text text-muted">Upload Aircraft documentation (logbooks, service records, etc.).</small>
</div>




                <!-- Show Call / Email Buttons -->
                
              <p>Listing Contact Options</p>
           <div class="form-row" style="width:80%;margin-left:12px; justify-cntent:space-between;">
               <div class="form-group col-md-4">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="show_seller_name" value="1" id="showSellerName" checked>
      <label class="form-check-label" for="showSellerName">Show Seller Name</label>
    </div>
  </div>
  <div class="form-group col-md-4" style="display:flex;">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="show_call_button" value="1" id="showCall"  checked>
      <label class="form-check-label" for="showCall">Show Call Seller Button</label>
    </div>
  </div>

  <div class="form-group col-md-4">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="show_email_button" value="1" id="showEmail" checked>
      <label class="form-check-label" for="showEmail">Show Email Seller Button</label>
    </div>
  </div>

  
</div>


                
                              <!-- Submit Button -->
              <button type="submit" class="btn" style="background-color:#4747a1;color:white;">Add Aircraft</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  // Format with commas while typing
  $('#price').on('input', function () {
    let value = $(this).val().replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
      $(this).val(parseInt(value).toLocaleString('en-US'));
    } else {
      $(this).val('');
    }
  });

  // Remove commas before form submit
  $('form').on('submit', function () {
    let priceValue = $('#price').val().replace(/,/g, '');
    $('#price').val(priceValue);
  });
</script>


<script>
$(document).ready(function () {
    // When aircraft type is selected
    $("#aircraft_type").change(function () {
        var aircraft_type = $(this).val();
        if (aircraft_type !== "") {
            $.ajax({
                url: "fetch_manufacturers.php",
                method: "POST",
                data: { aircraft_type: aircraft_type },
                dataType: "json",
                success: function (response) {
                    $("#manufacturer").html('<option value="">Select Manufacturer</option>');
                    $.each(response, function (key, value) {
                        $("#manufacturer").append('<option value="' + value + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $("#manufacturer").html('<option value="">Select Manufacturer</option>');
            $("#model").html('<option value="">Select Model</option>');
        }
    });

    // When manufacturer is selected
    $("#manufacturer").change(function () {
        var aircraft_type = $("#aircraft_type").val();
        var manufacturer = $(this).val();
        if (manufacturer !== "") {
            $.ajax({
                url: "fetch_models.php",
                method: "POST",
                data: { aircraft_type: aircraft_type, manufacturer: manufacturer },
                dataType: "json",
                success: function (response) {
                    $("#model").html('<option value="">Select Model</option>');
                    $.each(response, function (key, value) {
                        $("#model").append('<option value="' + value + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $("#model").html('<option value="">Select Model</option>');
        }
    });
});

</script>

<script>
    function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                let latitude = position.coords.latitude;
                let longitude = position.coords.longitude;

                fetch(`get_location.php?lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.location) {
                            document.getElementById("location").value = data.location;
                        }
                    })
                    .catch(error => console.error("Error fetching current location:", error));
            },
            function (error) {
                console.error("Error getting location:", error.message);
            }
        );
    } else {
        console.error("Geolocation is not supported by this browser.");
    }
}

// Fetch locations as the user types
document.getElementById("location").addEventListener("input", debounce(function () {
    let searchQuery = this.value.trim();

    if (searchQuery.length > 2) {
        fetch(`get_locations.php?query=${encodeURIComponent(searchQuery)}`)
            .then(response => response.json())
            .then(data => {
                let locationList = document.getElementById("locationList");
                locationList.innerHTML = "";

                if (data.length === 0) {
                    let noResult = document.createElement("option");
                    noResult.value = "";
                    noResult.textContent = "No results found";
                    locationList.appendChild(noResult);
                } else {
                    data.forEach(location => {
                        let option = document.createElement("option");
                        option.value = location;
                        locationList.appendChild(option);
                    });
                }
            })
            .catch(error => console.error("Error fetching locations:", error));
    }
}, 300)); // 300ms debounce

// Debounce function to reduce API calls
function debounce(func, delay) {
    let timeout;
    return function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, arguments), delay);
    };
}

// Auto-fetch user's location on page load
window.onload = getCurrentLocation;
</script>
<script>
  let autocomplete;

  function initAutocomplete() {
    const input = document.getElementById('searchBox');
    autocomplete = new google.maps.places.Autocomplete(input, {
      types: ['(cities)']
    });

    autocomplete.setFields(['address_component']);
    autocomplete.addListener('place_changed', fillInAddress);
  }

  function fillInAddress() {
    const place = autocomplete.getPlace();
    let city = '', state = '', country = '';

    place.address_components.forEach(component => {
      const types = component.types;
      if (types.includes('locality')) {
        city = component.long_name;
      } else if (types.includes('administrative_area_level_1')) {
        state = component.long_name;
      } else if (types.includes('country')) {
        country = component.long_name;
      }
    });

    document.getElementById('searchBox').value = city;
    document.getElementById('stateInput').value = state;
    document.getElementById('countryInput').value = country;
  }
</script>

<!-- 2. Google Maps API script loaded LAST -->
<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmlHqr1dCDcSciN-_94-i3jUg5P-48j60&libraries=places&callback=initAutocomplete">
</script>
<script>
$(document).ready(function () {
  $('#aircraft_type').change(function () {
    const type = $(this).val().trim();

    // Reset: Hide all dynamic groups
    $('.engine-group').hide();             // hide all engine groups
    $('#engine-fields').hide();            // hide main engine fields (if exists)
    $('#prop1-group').show();              // reset prop1 visibility
    $('#prop2-group').show();              // reset prop2 visibility
    $('#prop3-group').show();              // reset prop2 visibility
    $('#prop4-group').show();              // reset prop2 visibility

    // Show standard engine block
    if ([
      "Single Engine Piston",
      "LSA | Ultralight",
      "Glider | Sailplane",
      "Gyroplane"
    ].includes(type)) {
      $('#engine-fields').show();
        $('.lb1').text('Engine');
      $('.lb2').text('Engine Hours');
      $('.lb3').text('Prop');
      $('.lb4').text('Prop Hours');
    }

    // Show multi-engine + prop2
    else if ([
      "Multi Engine Piston",
      "TurboProp",
      "Balloons | Airships"
    ].includes(type)) {
      $('#engine-fields').show();
      $('#multi-engine-block').show();
      $('.lb1').text('Engine 1');
      $('.lb2').text('Engine 1 Hours');
      $('.lb3').text('Prop 1');
      $('.lb4').text('Prop 1 Hours');
    }

    // Jets: Hide prop1 and prop2
   else if (type === "Jet") {
      $('#engine-fields').show();
      $('#multi-engine-block').show();     // show Engine 2
      $('#prop1-group').hide();            // hide Prop 1
      $('#prop2-group').hide();            // hide Prop 2
      $('#prop3-group').hide();            // hide Prop 2
      $('#prop4-group').hide();            // hide Prop 2
    }

    // Helicopters: Only show helicopter engine block
    else if ([
      "Piston Helicopter",
      "Turbine Helicopter"
    ].includes(type)) {
      $('#helicopter-engine-block').show();
    }
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



<script>
  document.addEventListener('DOMContentLoaded', function () {
    const dropArea = document.getElementById('document-drop-area');
    const fileInput = document.getElementById('document-input');
    const fileList = document.getElementById('document-list');
    const fileCount = document.getElementById('document-count');

    // Highlight on drag
    ['dragenter', 'dragover'].forEach(event => {
      dropArea.addEventListener(event, (e) => {
        e.preventDefault();
        dropArea.style.backgroundColor = '#f8f9fa';
      });
    });

    // Remove highlight
    ['dragleave', 'drop'].forEach(event => {
      dropArea.addEventListener(event, (e) => {
        e.preventDefault();
        dropArea.style.backgroundColor = '';
      });
    });

    // On drop multiple files
    dropArea.addEventListener('drop', (e) => {
      const droppedFiles = e.dataTransfer.files;

      if (droppedFiles.length > 0) {
        const dataTransfer = new DataTransfer();

        // Add previously selected files (from manual file input)
        for (let i = 0; i < fileInput.files.length; i++) {
          dataTransfer.items.add(fileInput.files[i]);
        }

        // Add newly dropped files
        for (let i = 0; i < droppedFiles.length; i++) {
          dataTransfer.items.add(droppedFiles[i]);
        }

        // Update input and UI
        fileInput.files = dataTransfer.files;
        displayFileNames(fileInput.files);
      }
    });

    // Manual file selection
    fileInput.addEventListener('change', () => {
      displayFileNames(fileInput.files);
    });

    function displayFileNames(files) {
      fileList.innerHTML = '';
      for (let i = 0; i < files.length; i++) {
        const li = document.createElement('li');
        li.textContent = files[i].name;
        fileList.appendChild(li);
      }
      fileCount.textContent = `${files.length} file${files.length > 1 ? 's' : ''} uploaded`;
    }
  });
</script>
<script>
  const imageInput = document.getElementById('imageInput');
  const uploadContainer = document.getElementById('uploadContainer');
  const initialUpload = document.getElementById('initialUpload');
  const mainPreview = document.getElementById('mainPreview');
  const gallery = document.getElementById('gallery');
  const addBox = document.getElementById('addBox');
  const imageCheck = document.getElementById('imageCheck');

  let fileList = [];
  let mainImageIndex = null;
  const maxImages = 25;

  // Trigger input when add box clicked
  addBox.addEventListener('click', () => {
    imageInput.value = '';
    imageInput.click();
  });

  // Trigger input when initial upload clicked
  initialUpload.addEventListener('click', () => {
    imageInput.value = '';
    imageInput.click();
  });

  // Handle drag events on initial upload
  initialUpload.addEventListener('dragover', (e) => {
    e.preventDefault();
    initialUpload.classList.add('dragover');
  });

  initialUpload.addEventListener('dragleave', () => {
    initialUpload.classList.remove('dragover');
  });

  initialUpload.addEventListener('drop', (e) => {
    e.preventDefault();
    initialUpload.classList.remove('dragover');
    const droppedFiles = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
    addFiles(droppedFiles);
  });
  // Enable drag & drop in gallery area after initial upload
addBox.addEventListener('dragover', (e) => {
  e.preventDefault();
  uploadContainer.classList.add('dragover');
});

addBox.addEventListener('dragleave', () => {
  uploadContainer.classList.remove('dragover');
});

addBox.addEventListener('drop', (e) => {
  e.preventDefault();
  uploadContainer.classList.remove('dragover');
  const droppedFiles = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
  addFiles(droppedFiles);
});


  imageInput.addEventListener('change', (e) => {
    const selectedFiles = Array.from(e.target.files).filter(f => f.type.startsWith('image/'));
    if (selectedFiles.length > 0) {
      addFiles(selectedFiles);
    }
  });

  function addFiles(newFiles) {
    const availableSlots = maxImages - fileList.length;
    const filesToAdd = newFiles.slice(0, availableSlots);
    fileList = fileList.concat(filesToAdd);
    if (mainImageIndex === null && fileList.length > 0) {
      mainImageIndex = 0;
    }
    render();
  }

  function render() {
    if (fileList.length === 0) {
      initialUpload.style.display = 'block';
      uploadContainer.style.display = 'none';
      return;
    }

    initialUpload.style.display = 'none';
    uploadContainer.style.display = 'flex';

    renderMainImage();
    renderGallery();
    updateFileInput();
  }

  function renderMainImage() {
    if (fileList[mainImageIndex]) {
      const reader = new FileReader();
      reader.onload = (e) => {
        mainPreview.innerHTML = `<img src="${e.target.result}" alt="Main Preview Image">`;
      };
      reader.readAsDataURL(fileList[mainImageIndex]);
    } else {
      mainPreview.innerHTML = `<span>Main Image</span>`;
    }
  }

  function renderGallery() {
    gallery.innerHTML = '';

    fileList.forEach((file, index) => {
      const thumb = document.createElement('div');
      thumb.className = 'thumb';
      thumb.setAttribute('draggable', true);
      thumb.dataset.index = index;

      const img = document.createElement('img');
      const reader = new FileReader();
      reader.onload = (e) => img.src = e.target.result;
      reader.readAsDataURL(file);
      img.alt = `Image ${index + 1}`;
    img.onclick = () => {
  if (index !== 0) {
    const selectedFile = fileList.splice(index, 1)[0]; // remove selected
    fileList.unshift(selectedFile); // insert at 0 index
  }
  mainImageIndex = 0;
  render();
};

      const del = document.createElement('span');
      del.className = 'delete';
      del.textContent = '×';
      del.onclick = (e) => {
        e.stopPropagation();
        fileList.splice(index, 1);
        if (mainImageIndex === index) {
          mainImageIndex = fileList.length > 0 ? 0 : null;
        } else if (mainImageIndex > index) {
          mainImageIndex--;
        }
        render();
      };

      thumb.appendChild(img);
      thumb.appendChild(del);
      enableDragDrop(thumb);
      gallery.appendChild(thumb);
    });

    if (fileList.length < maxImages) {
      gallery.appendChild(addBox);
    }
  }

  function enableDragDrop(element) {
    element.addEventListener('dragstart', (e) => {
      e.dataTransfer.setData('text/plain', e.currentTarget.dataset.index);
    });

    element.addEventListener('dragover', (e) => {
      e.preventDefault();
    });

    element.addEventListener('drop', (e) => {
      e.preventDefault();
      const fromIndex = parseInt(e.dataTransfer.getData('text/plain'));
      const toIndex = parseInt(e.currentTarget.dataset.index);

      if (fromIndex === toIndex) return;

      const [movedFile] = fileList.splice(fromIndex, 1);
      fileList.splice(toIndex, 0, movedFile);

      if (mainImageIndex === fromIndex) {
        mainImageIndex = toIndex;
      } else if (mainImageIndex > fromIndex && mainImageIndex <= toIndex) {
        mainImageIndex--;
      } else if (mainImageIndex < fromIndex && mainImageIndex >= toIndex) {
        mainImageIndex++;
      }

      render();
    });

    element.addEventListener('dragenter', () => {
      element.classList.add('drag-over');
    });

    element.addEventListener('dragleave', () => {
      element.classList.remove('drag-over');
    });

    element.addEventListener('drop', () => {
      element.classList.remove('drag-over');
    });
  }

  function updateFileInput() {
    const dt = new DataTransfer();
    fileList.forEach(file => dt.items.add(file));
    imageInput.files = dt.files;
  }

  // Validate on form submission
  const form = document.querySelector('form');
  form?.addEventListener('submit', function (e) {
    if (fileList.length === 0) {
      imageCheck.setCustomValidity("Please upload at least one image.");
      imageCheck.reportValidity();
      e.preventDefault();
    } else {
      imageCheck.setCustomValidity("");
    }
  });
</script>

<script>
    

document.addEventListener("DOMContentLoaded", function () {
    const currencyDropdown = document.getElementById("currency");

    // Currency symbols mapping
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


    // Add a default option
    currencyDropdown.innerHTML = `<option value="">Loading...</option>`;

    // Fetch currency data
    fetch("https://open.er-api.com/v6/latest/USD")
        .then(response => response.json())
        .then(data => {
            currencyDropdown.innerHTML = ""; // Clear old options

            // Add USD first (default)
            const usdSymbol = currencySymbols["USD"] || "$";
            currencyDropdown.innerHTML += `<option value="${usdSymbol}" selected>USD (${usdSymbol})</option>`;

            // Loop all currencies from API
            Object.keys(data.rates).forEach(currency => {
                if(currency !== "USD") {
                    const symbol = currencySymbols[currency] || currency; // fallback
                    currencyDropdown.innerHTML += `<option value="${symbol}">${currency} (${symbol})</option>`;
                }
            });
        })
        .catch(error => {
            currencyDropdown.innerHTML = `<option value="">Error Loading</option>`;
            console.error("Currency API Error:", error);
        });
});


</script>


<?php include "footer.php"; ?>
   
