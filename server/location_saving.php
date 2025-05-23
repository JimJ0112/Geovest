<?php
require_once('database.php');

$vest_id = $_POST['vest_num'];
$lat = $_POST['lat'];
$long = $_POST['lng'];
$loc_name = $_POST['loc_name'];
$heartrate = $_POST['hrate'];

date_default_timezone_set('Asia/Manila');

$date_time = date('Y-m-d H:m:s');

$query = "SELECT * FROM vest_locations WHERE vest_id = $vest_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    // If vest_id exists, update the record
    $update_query = "UPDATE vest_locations SET latitude = $lat, longitude = $long, location_name = '$loc_name', heart_rate = $heartrate, datetime = '$date_time' WHERE vest_id = $vest_id";
    if (mysqli_query($conn, $update_query)) {
        echo "Location updated successfully!";
    } else {
        echo "Error updating location: " . mysqli_error($conn);
    }
} else {
    // If vest_id does not exist, insert a new record
    $insert_query = "INSERT INTO vest_locations (vest_id, latitude, longitude, location_name, heart_rate, datetime ) VALUES ($vest_id, $lat, $long, '$loc_name', $heartrate, '$date_time')";
    if (mysqli_query($conn, $insert_query)) {
        echo "Location saved successfully!";
    } else {
        echo "Error saving location: " . mysqli_error($conn);
    }
}

// Close connection
mysqli_close($conn);
