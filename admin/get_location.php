<?php
header('Content-Type: application/json');

if (isset($_GET['lat']) && isset($_GET['lon'])) {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];

    // Using OpenStreetMap Nominatim API to get location details
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lon";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['display_name'])) {
        echo json_encode(["location" => $data['display_name']]);
    } else {
        echo json_encode(["location" => "Location not found"]);
    }
} else {
    echo json_encode(["error" => "Invalid coordinates"]);
}
?>
