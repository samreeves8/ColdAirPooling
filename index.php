<form action="./query.php" method="post" name="query" id="query">
    <button type="submit" id="submit" name="query" class="btn-submit"> Query </button>
</form>
<?php
    //get's connection to database
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //develops query
    $sql = "SELECT * FROM SensorData";
    $result = $conn->query($sql);

    //creates a row for each sensor and adds a link to each row that will allow the user to query each sensor
    if ($result->num_rows > 0) {
        while($row = $result->fetch_array()) {
            echo "Sensor: <a href='https://gunnisoncoldpooling.net/index.php?sensorID=".$row['Sensor']."'>" . $row["Sensor"]. "</a>   Latitude: " . $row["Latitude"]. "   Longitude: ". $row["Longitude"]. "   Elevation: ". $row["Elevation"]. "   DateTime: " . $row["DateTime"]. "<br>";
        }
    } else {
        echo "0 results";
    }
    
    //defines constant humidity array for all sensors located in different table
    $humidity = array("01OBS", "10NEM", "17WIL", "21ALM", "24CAM", "29CAB");
    
    
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