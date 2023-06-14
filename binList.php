<?php

include 'conn.php';

// Select bin IDs from the database
$query = 'SELECT * FROM bins';
$result = mysqli_query($con, $query);

$binIds = array();

// Fetch the results and store the bin IDs in an array
if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {
        // $binIds = $row;
        array_push($binIds, $row);
    }
}

// Convert the bin IDs to JSON
$binIdsJson = json_encode($binIds);

// Send the JSON response to the frontend
// header('Content-Type: application/json');
echo $binIdsJson;
