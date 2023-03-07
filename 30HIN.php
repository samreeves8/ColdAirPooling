<?php
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM 30HIN";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "DateTime: ". $row["DateTime"]. "   Temperature (C): ".$row["Temperature"]. "<br>";
        }
    } else {
        echo "0 results";
    }
?>