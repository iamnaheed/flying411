<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps Search</title>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
        .search-container {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="search-container">
        <input id="searchBox" type="text" placeholder="Search for a location" style="width: 300px; padding: 8px;">
        <button onclick="searchLocation()">Search</button>
    </div>

    <div id="map"></div>

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

</body>
</html>
