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

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin d-flex justify-content-between align-items-center">
        <div>
          <h3 class="font-weight-bold">Add Engine</h3>
          <h6 class="font-weight-normal mb-0">Fill in the details below to add a new engine.</h6>
        </div>
        <!-- Bulk Upload Button -->
       <form method="POST" action="submit_engine.php" enctype="multipart/form-data" id="csvForm">
          <input type="file" name="bulk_upload" accept=".csv" hidden id="bulkUploadFile" onchange="showSubmitButton()">
          <button type="button" class="btn btn-success" onclick="document.getElementById('bulkUploadFile').click();">
            Upload CSV
          </button>

          <!-- Submit CSV Button -->
          <button type="submit" class="btn btn-primary" id="submitCSVButton" style="display: none;">Submit CSV</button>
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
             <div class="form-group">
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
<div class="form-group">
    <label>Engine Model</label>
    <select class="form-control" id="engine_model" name="engine_model" required>
        <option value="">Select Engine Model</option>
    </select>
</div>

<!-- Auto-filled Fields -->
<div class="form-group">
    <label>Engine Type</label>
    <input type="text" class="form-control" id="engine_type" name="engine_type" readonly required>
</div>
<div class="form-group">
    <label>Power/Thrust</label>
    <input type="text" class="form-control" id="power_thrust" name="power_thrust" readonly required>
</div>


              <!-- Additional Data Fields -->
              
              <div class="form-group">
                <label>Total Time (Hours)</label>
                <input type="number" class="form-control" name="total_time_hours">
              </div>
              <div class="form-group">
                <label>Total Cycles</label>
                <input type="number" class="form-control" name="cycles">
              </div>
              <div class="form-group">
                <label>Hours Remaining</label>
                <input type="number" class="form-control" name="hr">
              </div>
              <div class="form-group">
                <label>Cycles Remaining</label>
                <input type="number" class="form-control" name="cycles">
              </div>
              <div class="form-group">
                <label>Condition</label>
                <select class="form-control" name="condition" required>
                  <option value="Factory New">FN (Factory New)</option>
                  <option value="Overhauled">OH (Overhauled)</option>
                  <option value="Serviceable">SV (Serviceable)</option>
                  <option value="As Removed">AR (As Removed)</option>
                  <option value="New Surplus">NS (New Surplus)</option>
                  <option value="Repaired">RP (Repaired)</option>
                  <option value="Beyond Economic Repair">BER (Beyond Economic Repair)</option>
                </select>
              </div>
              <div class="form-group">
                <label>Year</label>
                <input type="number" class="form-control" name="year" required>
              </div>
              <div class="form-group">
                <label>Price</label>
                <input type="number" class="form-control" name="price" required>
              </div>
               <div class="form-group">
                    <label>Tags</label>
                    <input type="text" class="form-control" name="tags" placeholder="Enter tags, separated by commas">
                </div>
               <div class="form-group">
                <label>Location</label>
                 <input type="text" class="form-control" name="location" required>
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
                <label>Uploads Engine Images</label>
                <input type="file" class="form-control" name="images[]" multiple required>
              </div>
                <div class="form-group">
                        <label>Upload Documents</label>
                        <input type="file" class="form-control" name="documents[]" multiple>
                        <small class="form-text text-muted">Upload engine documentation (logbooks, service records, etc.).</small>
                    </div>
              <!-- Submit Button -->
              <button type="submit" class="btn btn-primary">Add Engine</button>
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
  // Function to show the "Submit CSV" button when a file is uploaded
  function showSubmitButton() {
    let fileInput = document.getElementById('bulkUploadFile');
    let submitCSVButton = document.getElementById('submitCSVButton');
    
    // Show the submit button if a file is selected
    if (fileInput.files.length > 0) {
        submitCSVButton.style.display = 'inline-block';  // Show the button
    } else {
        submitCSVButton.style.display = 'none';  // Hide the button if no file is selected
    }
  }
</script>