<?php
include 'conn.php';
// Retrieve the bin ID from the AJAX request
$binId = $_POST['binId'];
// $binId = 3;

// Prepare the SQL query to fetch the top 7 recent bin history data
$query = "SELECT level, time FROM bin_history WHERE bin_id = ? ORDER BY time DESC LIMIT 7";

// Prepare a statement and bind the bin ID parameter
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 's', $binId);

// Execute the statement
mysqli_stmt_execute($stmt);

// Bind the result variables
mysqli_stmt_bind_result($stmt, $level, $time);

// Create an array to store the bin history data
$binHistory = array();

// Fetch the results
while (mysqli_stmt_fetch($stmt)) {
    $history = array(
        'level' => $level,
        'time' => $time
    );

    // Add the history data to the array
    $binHistory[] = $history;
}

// Close the statement and database connection
mysqli_stmt_close($stmt);
mysqli_close($con);

// Return the bin history data as JSON response
header('Content-Type: application/json');
echo json_encode($binHistory);
