<?php
include 'config.php';

$service_type = $_POST['service_type'] ?? '';
$service_id = $_POST['service_id'] ?? null;

// Initialize existing values
$existing = [
    'instruction_rate' => '',
    'ground_rate' => '',
    'hourly_rate' => '',
    'aircraft_available' => '',
    'shop_type' => [],
    'mechanic_ratings' => [],
    'instruction_offered' => [],
    'ratings' => [],
    'school_type' => '',
    'certificate_number' => ''
];

// Fetch existing service data if editing
if ($service_id) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id=:id");
    $stmt->execute([':id'=>$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($service) {
        $existing['instruction_rate'] = $service['instruction_rate'];
        $existing['ground_rate'] = $service['ground_rate'];
        $existing['hourly_rate'] = $service['hourly_rate'];
        $existing['aircraft_available'] = $service['aircraft_available'];
        $existing['shop_type'] = json_decode($service['shop_type'], true) ?: [];
        $existing['mechanic_ratings'] = json_decode($service['mechanic_ratings'], true) ?: [];
        $existing['instruction_offered'] = json_decode($service['instruction_offered'], true) ?: [];
        $existing['ratings'] = json_decode($service['ratings'], true) ?: [];
        $existing['school_type'] = $service['school_type'] ?? '';
        $existing['certificate_number'] = $service['certificate_number'] ?? '';
    }
}

// Generate fields based on service type
switch($service_type) {
    case "Flight Instructor":
        ?>
        <div class="form-group">
            <label>Flight Instruction Rate</label>
            <input type="text" name="instruction_rate" class="form-control" value="<?= htmlspecialchars($existing['instruction_rate']) ?>">
        </div>
        <div class="form-group">
            <label>Ground Instruction Rate</label>
            <input type="text" name="ground_rate" class="form-control" value="<?= htmlspecialchars($existing['ground_rate']) ?>">
        </div>
        <div class="form-group">
            <label>Flight Instructor Ratings</label><br>
            <?php
            $all_ratings = ['CFI','CFII','MEI'];
            foreach($all_ratings as $r){
                $checked = in_array($r, $existing['ratings']) ? 'checked' : '';
                echo "<input type='checkbox' name='ratings[]' value='$r' $checked> $r ";
            }
            ?>
        </div>
        <div class="form-group">
            <label>Aircraft Available For Instruction</label>
            <select name="aircraft_available" class="form-control">
                <option value="Yes" <?= ($existing['aircraft_available']=='Yes')?'selected':'' ?>>Yes</option>
                <option value="No" <?= ($existing['aircraft_available']=='No')?'selected':'' ?>>No</option>
            </select>
        </div>
        <?php
        break;

    case "Flight School":
        ?>
        <div class="form-group">
            <label>Flight Instruction Rate</label>
            <input type="text" name="instruction_rate" class="form-control" value="<?= htmlspecialchars($existing['instruction_rate']) ?>">
        </div>
        <div class="form-group">
            <label>Ground Instruction Rate</label>
            <input type="text" name="ground_rate" class="form-control" value="<?= htmlspecialchars($existing['ground_rate']) ?>">
        </div>
        <div class="form-group">
            <label>Instruction Offered</label><br>
            <?php
            $all_instr = ['Private','Instrument','Commercial'];
            foreach($all_instr as $i){
                $checked = in_array($i, $existing['instruction_offered']) ? 'checked' : '';
                echo "<input type='checkbox' name='instruction_offered[]' value='$i' $checked> $i ";
            }
            ?>
        </div>
        <div class="form-group">
            <label>Aircraft Available</label>
            <input type="text" name="aircraft_available" class="form-control" value="<?= htmlspecialchars($existing['aircraft_available']) ?>">
        </div>
        <div class="form-group">
            <label>School Type</label>
            <select name="school_type" class="form-control">
                <option value="">Select School Type</option>
                <option value="Part 61" <?= ($existing['school_type']=='Part 61')?'selected':'' ?>>Part 61</option>
                <option value="Part 141" <?= ($existing['school_type']=='Part 141')?'selected':'' ?>>Part 141</option>
            </select>
        </div>
        <div class="form-group">
            <label>Certificate Number</label>
            <input type="text" name="certificate_number" class="form-control" value="<?= htmlspecialchars($existing['certificate_number']) ?>">
        </div>
        <?php
        break;

    case "Engine Shop":
        ?>
        <div class="form-group">
            <label>Engine Shop Type</label><br>
            <?php
            $all_shops = ['Small Engine','Turbine Engine'];
            foreach($all_shops as $s){
                $checked = in_array($s, $existing['shop_type']) ? 'checked' : '';
                echo "<input type='checkbox' name='shop_type[]' value='$s' $checked> $s ";
            }
            ?>
        </div>
        <div class="form-group">
            <label>Hourly Rate</label>
            <input type="text" name="hourly_rate" class="form-control" value="<?= htmlspecialchars($existing['hourly_rate']) ?>">
        </div>
        <?php
        break;

    case "Avionics Shop":
    case "Maintenance Shop":
        ?>
        <div class="form-group">
            <label>Hourly Rate</label>
            <input type="text" name="hourly_rate" class="form-control" value="<?= htmlspecialchars($existing['hourly_rate']) ?>">
        </div>
        <?php
        break;

    case "Local Mechanic":
        ?>
        <div class="form-group">
            <label>Hourly Rate</label>
            <input type="text" name="hourly_rate" class="form-control" value="<?= htmlspecialchars($existing['hourly_rate']) ?>">
        </div>
        <div class="form-group">
            <label>Mechanic Ratings</label><br>
            <?php
            $all_mech = ['A','P','A&P','IA'];
            foreach($all_mech as $m){
                $checked = in_array($m, $existing['mechanic_ratings']) ? 'checked' : '';
                echo "<input type='checkbox' name='mechanic_ratings[]' value='$m' $checked> $m ";
            }
            ?>
        </div>
        <?php
        break;
}
?>
