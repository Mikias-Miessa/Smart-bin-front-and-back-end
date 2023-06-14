import paho.mqtt.client as mqtt

# MQTT broker information
broker = "mqtt.example.com"  # Replace with your HiveMQ broker address
port = 1883                  # MQTT broker port
topic = "your/topic"         # MQTT topic to subscribe to

# MQTT callback functions
def on_connect(client, userdata, flags, rc):
    if rc == 0:
        print("Connected to MQTT broker")
        client.subscribe(topic)
    else:
        print("Connection failed with result code", rc)

def on_message(client, userdata, msg):
    print("Received message:", msg.payload.decode())

# Create an MQTT client instance
client = mqtt.Client()

# Set callback functions
client.on_connect = on_connect
client.on_message = on_message

# Connect to the MQTT broker
client.connect(broker, port)

# Start the MQTT network loop
client.loop_forever()
