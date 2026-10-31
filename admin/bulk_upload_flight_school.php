<?php
include "header.php";
// session_start();
include "config.php"; // DB connection

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
   header("Location: ../login.php");
   exit;
}

?>

<head>
<style>
.form-check .form-check-label {
    margin-left: 4px !important;
}

/* Drag & Drop Upload Box */
.drag-drop-area {
    border: 3px dashed #ccc;
    padding: 80px 0;
    text-align: center;
    border-radius: 10px;
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.drag-drop-area.dragover {
    border-color: #4CAF50;
}

.drag-drop-area img {
    width: 70px;
}

/* Buttons */
.upload-buttons {
    margin-top: 20px;
    display: flex;
    gap: 15px;
    justify-content: center;
}

.upload-buttons a,
.upload-buttons button {
    padding: 12px 25px;
    border: 1px solid #4747A1;
    border-radius: 30px;
    background: #4747A1;
    color: white;
    cursor: pointer;
}

.upload-buttons a:hover,
.upload-buttons button:hover {
    opacity: 0.8;
}

.hidden-input {
    display: none;
}
</style>
</head>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="row">
            <div class="col-md-12 grid-margin d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="font-weight-bold">Bulk Upload Services</h3>
                    <h6 class="font-weight-normal mb-0">Select a service type and upload CSV file.</h6>
                </div>
            </div>
        </div>

        <!-- Service Selection -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Select Service Type</h4>

                        <form method="POST" action="bulk_upload_submit.php" enctype="multipart/form-data">

                            <div class="form-group">
                                <label>Service Type</label>
                                <select name="service_type" id="service_type" class="form-control" required>
                                    <option value="">Select Service Type</option>
                                    <option value="flight_school">Flight School</option>
                                    <option value="flight_instructor">Flight Instructor</option>
                                    <option value="engine_shop">Engine Shop</option>
                                    <option value="avionics_shop">Avionics Shop</option>
                                    <option value="maintenance_shop">Maintenance Shop</option>
                                    <option value="local_mechanic">Local Mechanic</option>
                                </select>
                            </div>

                            <!-- Download Format Button -->
                            <div id="download-format" class="upload-buttons" style="display:none;">
                                <a id="download_template" href="#" download>Download CSV Format</a>
                            </div>

                            <!-- Drag & Drop Upload Section -->
                            <div id="upload-section" style="display:none; margin-top:20px;">
                                <h4 class="card-title">Upload CSV File</h4>

                                <label for="csv_file" style="width:100%;">
                                    <div class="drag-drop-area" id="drag-area">
                                        <img src="https://img.icons8.com/pastel-glyph/256/upload.png">
                                        <p>Drag & Drop CSV File Here<br>or Click to Browse</p>

                                        <input type="file" class="hidden-input" name="csv_file" id="csv_file" accept=".csv" required>
                                    </div>
                                </label>

                                <div class="upload-buttons">
                                    <button type="submit">Submit File</button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JS for Dynamic Behavior -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function(){

    $("#service_type").change(function(){
        var selected = $(this).val();

        if(selected !== "") {

            $("#download-format").show();
            $("#upload-section").show();

            // Correct CSV path
            let filePath = "templates/" + selected + "_template.csv";
            $("#download_template").attr("href", filePath);
            $("#download_template").attr("download", selected + "_template.csv");

        } else {
            $("#download-format").hide();
            $("#upload-section").hide();
        }
    });

    // Drag & Drop
    let dragArea = document.getElementById('drag-area');
    let fileInput = document.getElementById('csv_file');

    dragArea.addEventListener('dragover', function(e){
        e.preventDefault();
        dragArea.classList.add('dragover');
    });

    dragArea.addEventListener('dragleave', function(){
        dragArea.classList.remove('dragover');
    });

    dragArea.addEventListener('drop', function(e){
        e.preventDefault();
        dragArea.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
    });

});
</script>



<?php include "footer.php"; ?>
