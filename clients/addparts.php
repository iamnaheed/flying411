<?php
    session_start();
    include "header.php";
    include "config.php";
    
    if (!isset($_SESSION['vendor_id'])) {
        header("Location: login.php");
        exit;
    }
    $vendor_id = $_SESSION['vendor_id'];
    ?>

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="font-weight-bold">Add Part</h3>
                        <h6 class="font-weight-normal mb-0">Fill in the details below to add a new part.</h6>
                    </div>
                    <form method="POST" action="submit_part.php" enctype="multipart/form-data" id="csvForm">
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
                            <h4 class="card-title">Part Details</h4>
                            <form id="partForm" method="POST" action="submit_part.php" enctype="multipart/form-data">
                                <input type="hidden" name="vendor_id" value="<?= htmlspecialchars($vendor_id) ?>">
                                 
                                  <div class="form-group">
                                    <label>Part Name</label>
                                    <input type="text" class="form-control" name="part_name" required>
                                </div>
                                <div class="form-group">
                                    <label>Part Number</label>
                                    <input type="text" class="form-control" name="part_number" required>
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
                                    <label>Price</label>
                                    <input type="number" class="form-control" name="price" required>
                                </div>

                                <div class="form-group">
                                    <label>Tagged with EASA Form 1</label>
                                    <select class="form-control" name="tagged_with_easa_form_1" required>
                                        <option value="No Tag">No Tag</option>
                                        <option value="8130">8130</option>
                                        <option value="EASA Form 1">EASA Form 1</option>
                                        <option value="Dual Release 8130/EASA Form 1">Dual Release 8130/EASA Form 1</option>
                                    </select>
                                </div>
                                  <div class="form-group">
                                    <label>Location</label>
                                    <input type="text" class="form-control" name="region" required>
                                </div>
                                <div class="form-group">
                                    <label>Details</label>
                                    <textarea class="form-control" name="extra_details" rows="4"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Warranty</label>
                                    <textarea class="form-control" name="warranty" rows="4"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Upload Parts Images</label>
                                    <input type="file" class="form-control" name="images[]" multiple required>
                                </div>
                                <div class="form-group">
                                    <label>Upload Parts Documents</label>
                                    <input type="file" class="form-control" name="documents[]" multiple required>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Part</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "footer.php"; ?>
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
</body>
</html>

