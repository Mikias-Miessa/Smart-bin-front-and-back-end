<?php

require __DIR__ . '/vendor/autoload.php';
include 'conn.php';

// use PhpMqtt\Client\MqttClient;
// use PhpMqtt\Client\Exceptions\DataTransferException;
// // use mysqli;

// $mqttServer = "06c84745277c47e5a6a5fa9c6981d8fd.s2.eu.hivemq.cloud";  // MQTT broker server address
// $mqttPort = 8883;  // MQTT broker port
// $mqttTopic = "test";  // MQTT topic to subscribe to


// // MQTT client configuration
// $mqtt = new MqttClient($mqttServer, $mqttPort);

// // Connect to the MQTT broker
// try {
//     $mqtt->connect();
// } catch (DataTransferException $e) {
//     echo "MQTT connection failed: " . $e->getMessage();
//     exit(1);
// }

// // Subscribe to the MQTT topic
// try {
//     $mqtt->subscribe($mqttTopic, function ($topic, $message) use ($con) {
//         // Process the received data (waste level and location)
//         $data = json_decode($message, true);
//         echo ($data);
//         // $binId = $data['binId'];
//         // $level = $data['level'];
//         // $location = $data['location'];

//         // // Update the waste level and location in the database
//         // $sql = "INSERT INTO bins (bin_id, level, location) VALUES ('$binId', '$level', '$location') ON DUPLICATE KEY UPDATE level = '$level', location = '$location'";
//         // $con->query($sql);
//     });

// Handle requests from the frontend
if (isset($_POST['binId'])) {
    $binId = $_POST['binId'];

    // Retrieve the bin data from the database
    $sql = "SELECT level, location FROM bins WHERE bin_id = '$binId'";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $level = $row['level'];
        $location = $row['location'];

        // Prepare the response data
        $response = array(
            'binId' => $binId,
            'level' => $level,
            'location' => $location
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

    // Keep the script running to continuously check for MQTT messages
//     while (true) {
//         try {
//             $mqtt->loop(1); // Process incoming messages with a timeout of 1 second
//         } catch (DataTransferException $e) {
//             echo "MQTT error: " . $e->getMessage();
//             break; // Exit the loop on error
//         }
//     }
// } catch (DataTransferException $e) {
//     echo "MQTT error: " . $e->getMessage();
// }

// // Disconnect from the MQTT broker
// try {
//     $mqtt->disconnect();
// } catch (DataTransferException $e) {
//     echo "MQTT disconnection failed: " . $e->getMessage();
//     exit(1);
// }

// Close the database connection
