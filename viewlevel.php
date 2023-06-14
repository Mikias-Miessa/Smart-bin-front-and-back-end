<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>Smart Waste Bins</title>


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

        #binLevel {
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
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

</body>

</html>