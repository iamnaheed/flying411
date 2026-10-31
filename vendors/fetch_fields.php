<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_type'])) {
    $service_type = $_POST['service_type'];

    switch ($service_type) {
        case "Flight Instructor":
            echo '<div class="form-group"><label>Instruction Rate</label><input type="text" name="instruction_rate" class="form-control"></div>';
            break;
        case "Flight School":
            echo '<div class="form-group"><label>Aircraft Available</label><input type="text" name="aircraft_available" class="form-control"></div>';
            break;
        case "Engine Shop":
            echo '<div class="form-group"><label>Hourly Rate</label><input type="text" name="hourly_rate" class="form-control"></div>';
            break;
        case "Avionics Shop":
            echo '<div class="form-group"><label>Shop Services</label><input type="text" name="shop_services" class="form-control"></div>';
            break;
        default:
            echo '';
    }
}
?>
