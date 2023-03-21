<?php

    $temps = array();  
    $dates = array();
    $temps2 = array();
    $dates2 = array();

    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT DISTINCT Sensor FROM SensorData"; // only select unique sensor names
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<form action="query.php" method="POST">';
        while($row = $result->fetch_assoc()) {
            echo '<label><input type="checkbox" name="sensors[]" value="' . $row['Sensor'] . '">' . $row['Sensor'] . '</label><br>';
        }
        
    } else {
        echo "0 results";
    }

    echo 
    '<label for="dateStart">Select a start date:</label>
    <input type="date" id="dateStart" name="dateStart">
    <label for="timeStart">Select a start time:</label>
    <input type="time" id="timeStart" name="timeStart">

    <br>

    <label for="dateEnd">Select an end date:</label>
    <input type="date" id="dateEnd" name="dateEnd">

    <label for="timeEnd">Select an end time:</label>
    <input type="time" id="timeEnd" name="timeEnd"><br>
    <input type="submit" value="Submit"></form>';

    if($_SERVER['REQUEST_METHOD']==='POST'){
        $sensors = isset($_POST['sensors']) ? $_POST['sensors'] : array();
        $dateStart = $_POST['dateStart'];
        $dateEnd = $_POST['dateEnd'];
        $timeStart = $_POST['timeStart'];
        $timeEnd = $_POST['timeEnd'];
        $dateTimeStart = $dateStart . ' '.$timeStart;
        $dateTimeEnd = $dateEnd . ' ' . $timeEnd;

        foreach($sensors as $sensor){
            $table = null;
            if(in_array($sensor, $humidity)){
                $table = "HumidData";
            }else{
                $table = "TempData";
            }

            $sql = "SELECT Temperature, DateTime FROM " . $table . " WHERE Sensor = ? AND DateTime BETWEEN ? AND ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $sensor, $dateTimeStart, $dateTimeEnd);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    
                }
            }
        
        }
    }

    $sql = "SELECT Temperature, DateTime FROM TempData WHERE Sensor = '05VAN' 
            AND DateTime BETWEEN '2022-12-20 07:00:00' AND '2022-12-20 22:00:00';";
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
