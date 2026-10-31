<?php
session_start();
include "header.php";
include "config.php"; // Ensure this file properly initializes PDO

// Check vendor authentication
if (!isset($_SESSION['vendor_id'])) {
   header("Location: ../login.php");
    exit;
}
$vendor_id = $_SESSION['vendor_id'];
?>
<head>
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
<body>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin d-flex justify-content-between align-items-center">
        <div>
          <h3 class="font-weight-bold">Add Engine</h3>
          <h6 class="font-weight-normal mb-0">Fill in the details below to add a new engine.</h6>
        </div>
        <!-- Bulk Upload Button -->
        <form method="POST" action="submit_csvengines.php" enctype="multipart/form-data" id="engineCSVForm">
    <!-- Hidden file input -->
    <input type="file" name="bulk_upload" accept=".csv" hidden id="bulkUploadFile" onchange="csvSelected()">

    <!-- Upload CSV Button -->
    <button type="button" class="btn btn-success" onclick="document.getElementById('bulkUploadFile').click();">
      <i class="fa fa-upload"></i> Upload CSV
    </button>

    <!-- Submit CSV Button -->
    <button type="button" class="btn" style="background-color:#4747A1;color:white;" id="submitCSVButton" style="display: none;" onclick="submitCSV()">
      <i class="fa fa-paper-plane"></i> Submit CSV
    </button>

    <!-- Download Format Button -->
    <button type="button" class="btn btn-info" onclick="downloadCSVFormat()">
      <i class="fa fa-download"></i> Download CSV Format
    </button>
  </form>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Engine Details</h4>
            <form id="engineForm" method="POST" action="submit_engine.php" enctype="multipart/form-data">
              <input type="hidden" name="vendor_id" value="<?= htmlspecialchars($vendor_id) ?>">

              <!-- Engine Model Selection -->
             <div class="form-row">
             <div class="form-group col-md-3">
    <label>Manufacturer</label>
    <select class="form-control" id="manufacturer" name="manufacturer" required>
        <option value="">Select Manufacturer</option>
        <?php
        $query = "SELECT DISTINCT manufacturer FROM engines_details";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='".htmlspecialchars($row['manufacturer'])."'>".htmlspecialchars($row['manufacturer'])."</option>";
        }
        ?>
    </select>
</div>

<!-- Engine Model Selection (Dynamically Loaded) -->
<div class="form-group col-md-3">
    <label>Engine Model</label>
    <select class="form-control" id="engine_model" name="engine_model" required>
        <option value="">Select Engine Model</option>
    </select>
</div>

<!-- Auto-filled Fields -->
<div class="form-group col-md-3">
    <label>Engine Type</label>
    <input type="text" class="form-control" id="engine_type" name="engine_type" readonly required>
</div>
<div class="form-group col-md-3">
    <label>Power/Thrust</label>
    <input type="text" class="form-control" id="power_thrust" name="power_thrust" readonly required>
</div>
</div>


              <!-- Additional Data Fields -->
              <div class="form-row">
              <div class="form-group col-md-3">
                <label>Total Time (Hours)</label>
                <input type="number" class="form-control" name="total_time_hours">
              </div>
              <div class="form-group col-md-3">
                <label>Hours Remaining</label>
                <input type="number" class="form-control" name="hr" >
              </div>
              <div class="form-group col-md-3">
                <label>Total Cycles</label>
                <input type="number" class="form-control" name="cycles">
              </div>
              
              <div class="form-group col-md-3">
                <label>Cycles Remaining</label>
                <input type="number" class="form-control" name="cycles_remaining">
              </div>
              </div>
              <div class="form-row">
              <div class="form-group col-md-3">
                <label>Condition</label>
                <select class="form-control" name="condition" >
                  <option value="Factory New">FN (Factory New)</option>
                  <option value="Overhauled">OH (Overhauled)</option>
                  <option value="Serviceable">SV (Serviceable)</option>
                  <option value="As Removed">AR (As Removed)</option>
                  <option value="New Surplus">NS (New Surplus)</option>
                  <option value="Repaired">RP (Repaired)</option>
                  <option value="Beyond Economic Repair">BER (Beyond Economic Repair)</option>
                </select>
              </div>
              <div class="form-group col-md-3">
                <label>Year</label>
                <input type="number" class="form-control" name="year" >
              </div>
              <div class="form-group col-md-3">
                        <label>Sale Type</label>
                        <select class="form-control" name="sale_type" id="saleTypeField" >
                            <option value="">--- Select Sale Type ---</option>
                            <option value="outright">Outright</option>
                            <option value="exchange">Exchange</option>
                        </select>
                        
                    </div>
                     <div class="form-group col-md-3">
                        <label>Tag Type</label>
                        <select class="form-control" name="tags">
                            <option value="8130/EASA Form 1">Dual Release (8130/EASA Form 1)</option>
                            <option value="8130">8130</option>
                            <option value="EASA Form 1">EASA Form 1</option>
                            <option value="Yellow Tag">Yellow Tag</option>
                            <option value="No Tag">No Tag</option>
                        </select>
                    </div>
                
                </div>
              
              <div class="form-row">
                  <div class="form-group col-md-3">
                        <label>Price Label (Optional)</label>
                        <select class="form-control" name="price_label" id="price_label">
                          <option value="">----Select---</option>
                          <option value="call">Call for Price</option>
                          <option value="sp">Sale Price</option>
                        </select>
                      </div> 
                   <div class="form-group col-md-3">
                    <label>Currency</label>
                    <select class="form-control" name="currency" id="currency"></select>
                </div>
              <div class="form-group col-md-3">
                <label>Price</label>
                <input type="text" class="form-control" name="price" id="price" placeholder="Enter Price" >
              </div>
              <div class="form-group col-md-3">
                    <label>Country</label>
                    <input type="text" id="countryInput" class="form-control" name="country" >
                 </div>
              </div>
              
  

  
              <div class="form-group">
                <label>Details</label>
                <textarea class="form-control" name="extra_details" rows="4"></textarea>
              </div>
              <div class="form-group">
                <label>Warranty</label>
                <textarea class="form-control" name="warranty" rows="4"></textarea>
              </div>

              <!-- Engine Image Upload -->
              <!-- Product Images -->
              <div class="form-group">
  <h4>Upload  Engine Images</h4>
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
  <label>Upload Engine Documents (Optional)</label>

  <div class="drag-drop-area" id="document-drop-area" style="border: 2px dashed #ccc; padding: 20px; border-radius: 10px;">
       <img src="images/upload (1).png" alt="Upload Icon">
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
              <!-- Submit Button -->
              <button type="submit" class="btn " style="background-color:#4747A1;color:white;">Add Engine</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


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
    const input = document.getElementById('countryInput');

    autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['country']
    });

    autocomplete.setFields(['address_component']);

    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();
        let country = '';

        place.address_components.forEach(component => {
            if (component.types.includes('country')) {
                country = component.long_name;
            }
        });

        // Set only country value
        input.value = country;
    });
}
</script>
<!-- 2. Google Maps API script loaded LAST -->
<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmlHqr1dCDcSciN-_94-i3jUg5P-48j60&libraries=places&callback=initAutocomplete">
</script>
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
});
</script>

<script>
  const dropArea = document.getElementById('dropArea');
  const fileInput = document.getElementById('imageInput');
  const fileCount = document.getElementById('fileCount');

  // Update file count display
  function updateFileCount() {
    const count = fileInput.files.length;
    fileCount.innerText = `${count}/25`;
  }

  // Drag & drop events
  dropArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropArea.classList.add('dragover');
  });

  dropArea.addEventListener('dragleave', () => {
    dropArea.classList.remove('dragover');
  });

  dropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dropArea.classList.remove('dragover');

    const files = e.dataTransfer.files;
    const dataTransfer = new DataTransfer();
    
    // Combine existing and new files
    for (let i = 0; i < fileInput.files.length; i++) {
      dataTransfer.items.add(fileInput.files[i]);
    }
    for (let i = 0; i < files.length; i++) {
      dataTransfer.items.add(files[i]);
    }

    fileInput.files = dataTransfer.files;
    updateFileCount();
  });

  // Listen to file input change
  fileInput.addEventListener('change', updateFileCount);
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
document.addEventListener("DOMContentLoaded", function() {
    const fileInput = document.getElementById('bulkUploadFile');
    const submitButton = document.getElementById('submitCSVButton');

    // When a CSV file is selected
    window.csvSelected = function() {
        if (fileInput.files.length > 0) {
            console.log("CSV selected:", fileInput.files[0].name);
            submitButton.style.display = 'inline-block';
        } else {
            submitButton.style.display = 'none';
        }
    }

    // Submit CSV to PHP backend
    window.submitCSV = function() {
        if (fileInput.files.length === 0) {
            alert("⚠️ Please select a CSV file first.");
            return;
        }

        const formData = new FormData();
        formData.append('bulk_upload', fileInput.files[0]);

        console.log("Uploading engine CSV:", fileInput.files[0].name);

        fetch('submit_csvengines.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log("Server response:", data);
            alert("✅ Engine CSV uploaded successfully!");
            fileInput.value = '';
            submitButton.style.display = 'none';
        })
        .catch(error => {
            console.error("Error:", error);
            alert("❌ Error uploading CSV. Please try again.");
        });
    }

    // Download CSV Format File
    window.downloadCSVFormat = function() {
        const csvUrl = "uploads/CSVFormate/engine_format.csv"; // Update path if needed
        const link = document.createElement("a");
        link.href = csvUrl;
        link.download = "engine_format.csv";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
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
</body>
