<?php
include_once('templates/header.php');
include_once('templates/navbar.php');
include_once('templates/sidebar.php');
?>
<style>
    ul,
    li {
        list-style-type: none;
        text-decoration: none;
    }
</style>
<div class="content-wrapper">
    <div class="row m-4 card">
        <div class="col">
            <h3 class="m-auto"> Vests </h3>

            <ul style="list-style-type: type none;">
                <li> <img src="resources/imgs/vest_icon.png" alt="" style="width: 50px; height:50px;"> Vest 1 <br /> <img src="resources/imgs/heartbeat.gif" style="width:50px;height:50px;" /> Heart Rate: <span id="vest_heartrate"> </span> </li>
            </ul>
        </div>

        <div class="col">
            <div id="map" style="height: 400px;"></div>
        </div>
    </div>
</div>

<script>
    var map = L.map('map', {
        fullscreenControl: true
    }).setView([51.505, -0.09], 5); // High zoom level

    // 🗺️ Base Layers
    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'OpenStreetMap contributors',
        maxZoom: 24
    }).addTo(map);

    var satellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: 'Google Satellite',
        maxZoom: 24
    });

    var terrain = L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: 'Google Terrain',
        maxZoom: 24
    });

    var cartoDark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: 'CartoDB Dark Mode',
        maxZoom: 24
    });

    var esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'ESRI Satellite',
        maxZoom: 24
    });

    // 📌 Overlays
    var markerLayer = L.layerGroup().addTo(map); // ✅ Always visible for tracking markers

    var polygonLayer = L.polygon([
        [51.51, -0.12],
        [51.51, -0.08],
        [51.505, -0.06]
    ], {
        color: 'red'
    });

    var circleMarkerLayer = L.circleMarker([51.505, -0.09], {
        color: 'blue',
        radius: 10
    }).bindPopup("Circle Marker");

    // ➰ Route Polyline Example
    var routePolyline = L.polyline([
        [51.505, -0.09],
        [51.51, -0.08],
        [51.515, -0.09]
    ], {
        color: 'purple'
    }).bindPopup("Route Path");

    // 🔥 Simulated Heatmap (Using featureGroup instead of heatLayer)
    var heatmapLayer = L.featureGroup([
        L.circle([51.505, -0.09], {
            radius: 50,
            color: "red",
            fillOpacity: 0.5
        }),
        L.circle([51.51, -0.1], {
            radius: 70,
            color: "orange",
            fillOpacity: 0.5
        }),
        L.circle([51.51, -0.08], {
            radius: 90,
            color: "yellow",
            fillOpacity: 0.5
        })
    ]);

    // 🗺️ Layer Control
    var baseMaps = {
        "OpenStreetMap": osm,
        "Google Satellite": satellite,
        "Google Terrain": terrain,
        "Carto Dark Mode": cartoDark,
        "ESRI Satellite": esriSatellite
    };

    var overlayMaps = {
        "Current Location (Marker)": markerLayer,
        "Polygon": polygonLayer,
        "Circle Marker": circleMarkerLayer,
        "Route Path": routePolyline,
        "Heatmap Simulation": heatmapLayer
    };

    // ✅ Always Show Layer Control
    L.control.layers(baseMaps, overlayMaps, {
        collapsed: true
    }).addTo(map);

    // 📍 GPS Marker Logic
    var marker = null;
    var lastLat = null,
        lastLng = null;
    var userZoomLevel = map.getZoom();

    map.on('zoomend', function() {
        userZoomLevel = map.getZoom();
    });



    function get_location() {
        let vest_heartrate = document.getElementById('vest_heartrate');
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
                        marker = L.marker([lat, lng]).addTo(markerLayer)
                            .bindPopup(response.data[0].vest_number).openPopup();
                    } else {
                        marker.setLatLng([lat, lng]);
                    }

                    if (lastLat === null || lastLng === null || Math.abs(lastLat - lat) > 0.0001 || Math.abs(lastLng - lng) > 0.0001) {
                        map.setView([lat, lng], userZoomLevel);
                    }

                    let heart_rate_display = "";

                    if (response.data[0].heart_rate == 4095) {
                        heart_rate_display = "Normal";
                    } else {
                        heart_rate_display = "No readings";
                    }
                    // vest_heartrate.innerText = response.data[0].heart_rate;
                    vest_heartrate.innerText = heart_rate_display;

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
    setInterval(get_location, 1000);
    get_location();
</script>



<?php
include_once('templates/footer.php');
?>