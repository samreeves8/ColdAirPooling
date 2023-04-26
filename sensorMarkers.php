
<?php
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $markers = array();
    
    $sql = "SELECT Sensor, ROUND(Latitude, 2) as Latitude, ROUND(Longitude, 2) as Longitude, Elevation, DATE_FORMAT(Date, '%Y-%m-%d') as Date, humidity, Description FROM SensorData";
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
                "recordsHumidity" => $row['humidity'],
                "description" => $row['Description']
            );
        }
    }
    
    echo json_encode($markers);
?> 
