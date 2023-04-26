<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/Western Logo.png">
    <title>Home</title>
    <link rel="stylesheet" href="styles/nav.css">
    <link rel = "stylesheet" href = "styles/index.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'navBar.php';?>
    
</body>
</html>

<?php
    //get's connection to database
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //develops query
    $sql = "SELECT * FROM SensorData";
    $result = $conn->query($sql);

    //creates a table with headers for each column
    echo "<table>";
    echo "<tr><th>Sensor</th><th>Elevation</th><th>Location</th></tr>";


    //creates a row for each sensor and adds a link to each row that will allow the user to query each sensor
    if ($result->num_rows > 0) {
        // output data of each row as a table row
        while($row = $result->fetch_array()) {
            echo "<tr><td><a href='sensorInfo.php?sensor=" . $row["Sensor"] . "'>" . $row["Sensor"] . "</a></td><td>". $row["Elevation"]. "</td><td>" . $row["Description"]. "</td></tr>";
        }
    } else {
        echo "<tr><td colspan='5'>0 results</td></tr>";
    }
    
    echo "</table>";
    
    //defines constant humidity array for all sensors located in different table
    //gather sensors that gather humidity
    $humidity[] = array();
    $humiditySQL = "SELECT DISTINCT Sensor from HumidData";
    $stmt = $conn->prepare($humiditySQL);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $humidity[] = $row['Sensor'];
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['sensorID'])) {
            $sensorID = $_GET['sensorID'];
            $stmt = null;
            $h = null;
            
            //Changes query based on talbe
            if(in_array($sensorID, $humidity)){
                 $stmt = $conn->prepare("SELECT * FROM HumidData WHERE Sensor = ?");
                 $h = true;
            }else{
                $stmt = $conn->prepare("SELECT * FROM TempData WHERE Sensor = ?");
                $h = false;
            }
            
            //Binds parameters and executes statement
            $stmt->bind_param("s", $sensorID);
            $stmt->execute();
            $result = $stmt->get_result();
            
            
            //Iterates through all rows in $result which contains the query's return value
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    //Echo's rows based on table
                    if($h){
                        echo "Sensor: " . $row["Sensor"] . "   DateTime: " . $row["Date"] . "   Temperature: " . $row["Temperature"] . "   RH: " . $row["RH"] . "   DewPoint: " . $row["DewPoint"]. "<br>";
                    }else{
                        echo "Sensor: " . $row["Sensor"] . "   DateTime: " . $row["Date"] . "   Temperature: " . $row["Temperature"] . "<br>";
                    }
                    
                }
            } else {
                echo "No results found for sensorID " . $sensorID;
            }
        }
    }
    
    
?>