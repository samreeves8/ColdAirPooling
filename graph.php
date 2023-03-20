<?php

    $temps = array();  
    $dates = array();

    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT Temperature, DateTime FROM TempData WHERE Sensor = '05VAN' 
            AND DateTime BETWEEN '2022-12-20 00:00:00' AND '2022-12-21 00:00:00';";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    echo $sql."<br>";
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $temps[] = $row["Temperature"];
            $dates[] = $row["DateTime"]; 
        }
    }

    $myArrayJsonTemps = json_encode($temps);
    $myArrayJsonDates = json_encode($dates);
    echo "<script>var temps = JSON.parse('" . $myArrayJsonTemps . "');</script>";
    echo "<script>var dates = JSON.parse('" . $myArrayJsonDates . "');</script>";
?>


<!DOCTYPE html>
<html>
  <head>
    <title>Temperature Sensor Readings</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
        
  </head>
  <body>
    <canvas id="myChart"></canvas>
    <script>
        temps = temps.map(Number);

        console.log(temps);
        new Chart("myChart", {
            
            type: "line",
            data: {
                labels: dates,
                datasets: [{
                    data: temps,
                    borderColor: "red",
                    fill: false
                }]
            },
            options: {
                legend: {display: false}
            }
        });
    </script>
  </body>
</html>
