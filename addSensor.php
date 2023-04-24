<?php
    session_start();
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== 1) {
        // user is not logged in, redirect to login page
        echo "<script>location.href='login.php';</script>";
      
        exit;
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
    <?php include 'header.php';?>
    <?php include 'navBar.php';?>

    <h2>Add a new Sensor</h2>
    <form action="addSensor.php" method="POST">
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

    <label for="humidity">Humidity Sensor:</label>
    <input type="checkbox" id="humidity" name="humidity" value="1">Yes
    <input type="checkbox" id="humidity" name="humidity" value="0">No


    <input type="submit" value="Submit">
</form>
</body>
</html>

<?php
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sensorName = $_POST['sensor-name'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $elevation = $_POST['elevation'];
        $dateInstalled = $_POST['date-installed'];
        $humidity = $_POST['humidity'];


        $sql = "INSERT INTO SensorData (Sensor, Latitude, Longitude, Elevation, DateTime, Humidity) VALUES (?, ?, ?, ?, ?, ?)";
        //prepare the query to prevent sql injection
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sddds", $sensorName, $latitude, $longitude, $elevation, $dateInstalled, $humidity);
        if ($stmt->execute()) {
            echo "New sensor added successfully";
        } else {
            echo "Error adding new sensor: " . $stmt->error;
        }
    }
?>
