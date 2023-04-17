<?php
    session_start();
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/nav.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel = "stylesheet" href = "styles/query.css">
    <title>Document</title>
</head>
<body>
    <?php include 'navBar.php';?>

    <h2>Add a new Sensor</h2>
    <form action="add.php" method="POST">
    <label for="sensor-name">Sensor Name:</label>
    <input type="text" id="sensor-name" name="sensor-name"><br><br>

    <label for="latitude">Latitude:</label>
    <input type="number" id="latitude" name="latitude"><br><br>

    <label for="longitude">Longitude:</label>
    <input type="number" id="longitude" name="longitude"><br><br>

    <label for="elevation">Elevation:</label>
    <input type="number" id="elevation" name="elevation"><br><br>

    <label for="date-installed">Date Installed:</label>
    <input type="date" id="date-installed" name="date-installed"><br><br>

    <input type="submit" value="Submit">
</form>
</body>
</html>