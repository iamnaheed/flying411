<?php
session_start();
require 'config.php'; // Database connection via PDO

// Check vendor authentication
if (!isset($_SESSION['vendor_id'])) {
     header("Location: ../login.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

include "header.php";
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin d-flex justify-content-between align-items-center">
        <div>
          <h3 class="font-weight-bold">Add Service</h3>
          <h6 class="font-weight-normal mb-0">Fill in the details below to add a new service.</h6>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Service Details</h4>
            <form method="POST" action="add_service.php" enctype="multipart/form-data">
              <input type="hidden" name="vendor_id" value="<?= htmlspecialchars($vendor_id) ?>">
              
              <div class="form-group">
                <label>Service Type</label>
                <select name="service_type" id="service_type" class="form-control" required>
                  <option value="">Select Service Type</option>
                  <option value="Flight Instructor">Flight Instructor</option>
                  <option value="Flight School">Flight School</option>
                  <option value="Engine Shop">Engine Shop</option>
                  <option value="Avionics Shop">Avionics Shop</option>
                  <option value="Maintenance Shop">Maintenance Shop</option>
                  <option value="Local Mechanic">Local Mechanic</option>
                </select>
              </div>
              
              <div id="dynamic-form"></div>
              
              <button type="submit" class="btn btn-primary">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  $(document).ready(function() {
    $('#service_type').change(function() {
      var selectedService = $(this).val();
      $.ajax({
        url: 'service_form_loader.php',
        method: 'POST',
        data: { service_type: selectedService },
        success: function(response) {
          $('#dynamic-form').html(response);
        }
      });
    });
  });
</script>

<?php include "footer.php"; ?>
