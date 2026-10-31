
<?php


// API URL to fetch countries and states
$api_url = "https://countriesnow.space/api/v0.1/countries/states";

// Fetch data from API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Decode JSON response
$data = json_decode($response, true);

if ($data && isset($data['data'])) {
    $countries = $data['data'];
} else {
    $countries = [];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_type'])) {
    $service_type = $_POST['service_type'];

    switch ($service_type) {
        case "Flight Instructor":
            echo '<div class="form-group">
                    <label>Flight Instruction Rate</label>
                    <input type="text" name="instruction_rate" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Ground Instruction Rate</label>
                    <input type="text" name="ground_rate" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Flight Instructor Ratings</label><br>
                    <input type="checkbox" name="ratings[]" value="CFI"> CFI 
                    <input type="checkbox" name="ratings[]" value="CFII"> CFII 
                    <input type="checkbox" name="ratings[]" value="MEI"> MEI 
                  </div>
                  <div class="form-group">
                    <label>Aircraft Available For Instruction</label>
                    <select name="aircraft_available" class="form-control">
                      <option value="Yes">Yes</option>
                      <option value="No">No</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Instructor Photos</label>
                    <input type="file" name="photos[]" class="form-control">
                  </div>';
            break;

        case "Flight School":
            echo '<div class="form-group">
                    <label>Flight Instruction Rate</label>
                    <input type="text" name="instruction_rate" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Ground Instruction Rate</label>
                    <input type="text" name="ground_rate" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Instruction Offered</label><br>
                    <input type="checkbox" name="instruction_offered[]" value="Private"> Private
                    <input type="checkbox" name="instruction_offered[]" value="Instrument"> Instrument
                    <input type="checkbox" name="instruction_offered[]" value="Commercial"> Commercial
                  </div>
                  <div class="form-group">
                    <label>Aircraft Available</label>
                    <input type="text" name="aircraft_available" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>School Photos</label>
                    <input type="file" name="photos[]" class="form-control">
                  </div>';
            break;

        case "Engine Shop":
            echo '<div class="form-group">
                    <label>Engine Shop Type</label><br>
                    <input type="checkbox" name="shop_type[]" value="Small Engine"> Small Engine 
                    <input type="checkbox" name="shop_type[]" value="Turbine Engine"> Turbine Engine
                  </div>
                  <div class="form-group">
                    <label>Hourly Rate</label>
                    <input type="text" name="hourly_rate" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Shop Photos</label>
                    <input type="file"name="photos[]" class="form-control">
                  </div>';
            break;

        case "Avionics Shop":
            echo '<div class="form-group">
                    <label>Hourly Rate</label>
                    <input type="text" name="hourly_rate" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Shop Photos</label>
                    <input type="file"name="photos[]" class="form-control">
                  </div>';
            break;

        case "Maintenance Shop":
            echo '
                  <div class="form-group">
                    <label>Hourly Rate</label>
                    <input type="text" name="hourly_rate" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Shop Photos</label>
                    <input type="file"name="photos[]" class="form-control">
                  </div>';
            break;

        case "Local Mechanic":
            echo '<div class="form-group">
                    <label>Hourly Rate</label>
                    <input type="text" name="hourly_rate" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Mechanic Ratings</label><br>
                    <input type="checkbox" name="mechanic_ratings[]" value="A"> A 
                    <input type="checkbox" name="mechanic_ratings[]" value="P"> P 
                    <input type="checkbox" name="mechanic_ratings[]" value="A&P"> A&P 
                    <input type="checkbox" name="mechanic_ratings[]" value="IA"> IA 
                  </div>
                  <div class="form-group">
                    <label>Mechanic Photos</label>
                    <input type="file" name="photos[]" class="form-control">
                  </div>';
            break;
    }

 echo '<div class="form-group">
        <label for="country">Location:</label>
        <input id="searchBox" name="country" type="text" placeholder="Search for a location" style="width: 100%; padding: 8px; border:1px solid gray">
      </div>
      <div id="map"> </div>';

echo '     </select>
      </div>
      <div class="form-group">
          <label>Phone Number</label>
          <input type="text" name="phone_number" class="form-control">
      </div>
      <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" class="form-control">
      </div>
      <div class="form-group">
          <label>Website</label>
          <input type="text" name="website" class="form-control">
      </div>';
}
?>

<script>
        let map, marker, autocomplete;

        function initMap() {
            const defaultLocation = { lat: -37.8162797, lng: 144.9537353 };

            map = new google.maps.Map(document.getElementById("map"), {
                center: defaultLocation,
                zoom: 10
            });

            marker = new google.maps.Marker({
                position: defaultLocation,
                map: map
            });

            const searchBox = document.getElementById("searchBox");
            if (searchBox) {
                autocomplete = new google.maps.places.Autocomplete(searchBox);
                autocomplete.addListener("place_changed", searchLocation);
            } else {
                console.error("Search input not found.");
            }
        }

        function searchLocation() {
            const place = autocomplete.getPlace();
            if (!place || !place.geometry) {
                alert("Please select a valid location.");
                return;
            }

            map.setCenter(place.geometry.location);
            map.setZoom(12);
            marker.setPosition(place.geometry.location);
        }
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAmlHqr1dCDcSciN-_94-i3jUg5P-48j60&libraries=places&callback=initMap"></script>