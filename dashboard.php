<?php
include_once('templates/header.php');
include_once('templates/navbar.php');
include_once('templates/sidebar.php');
?>

<div class="content-wrapper">
    <div class="row m-4 card">
        <div class="col">
            <h3 class="m-auto"> Vests </h3>
            <ul>
                <li>Vest 1</li>
            </ul>
        </div>

        <div class="col">
            <div id="map" style="height: 400px;"></div>
        </div>
    </div>
</div>

<script>
    var map = L.map('map').setView([51.505, -0.09], 13); // Initial map setup

    // Add OpenStreetMap layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'powered by <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = null; // Store marker reference
    var lastLat = null,
        lastLng = null; // Store last known location
    var userZoomLevel = map.getZoom(); // Store user-defined zoom level

    // Detect user zoom changes
    map.on('zoomend', function() {
        userZoomLevel = map.getZoom(); // Save zoom level when user changes it
    });

    function get_location() {
        $.ajax({
            url: 'http://localhost/Geovest/server/location_get.php',
            method: 'POST',
            data: {
                vest_num: 1
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    var lat = parseFloat(response.data[0].latitude);
                    var lng = parseFloat(response.data[0].longitude);

                    if (isNaN(lat) || isNaN(lng)) {
                        console.warn("Invalid coordinates received:", lat, lng);
                        return;
                    }

                    // If marker doesn't exist, create it
                    if (!marker) {
                        marker = L.marker([lat, lng]).addTo(map)
                            .bindPopup("Current Location").openPopup();
                    } else {
                        // Smoothly move marker instead of re-adding
                        marker.setLatLng([lat, lng]);
                    }

                    // Only move the map if location changed significantly
                    if (lastLat === null || lastLng === null || Math.abs(lastLat - lat) > 0.0001 || Math.abs(lastLng - lng) > 0.0001) {
                        map.setView([lat, lng], userZoomLevel); // Preserve user zoom
                    }

                    lastLat = lat;
                    lastLng = lng;
                } else {
                    console.error("Error:", response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", xhr.responseText);
            }
        });
    }

    // Fetch location every 5 seconds
    setInterval(get_location, 5000);

    // Initial fetch
    get_location();
</script>



<?php
include_once('templates/footer.php');
?>