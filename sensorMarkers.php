
<?php
        global $conn;
        $markers = array();
        
        $sql = "SELECT Sensor, Latitude, Longitude, Elevation, DATE_FORMAT(Date, '%Y-%m-%d') as Date, humidity FROM SensorData";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $markers[] = array(
                    "id" => $row['Sensor'],
                    "lat" => $row['Latitude'],
                    "lng" => $row['Longitude'],
                    "elevation" => $row['Elevation'],
                    "dateInstalled" => $row['Date'],
                    "recordsHumidity" => $row['humidity']
                );
            }
        }
        
        echo json_encode($markers);
        
        
    ?>