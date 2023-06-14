<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>Smart Waste Bins</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        #trashBinContainer {
            width: 200px;
            height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #f1f1f1;
        }

        #trashBin {
            width: 100px;
            height: 300px;
            background-color: #333;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        #trashBinFill {
            width: 100%;
            background-image: linear-gradient(to bottom, #4682b4, #4682b4 50%, #808080 50%);
            position: absolute;
            bottom: 0;
            transform-origin: bottom;
            transition: transform 0.5s ease;
        }

        #map {
            width: 900px;
            height: 500px;
            position: fixed;
            bottom: 0;
            right: 0;
        }


        #binLevel {
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        #demo {
            width: 550px;
            height: 300px;
        }

        .tocenter {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="gauge/dist/gauge.js"></script>


</head>

<body>

    <h1 class="text-center">Smart Waste Bins</h1>
    <hr class="border border-primary border-3 opacity-75">

    <div class="row">
        <div class="col-6">
            <div id="binsContainer">

            </div>
            <div id="resultContainer" class="text-center">

            </div>


        </div>

        <div class="col-6">
            <div class="tocenter">
                <canvas id="demo"></canvas>
            </div>
            <div class="tocenter">
                <div id="preview-textfield" class="fw-bold"></div>%
            </div>
            <div id="map"></div>
        </div>
    </div>


    <br>




    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDXqTMd1Jm9-0NveaHLuO6MHgs-gwqydcY"></script>







    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
        var intervalID;
        $(document).ready(function() {
            binlist();
            // Create a new map instance
            var map = L.map('map').setView([8.9806, 38.7578], 11);
            L.tileLayer('https://api.maptiler.com/maps/basic-v2/{z}/{x}/{y}.png?key=Acn1BE9N58lozfv2mMVc', {
                attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
            }).addTo(map);

            var marker;
            var fullIcon = L.icon({
                iconUrl: 'assets/images/full.jpg',
                iconSize: [30, 35],
                iconAnchor: [22, 92],
                popupAnchor: [-3, -76],
                // shadowUrl: 'my-icon-shadow.png',
                shadowSize: [68, 95],
                shadowAnchor: [22, 94]
            });
            var emptyIcon = L.icon({
                iconUrl: 'assets/images/empty.jpg',
                iconSize: [30, 35],
                iconAnchor: [22, 92],
                popupAnchor: [-3, -76],
                // shadowUrl: 'my-icon-shadow.png',
                shadowSize: [68, 95],
                shadowAnchor: [22, 94]
            });
            var halfIcon = L.icon({
                iconUrl: 'assets/images/half.jpg',
                iconSize: [25, 30],
                iconAnchor: [22, 92],
                popupAnchor: [-3, -76],
                // shadowUrl: 'my-icon-shadow.png',
                shadowSize: [68, 95],
                shadowAnchor: [22, 94]
            });


            // Function to zoom to a specific location
            function zoomToLocation(lat, lon) {
                var location = [lat, lon]; // Replace with your desired location coordinates
                var zoomLevel = 16; // Adjust the zoom level as desired

                map.flyTo(location, zoomLevel, {
                    duration: 2, // Animation duration in seconds
                    easeLinearity: 0.5 // Smoother animation
                });
            }
            // Function to zoom out with animation
            function zoomOut() {
                var location = [8.9806, 38.7578];
                map.flyTo(location, 11, {
                    duration: 2, // Animation duration in seconds
                    easeLinearity: 0.5 // Smoother animation
                });
            }


            function displayOnMap(id) {

                // Clear existing marker
                if (marker) {
                    // Remove existing layers
                    map.eachLayer(function(layer) {
                        if (layer instanceof L.Marker) {
                            map.removeLayer(layer);
                        }
                    });
                    // map.removeLayer(marker);
                }


                $.ajax({
                    url: 'mapserver.php',
                    type: 'POST',
                    data: {
                        binId: id
                    },
                    success: function(data) {
                        var lat = parseFloat(data.lat);
                        var lon = parseFloat(data.lon);
                        var level = parseFloat(data.level);
                        console.log(lat, lon, level)
                        zoomToLocation(lat, lon);
                        if (level <= 20) {
                            marker = L.marker([lat, lon], {
                                icon: emptyIcon
                            }).addTo(map);
                        } else if (level > 20 && level <= 80) {
                            marker = L.marker([lat, lon], {
                                icon: halfIcon
                            }).addTo(map);
                        } else {
                            marker = L.marker([lat, lon], {
                                icon: fullIcon
                            }).addTo(map);
                        }


                    },
                    error: function(error) {
                        console.error('Error sending ID:', error);
                        // Handle error response here
                    }
                });

            }

            // Function to hide the map and clear the interval when the "Back" button is clicked
            function hideMap() {
                $('#resultContainer').empty();
                // $('#map').empty();
                // map = undefined;

                clearInterval(intervalID);
                updateGaugeValue(0);
                binlist(); // Show the bin buttons again
            }

            function binlist() {
                clearInterval(intervalID);

                fetch('binList.php')
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Request failed with status: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(function(data) {
                        // Get the container element where the buttons will be added
                        var container = document.getElementById('binsContainer');
                        data.forEach(function(bin) {
                            var lat = parseFloat(bin.lat);
                            var lon = parseFloat(bin.lon);
                            var level = parseFloat(bin.level);
                            var id = bin.bin_id;

                            // Customize marker based on level
                            if (level <= 20) {
                                marker = L.marker([lat, lon], {
                                    icon: emptyIcon
                                }).addTo(map);
                                // Add a click event listener to the marker
                                marker.on('click', function(e) {
                                    // Access the marker data and handle the click event
                                    var markerID = id;
                                    console.log('Marker clicked:', markerID);
                                    // Perform any desired actions with the clicked marker data
                                    displayOnMap(markerID);
                                    intervalID = setInterval(function() {
                                        sendIDToServer(markerID); // Call the sendIDToServer function every second
                                    }, 1000);
                                    $('#binsContainer').empty();
                                });

                            } else if (level > 20 && level <= 80) {
                                marker = L.marker([lat, lon], {
                                    icon: halfIcon
                                }).addTo(map);
                                // Add a click event listener to the marker
                                marker.on('click', function(e) {
                                    // Access the marker data and handle the click event
                                    var markerID = id;
                                    console.log('Marker clicked:', markerID);
                                    // Perform any desired actions with the clicked marker data
                                    displayOnMap(markerID);
                                    intervalID = setInterval(function() {
                                        sendIDToServer(markerID); // Call the sendIDToServer function every second
                                    }, 1000);
                                    $('#binsContainer').empty();
                                });
                            } else {
                                marker = L.marker([lat, lon], {
                                    icon: fullIcon
                                }).addTo(map);
                                // Add a click event listener to the marker
                                marker.on('click', function(e) {
                                    // Access the marker data and handle the click event
                                    var markerID = id;
                                    console.log('Marker clicked:', markerID);
                                    // Perform any desired actions with the clicked marker data
                                    displayOnMap(markerID);
                                    intervalID = setInterval(function() {
                                        sendIDToServer(markerID); // Call the sendIDToServer function every second
                                    }, 1000);
                                    $('#binsContainer').empty();
                                });
                            }
                        });
                        // Generate buttons for each item in the data array
                        data.forEach(function(item) {
                            var button = document.createElement('button');
                            button.textContent = 'Bin Number ' + item.bin_id;
                            button.value = item.bin_id; // Set the value to the ID
                            button.classList.add('button-item', 'btn', 'btn-primary');

                            button.onclick = function() {
                                $('#binsContainer').empty();
                                // Handle button click event here
                                var id = this.value; // Access the button value (ID)
                                console.log('Button clicked with ID:', id);
                                displayOnMap(id);

                                intervalID = setInterval(function() {
                                    sendIDToServer(id); // Call the sendIDToServer function every second
                                }, 1000);

                            };
                            button.style.width = '200px'; // Set the width of the button in pixels
                            container.appendChild(button);
                        });
                    })
                    .catch(function(error) {
                        console.error('Error fetching items:', error);
                    });


            }

            var opts = {
                // color configs
                colorStart: "#6fadcf",
                colorStop: void 0,
                gradientType: 0,
                strokeColor: "#e0e0e0",
                generateGradient: true,
                percentColors: [
                    [0.0, "#a9d70b"],
                    [0.50, "#f9c802"],
                    [1.0, "#ff0000"]
                ],
                // customize pointer
                pointer: {
                    length: 0.8,
                    strokeWidth: 0.035,
                    iconScale: 1.0
                },
                // static labels
                staticLabels: {
                    font: "10px sans-serif",
                    labels: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
                    fractionDigits: 0
                },
                // static zones
                staticZones: [{
                        strokeStyle: "#00FF00",
                        min: 0,
                        max: 80
                    },
                    {
                        strokeStyle: "#FF0000",
                        min: 80,
                        max: 100
                    },

                ],
                // render ticks
                renderTicks: {
                    divisions: 5,
                    divWidth: 1.1,
                    divLength: 0.7,
                    divColor: 333333,
                    subDivisions: 3,
                    subLength: 0.5,
                    subWidth: 0.6,
                    subColor: 666666,
                },
                // the span of the gauge arc
                angle: 0,
                // line thickness
                lineWidth: 0.6,
                // radius scale
                radiusScale: 1.0,
                // font size
                fontSize: 60,
                // if false, max value increases automatically if value > maxValue
                limitMax: false,
                // if true, the min value of the gauge will be fixed
                limitMin: false,
                // High resolution support
                highDpiSupport: true
            };
            var target = document.getElementById('demo');
            var gauge = new Gauge(target).setOptions(opts);

            document.getElementById("preview-textfield").className = "preview-textfield";
            gauge.setTextField(document.getElementById("preview-textfield"));


            gauge.animationSpeed = 40
            gauge.maxValue = 100;
            gauge.setMinValue(0);

            function updateGaugeValue(value) {
                gauge.set(value);
            }

            // Function to send ID to the server
            function sendIDToServer(id) {


                // Make an AJAX request or fetch API call to send the ID to the server
                $.ajax({
                    url: 'mqtt_server.php',
                    type: 'POST',
                    data: {
                        binId: id
                    },
                    success: function(data) {
                        console.log('ID sent to the server:', id);

                        var dataToShow = data.level; // Assuming the server response has a 'data' property

                        updateGaugeValue(dataToShow);

                        var dataElement = $('<div>').addClass('card mb-3');

                        var cardBody = $('<div>').addClass('card-body');
                        var binHeading = $('<h3>').text(`Bin ${data.binId}`).addClass('card-title');
                        var wasteLevel = $('<p>').text(`Waste Level: ${data.level}`).addClass('card-text fw-bold');
                        var location = $('<p>').text(`Location: ${data.location}`).addClass('card-text');


                        cardBody.append(binHeading, wasteLevel, location);
                        dataElement.append(cardBody);

                        dataElement.css({
                            'padding': '10px',
                            'margin': 'auto',
                            'width': '50%',
                            'border': '1px solid #ccc',
                            'border-radius': '5px',
                            'background-color': '#f8f8f8'
                        });

                        // Create another button
                        var anotherButton = $('<button>').text('Back').addClass('btn btn-secondary');




                        anotherButton.on('click', function() {
                            $('#resultContainer').empty();
                            if (marker) {
                                map.removeLayer(marker);
                            }
                            hideMap();
                            zoomOut();
                            // clearInterval(intervalID);
                            // updateGaugeValue(0);
                            // Show the bin buttons again
                            // binlist();
                        });



                        // Append the data element and the button to a container element
                        var container = $('#resultContainer');
                        container.empty(); // Clear the previous content
                        container.append(dataElement, anotherButton);




                    },
                    error: function(error) {
                        console.error('Error sending ID:', error);
                        // Handle error response here
                    }
                });
            }
        });
    </script>
    <script>

    </script>
</body>

</html>