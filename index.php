<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="navbar">
         <ul class="menu">
            <li><a href="#">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="query.php">Query</a></li>
            <li><a href="#">Members</a></li>
            <li><a href="#">Log In</a></li>
         </ul>
    </div>
</body>
</html>

<?php
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM SensorData";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_array()) {
            echo "Sensor: <a href='https://gunnisoncoldpooling.net/index.php?sensorID=".$row['Sensor']."'>" . $row["Sensor"]. "</a>   Latitude: " . $row["Latitude"]. "   Longitude: ". $row["Longitude"]. "   Elevation: ". $row["Elevation"]. "   DateTime: " . $row["DateTime"]. "<br>";
        }
    } else {
        echo "0 results";
    }
    
    $humidity = array("01OBS", "10NEM", "17WIL", "21ALM", "24CAM", "29CAB");
    
    
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['sensorID'])) {
            $sensorID = $_GET['sensorID'];
            $stmt = null;
            $h = null;
            
            if(in_array($sensorID, $humidity)){
                 $stmt = $conn->prepare("SELECT * FROM HumidData WHERE Sensor = ?");
                 $h = true;
            }else{
                $stmt = $conn->prepare("SELECT * FROM TempData WHERE Sensor = ?");
                $h = false;
            }
            
            $stmt->bind_param("s", $sensorID);
            $stmt->execute();
            $result = $stmt->get_result();
            
            

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if($h){
                        echo "Sensor: " . $row["Sensor"] . "   DateTime: " . $row["DateTime"] . "   Temperature: " . $row["Temperature"] . "   RH: " . $row["RH"] . "   DewPoint: " . $row["DewPoint"]. "<br>";
                    }else{
                        echo "Sensor: " . $row["Sensor"] . "   DateTime: " . $row["DateTime"] . "   Temperature: " . $row["Temperature"] . "<br>";
                    }
                    
                }
            } else {
                echo "No results found for sensorID " . $sensorID;
            }
        }
    }
    
    
?>
<br>
<form action="./importCSV.php" method="post" name="uploadFile" id="uploadFile">
    <button type="submit" id="submit" name="upload" class="btn-submit"> Upload File </button>
</form>