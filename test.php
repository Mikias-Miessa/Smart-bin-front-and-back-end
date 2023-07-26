<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>Smart Waste Bins</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>



    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="assets/js/gauge/dist/gauge.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <h1 class="text-center">Smart Waste Bins</h1>
    <hr class="border border-primary border-3 opacity-75">

    <div class="row">
        <div class="col-6">
            <div id="binsContainer">

            </div>
            <div id="resultContainer">

            </div>
            <div id="2">
                <div id="tableContainer"></div>

                <canvas id="predictionContainer">
                </canvas>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>