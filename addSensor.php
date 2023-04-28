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
    <link rel="icon" type="image/png" href="images/Western Logo.png">
    <link rel="stylesheet" href="styles/nav.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel = "stylesheet" href = "styles/query.css">
    <title>Add Sensor</title>
</head>
<body>
    <?php include 'header.php';?>
    <?php include 'navBar.php';?>

    <h2>Add a new Sensor</h2>
    <form action="addSensor.php?action=add" method="POST" enctype="multipart/form-data">
    <label for="sensor-name">Sensor Name:</label>
    <input type="text" id="sensor-name" name="sensor-name"><br><br>

    <label for="latitude">Latitude:</label>
    <input type="number" id="latitude" name="latitude" step="any"><br><br>

    <label for="longitude">Longitude:</label>
    <input type="number" id="longitude" name="longitude" step="any"><br><br>

    <label for="elevation">Elevation:</label>
    <input type="number" id="elevation" name="elevation"><br><br>

    <label for="date-installed">Date Installed:</label>
    <input type="date" id="date-installed" name="date-installed"><br><br>

    <label for="humidity">Humidity Sensor:</label>
    <input id = "checkbox" type="checkbox" id="humidity" name="humidity" value="1">Yes
    <input id = "checkbox" type="checkbox" id="humidity" name="humidity" value="0">No

    <br><br>
    <label for="sensor-image">Sensor Image:</label>
    <input type="file" id="sensor-image" name="picture"><br><br> 
    <label for="description">Description:</label>
    <textarea name="description" id="description"></textarea><br><br>

    <input type="submit" value="Submit">
    </form>


    <?php
    $sensorList = array();
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT DISTINCT Sensor FROM SensorData";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sensorList[] = $row['Sensor'];
        }
    }
    ?>
    <h2>Delete an Existing Sensor</h2>
    <form action="addSensor.php?action=delete" method="POST" enctype="multipart/form-data">
    <label for="sensor-list">Select Sensor:</label>
    <select id="sensor-list" name="sensor-name">
        <?php
        // Loop through the sensor list and add each sensor as an option in the dropdown menu
        foreach ($sensorList as $sensor) {
            echo "<option value='$sensor'>$sensor</option>";
        }
        ?>
    </select><br><br>
    <input type="submit" value="Submit">
</form>


</body>
</html>

<?php

    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    function delete()
{
    global $conn;

    $sensor = $_POST['sensor-name'];
    $sql = "DELETE FROM SensorData WHERE Sensor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sensor);
    if ($stmt->execute()) {
        echo "Sensor deleted successfully";
    } else {
        echo "Error deleting sensor: " . $stmt->error;
    }
}

    function add(){
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
        $description = $_POST['description'];
        $file = isset($_FILES['picture']) ? $_FILES['picture'] : NULL;

        if($file == NULL) {
            $file_path = "no picture";
        } else {
            $file_name = uniqid() . '-' . $_FILES['picture']['name'];
            $file_path = 'images/' . $file_name;
            move_uploaded_file($_FILES['picture']['tmp_name'], $file_path);
        }
        $sql = "INSERT INTO SensorData (Sensor, Latitude, Longitude, Elevation, Date, humidity, Picture, Description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        //prepare the query to prevent sql injection
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdddsdss", $sensorName, $latitude, $longitude, $elevation, $dateInstalled, $humidity, $file_path, $description);
        if ($stmt->execute()) {
            echo "New sensor added successfully";
        } else {
            echo "Error adding new sensor: " . $stmt->error;
        }
    }

    // Route the request to the appropriate function based on the URL
    $action = isset($_GET['action']) ? $_GET['action'] : 'index';
    switch ($action) {
        case 'add':
            add();
            break;
        case 'delete':
            delete();
            break;
    }
?>
