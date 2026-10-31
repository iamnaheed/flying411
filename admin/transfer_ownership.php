<?php
session_start();
include "config.php"; // PDO connection


if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

include "header.php";

// Fetch vendors using PDO
try {
    $stmt = $pdo->prepare("SELECT vendor_id, business_name FROM vendors WHERE user_role = 'vendor' ORDER BY business_name ASC");
    $stmt->execute();
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $vendors = [];
}
if (isset($_SESSION['message'])):
    $msg = $_SESSION['message'];
    $alert_class = ($msg['type'] === 'success') ? 'alert-success' : 'alert-danger';
?>
<div id="popupMessage" class="popup-message <?= $alert_class ?>">
    <?= htmlspecialchars($msg['text']) ?>
</div>
<?php
    unset($_SESSION['message']);
endif;
?>
<style>
    .popup-message {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999;
    padding: 20px 30px;
    border-radius: 10px;
    font-size: 18px;
    color: #fff;
    min-width: 300px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    opacity: 0.95;
}

.alert-success {
    background-color: #28a745;
}

.alert-danger {
    background-color: #dc3545;
}

</style>
<div class="main-panel">
    <div class="content-wrapper">

        <h3 class="font-weight-bold mb-4">Transfer Ownership</h3>

        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Transfer Service Ownership</h4>

                <form method="POST" action="transfer_ownership_submit.php">

                    <!-- Service Type -->
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

                    <!-- Search by Name -->
                    <div class="form-group">
                        <label>Search Service by Name</label>
                        <input type="text" id="search_query" class="form-control" placeholder="Search...">
                    </div>

                    <!-- Services Dropdown -->
                    <div class="form-group">
                        <label>Select Service</label>
                        <select name="service_id" id="service_list" class="form-control" required>
                            <option value="">Select service</option>
                        </select>
                    </div>

                    <!-- Vendors Dropdown -->
                    <div class="form-group">
                        <label>Select Vendor</label>
                        <select name="new_vendor_id" class="form-control" required>
                            <option value="">Select Vendor</option>
                            <?php foreach ($vendors as $v): ?>
                                <option value="<?= htmlspecialchars($v['vendor_id']) ?>">
                                    <?= htmlspecialchars($v['business_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Transfer Ownership</button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function(){

    function loadServices() {
        let type = $("#service_type").val();
        let search = $("#search_query").val();

        if(type !== "") {
            $.ajax({
                url: "fetch_services_by_type.php",
                type: "POST",
                data: { service_type: type, search: search },
                success: function(response){
                    $("#service_list").html(response);
                },
                error: function() {
                    $("#service_list").html('<option value="">Error loading services</option>');
                }
            });
        } else {
            $("#service_list").html('<option value="">Select service</option>');
        }
    }

    $("#service_type").change(loadServices);
    $("#search_query").keyup(loadServices);

});
</script>
<script>
$(document).ready(function() {
    setTimeout(function() {
        $('#popupMessage').fadeOut('slow');
    }, 3000);
});
</script>


<?php include "footer.php"; ?>
