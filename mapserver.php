<?php
include 'conn.php';

if (isset($_POST['binId'])) {
    $binId = $_POST['binId'];

    // $binId = 1;
    // Retrieve the bin data from the database
    $sql = "SELECT * FROM bins WHERE bin_id = '$binId'";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lat = $row['lat'];
        $lon = $row['lon'];
        $level = $row['level'];

        // Prepare the response data
        $response = array(
            'binId' => $binId,
            'lat' => $lat,
            'lon' => $lon,
            'level' => $level
        );

        // Send the response to the frontend as JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        echo "Invalid binId";
    }
} else {
    echo "No binId specified";
}
