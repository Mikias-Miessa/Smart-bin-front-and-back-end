<?php

require __DIR__ . '/vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\Exceptions\DataTransferException;

$mqttServer = "mqtt.example.com";  // MQTT broker server address
$mqttPort = 1883;  // MQTT broker port
$mqttTopic = "waste/bin/level";  // MQTT topic to subscribe to

$bins = []; // Associative array to store bin data dynamically

$mqtt = new MqttClient($mqttServer, $mqttPort);

// Connect to the MQTT broker
try {
    $mqtt->connect();
} catch (DataTransferException $e) {
    echo "MQTT connection failed: " . $e->getMessage();
    exit(1);
}

// Subscribe to the MQTT topic
try {
    $mqtt->subscribe($mqttTopic, function ($topic, $message) use (&$bins) {
        // Process the received data (waste level)
        $data = json_decode($message, true);

        $binId = $data['binId'];
        $level = $data['level'];

        // Check if the bin ID already exists
        if (array_key_exists($binId, $bins)) {
            // Update the waste level for the existing bin
            $bins[$binId]['level'] = $level;
        } else {
            // Create a new entry for the bin
            $bins[$binId] = [
                'level' => $level
            ];
        }
    });

    // Handle requests from the frontend
    if (isset($_POST['binId'])) {
        $binId = $_POST['binId'];

        // Find the selected bin data
        if (array_key_exists($binId, $bins)) {
            $binData = $bins[$binId];

            // Prepare the response data
            $response = array(
                'binId' => $binId,
                'level' => $binData['level']
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
    while (true) {
        try {
            $mqtt->loop(1); // Process incoming messages with a timeout of 1 second
        } catch (DataTransferException $e) {
            echo "MQTT error: " . $e->getMessage();
            break; // Exit the loop on error
        }
    }
} catch (DataTransferException $e) {
    echo "MQTT error: " . $e->getMessage();
}

// Disconnect from the MQTT broker
try {
    $mqtt->disconnect();
} catch (DataTransferException $e) {
    echo "MQTT disconnection failed: " . $e->getMessage();
}
