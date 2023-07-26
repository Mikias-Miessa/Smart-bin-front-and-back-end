var intervalID;

$(document).ready(function () {
    var origin;
    var destination;
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    } else {
        console.log('Geolocation is not supported by this browser.');
    }

    function successCallback(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        // origin = {
        //     latitude: latitude,
        //     longitude: longitude
        // };

        // return origin;
        console.log('Latitude: ' + origin.latitude);
        console.log('Longitude: ' + origin.longitude);
    }
    // console.log(origin)

    function errorCallback(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                console.log('User denied the request for Geolocation.');
                break;
            case error.POSITION_UNAVAILABLE:
                console.log('Location information is unavailable.');
                break;
            case error.TIMEOUT:
                console.log('The request to get user location timed out.');
                break;
            case error.UNKNOWN_ERROR:
                console.log('An unknown error occurred.');
                break;
        }
    }
    binlist();
    // Create a new map instance
    var map = L.map('map').setView([8.9806, 38.7578], 11);
    L.tileLayer('https://api.maptiler.com/maps/basic-v2/{z}/{x}/{y}.png?key=Acn1BE9N58lozfv2mMVc', {
        attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
    }).addTo(map);
    var barChart;
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

    origin = {
        latitude: 8.887226841414181,
        longitude: 38.81120906312185
    };

    // destination = {
    //     latitude: 8.98099,
    //     longitude: 38.7578
    // };
    // calculateRoutingDistance(origin, destination);
    // console.log(origin, destination)


    function calculateRoutingDistance(origin, destination) {
        const url = `http://router.project-osrm.org/route/v1/driving/${origin.longitude},${origin.latitude};${destination.longitude},${destination.latitude}`;

        try {
            const response = $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                async: false
            }).responseJSON;
            // console.log(response)
            const route = response.routes[0];
            const distanceInMeters = route.distance;
            const distanceInKilometers = (distanceInMeters / 1000).toFixed(2);
            console.log(distanceInKilometers + " km"); // Distance in kilometers
            return distanceInKilometers
            // console.log(origin, destination)
        } catch (error) {
            console.log(error);
            throw new Error('Error calculating routing distance.');
        }
    }

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
            map.eachLayer(function (layer) {
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
            success: function (data) {
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
            error: function (error) {
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

    function createOrUpdateBarGraph(binData) {
        // Extract bin IDs and levels from bin data
        var binIds = binData.map(function (bin) {
            return bin.bin_id;
        });
        var levels = binData.map(function (bin) {
            return bin.level;
        });

        // Define the color thresholds for different level groups
        var colorThresholds = {
            green: 20,
            orange: 80
        };

        // Assign colors to each data point based on the level ranges
        var backgroundColors = levels.map(function (level) {
            if (level <= colorThresholds.green) {
                return 'green';
            } else if (level <= colorThresholds.orange) {
                return 'rgba(255, 165, 0, 0.7)'; // Less intense orange color
            } else {
                return 'rgba(255, 0, 0, 0.7)'; // Less intense red color
            }
        });

        // Check if the chart exists
        if (barChart) {
            // If the chart exists, update the data and redraw
            barChart.data.labels = binIds;
            barChart.data.datasets[0].data = levels;
            barChart.data.datasets[0].backgroundColor = backgroundColors;
            barChart.update();
        } else {
            // If the chart does not exist, create a new chart
            var ctx = document.getElementById('predictionContainer').getContext('2d');
            barChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: binIds,
                    datasets: [{
                        label: 'Bin Levels',
                        data: levels,
                        backgroundColor: backgroundColors
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Level (%)'
                            }
                        }
                    }
                }
            });
        }
    }

    function fetchBinHistory(binId) {
        // Make an AJAX request to fetch bin history data from the server
        $.ajax({
            url: 'binhistory.php',
            method: 'POST',
            data: {
                binId: binId
            },
            success: function (response) {
                // Clear the canvas with ID 'x'
                var canvas = document.getElementById('predictionContainer');
                canvas.style.display = 'none';
                constructBinHistoryTable(response);
            },
            error: function (xhr, status, error) {
                console.log('Error occurred while fetching bin history:', error);
            }
        });
    }

    // Function to construct and display the bin history table
    function constructBinHistoryTable(binHistory) {
        // Create a new table element
        var table = document.createElement('table');
        table.style.marginBottom = '50px';

        // Add the 'binHistoryTable' ID to the table
        table.id = 'binHistoryTable';

        // Create the table header row
        var headerRow = table.insertRow();
        var levelHeader = headerRow.insertCell();
        var timeHeader = headerRow.insertCell();

        // Set the header cell content
        levelHeader.textContent = 'Level';
        timeHeader.textContent = 'Time';

        // Iterate over the bin history data and create table rows
        binHistory.forEach(function (history) {
            // Create a new row
            var row = table.insertRow();

            // Create cells for level and time
            var levelCell = row.insertCell();
            var timeCell = row.insertCell();

            // Set the level and time values in the cells
            levelCell.textContent = history.level;
            timeCell.textContent = history.time;
        });

        // Get the container element where the table will be displayed
        var container = document.getElementById('tableContainer');


        // Clear the container
        container.innerHTML = '';
        // Add a title before the table
        // Add a title before the table
        var title = document.createElement('h3');
        title.textContent = 'Bin History';

        // Apply CSS styles to center-align the title
        title.style.textAlign = 'center';
        title.style.marginTop = '20px';
        container.appendChild(title);


        container.appendChild(table);

        // Create another button
        var anotherButton = document.createElement('button');
        anotherButton.textContent = 'Back';
        anotherButton.classList.add('btn', 'btn-secondary');
        anotherButton.style.display = 'block';
        anotherButton.style.margin = '0 auto';
        anotherButton.style.border = '0';


        anotherButton.style.width = '200px';
        anotherButton.style.backgroundColor = 'darkblue';
        anotherButton.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.6)';


        anotherButton.addEventListener('click', function () {
            var resultContainer = document.getElementById('resultContainer');
            resultContainer.innerHTML = '';

            var binsContainer = document.getElementById('binsContainer');
            binsContainer.style.height = '300px';

            var marker = document.getElementById('marker');
            if (marker) {
                marker.remove();
            }

            hideMap();
            zoomOut();

            var canvas = document.getElementById('predictionContainer');
            canvas.style.display = 'block';

            var tableContainer = document.getElementById('tableContainer');
            tableContainer.innerHTML = '';

            // clearInterval(intervalID);
            // updateGaugeValue(0);

            // Show the bin buttons again
            // binlist();
        });

        container.appendChild(anotherButton);

    }



    function binlist() {
        clearInterval(intervalID);

        fetch('binList.php')
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Request failed with status: ' + response.status);
                }
                return response.json();
            })
            .then(function (data) {
                // console.log(data);
                createOrUpdateBarGraph(data);
                // Get the container element where the buttons will be added
                var container = document.getElementById('binsContainer');
                data.forEach(function (bin) {
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
                        marker.on('click', function (e) {
                            // Access the marker data and handle the click event
                            var markerID = id;
                            console.log('Marker clicked:', markerID);
                            // Perform any desired actions with the clicked marker data
                            displayOnMap(markerID);
                            fetchBinHistory(markerID);
                            intervalID = setInterval(function () {
                                sendIDToServer(markerID); // Call the sendIDToServer function every second
                            }, 1000);
                            $('#binsContainer').empty();
                            document.getElementById('binsContainer').style.height = '0px';
                        });

                    } else if (level > 20 && level <= 80) {
                        marker = L.marker([lat, lon], {
                            icon: halfIcon
                        }).addTo(map);
                        // Add a click event listener to the marker
                        marker.on('click', function (e) {
                            // Access the marker data and handle the click event
                            var markerID = id;
                            console.log('Marker clicked:', markerID);
                            // Perform any desired actions with the clicked marker data
                            displayOnMap(markerID);
                            fetchBinHistory(markerID);
                            intervalID = setInterval(function () {
                                sendIDToServer(markerID); // Call the sendIDToServer function every second
                            }, 1000);
                            $('#binsContainer').empty();
                            document.getElementById('binsContainer').style.height = '0px';
                        });
                    } else {
                        marker = L.marker([lat, lon], {
                            icon: fullIcon
                        }).addTo(map);
                        // Add a click event listener to the marker
                        marker.on('click', function (e) {
                            // Access the marker data and handle the click event
                            var markerID = id;
                            fetchBinHistory(markerID);
                            console.log('Marker clicked:', markerID);
                            // Perform any desired actions with the clicked marker data
                            displayOnMap(markerID);
                            intervalID = setInterval(function () {
                                sendIDToServer(markerID); // Call the sendIDToServer function every second
                            }, 1000);
                            $('#binsContainer').empty();
                            document.getElementById('binsContainer').style.height = '0px';
                        });
                    }
                });
                // Create the title element
                var title = document.createElement('h2');
                title.textContent = 'Available Bins';
                title.classList.add('bins-title'); // Add class for styling

                // Append the title element to the binsContainer
                var binsContainer = document.getElementById('binsContainer');
                binsContainer.insertBefore(title, binsContainer.firstChild);

                // Create a table element
                var table = document.createElement('table');
                table.classList.add('card-table'); // Add class for styling

                // Iterate over the data array
                data.forEach(function (item) {
                    // Create a new row
                    var row = table.insertRow();
                    // var destin = item.
                    // console.log(item)
                    destination = {
                        latitude: item.lat,
                        longitude: item.lon
                    }
                    console.log(destination)
                    km = calculateRoutingDistance(origin, destination);
                    // console.log(km);
                    // Create a cell for the bin ID
                    var binIdCell = row.insertCell();
                    var binIdButton = document.createElement('button');
                    binIdButton.textContent = 'Bin Number ' + item.bin_id + ', Distance: ' + km + ' km away';
                    binIdButton.style.fontFamily = 'arial';
                    binIdButton.addEventListener('click', function () {
                        // Button click event handler
                        $('#binsContainer').empty();
                        document.getElementById('binsContainer').style.height = '0';
                        var id = item.bin_id;
                        console.log('Button clicked with ID:', id);

                        displayOnMap(id);
                        fetchBinHistory(id);
                        intervalID = setInterval(function () {
                            sendIDToServer(id);
                        }, 1000);
                    });
                    binIdCell.appendChild(binIdButton);

                    // Append the row to the table
                    table.appendChild(row);
                });

                // Append the table to the container
                container.appendChild(table);

            })
            .catch(function (error) {
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
        staticLabels: {
            font: "20px sans-serif",
            labels: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
            fractionDigits: 0,
            color: "#fff" // Change this to the desired color
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
            success: function (data) {
                console.log('ID sent to the server:', id);

                var dataToShow = data.level; // Assuming the server response has a 'data' property

                updateGaugeValue(dataToShow);

                console.log(destination)
                var dataElement = $('<div>').addClass('card mb-3');

                var cardBody = $('<div>').addClass('card-body');
                var binHeading = $('<h3>').text(`Bin ${data.binId}`).addClass('card-title');
                var wasteLevel = $('<p>').text(`Waste Level: ${data.level}`).addClass('card-text fw-bold');
                var location = $('<p>').text(`Location: ${data.location}`).addClass('card-text');
                var coordinate = $('<p>').text(`Coordinate: ${data.lat} latitude & ${data.lon} longtiude`).addClass('card-text');


                cardBody.append(binHeading, wasteLevel, location, coordinate);
                dataElement.append(cardBody);

                dataElement.css({
                    'padding': '10px',
                    'margin': 'auto',
                    'width': '100%', /* Adjust the width as per your preference */
                    'border': '1px solid #ccc',
                    'border-radius': '5px',
                    'background-color': '#f8f8f8',
                    'box-shadow': '0 2px 4px rgba(0, 0, 0, 0.2)' /* Add a shadow effect */
                });


                // Append the data element and the button to a container element
                var container = $('#resultContainer');
                container.empty(); // Clear the previous content
                container.append(dataElement);




            },
            error: function (error) {
                console.error('Error sending ID:', error);
                // Handle error response here
            }
        });
    }
});