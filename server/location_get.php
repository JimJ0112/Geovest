<?php
require_once('database.php');
require_once('helpers/ajax_result.php');
require_once('helpers/cors_helper.php');

header('Content-Type: application/json');

//$vest_id = $_POST['vest_num'];

$vest_id = 1;

$result = $conn->query("SELECT vest_locations.id, vest_locations.vest_id, vest_locations.latitude, vest_locations.longitude, vest_locations.location_name, vest_locations.heart_rate, vests.id as vests_vest_id, vests.vest_number  FROM vest_locations 
LEFT JOIN vests ON vest_locations.vest_id = vests.id
WHERE vest_locations.vest_id = $vest_id ORDER BY vest_locations.id LIMIT 1");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row; // Add each row to the array
    }
}

//echo json_encode($data); // Return JSON data
ajax_result(true, "fetched_successfully", $data);


$conn->close();
