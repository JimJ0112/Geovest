<?php
require_once('database.php');
require_once('helpers/ajax_result.php');

$vest_id = $_POST['vest_num'];


$result = $conn->query("SELECT vest_id, latitude, longitude, location_name FROM vest_locations WHERE vest_id = $vest_id ORDER BY id LIMIT 1");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row; // Add each row to the array
    }
}

//echo json_encode($data); // Return JSON data
ajax_result(true, "fetched_successfully", $data);


$conn->close();
