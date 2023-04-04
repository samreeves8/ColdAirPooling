<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="nav.css">
    <link rel = "stylesheet" href = "query.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <title>Document</title>
</head>
<body>
    <div class="navbar">
         <ul class="menu">
            <li><a href="#">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="query.php">Query</a></li>
            <li><a href="#">Members</a></li>
            <li><a href="login.php">Log In</a></li>
            <li><a href="graph.php">Graph's</a></li>
         </ul>
    </div>
</body>
</html>


<?php

    $humidity = array("01OBS", "10NEM", "17WIL", "21ALM", "24CAM", "29CAB");
    if (isset($_POST['val'])){

        $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $val = isset($_POST['val']) ? $_POST['val'] : null;
        $dateTimeStart = $_POST['dateTimeStart'];
        $dateTimeEnd = $_POST['dateTimeEnd'];
        $serializedArray = $_POST['sensors'];
        $unserializedArray = unserialize($serializedArray);
        
        if ($val !== null) {
            if($val == "3 Minutes"){
                $x = 3;
            } else if($val == "6 Minutes"){
                $x = 6;
            } else if($val == "15 Minutes"){
                $x = 15;
            } else if($val == "30 Minutes"){
                $x = 30;
            } else if($val == "1 Hour"){
                $x = 60;
            } else if($val == "2 Hours"){
                $x = 120;
            } else if($val == "4 Hours"){
                $x = 240;
            } else if($val == "12 Hours"){
                $x = 720;
            } else if($val == "Daily"){
                $x = 1440;
            } else if($val == "Bi-Daily"){
                $x = 2880;
            } else if($val == "Weekly"){
                $x = 10080;
            } else if($val == "Bi-Weekly"){
                $x = 20160;
            } else {
                // handle any other cases or errors here
            }
        }

        foreach($unserializedArray as $sensor){
            $table = null;
            if(in_array($sensor, $humidity)){
                $table = "HumidData";
            }else{
                $table = "TempData";
            }

            $sqlString = "SELECT Sensor, FLOOR((@row_number:=@row_number+1)/$x) AS GroupNum, MIN(DateTime) AS StartDateTime, MAX(DateTime) AS EndDateTime, 
            MIN(Temperature) AS MinTemperature, MAX(Temperature) AS MaxTemperature, ROUND(AVG(Temperature),2) AS AvgTemperature 
            FROM $table, (SELECT @row_number:=0) AS t WHERE Sensor IN ('$sensor') AND DateTime BETWEEN '$dateTimeStart' AND '$dateTimeEnd' GROUP BY Sensor, GroupNum  ORDER BY `Sensor`  DESC;";

            // $sql = "SELECT DISTINCT Sensor, FLOOR((@row_number:=@row_number+1)/". $x .") AS GroupNum, Min(DateTime) AS StartDateTime, MAX(DateTime) AS EndDateTime,
            // MIN(Temperature) AS MinTemperature, MAX(Temperature) AS MaxTemperature, ROUND(AVG(Temperature),2) AS AvgTemperature
            // FROM " . $table . ", (SELECT @row_number:=0) AS t WHERE Sensor IN (?) AND DateTime BETWEEN ? AND ? GROUP BY GroupNum ORDER BY `Sensor` DESC;";
               
            $sql = "SELECT DateTime, Temperature
            FROM ".$table."
            WHERE Sensor = ?
            AND DateTime >= ? AND DateTime <= ?
            AND MINUTE(DateTime) % ".$x." = 0
            GROUP BY Sensor, DateTime
            ORDER BY DateTime ASC;";


            // SELECT 
            //     DATE_FORMAT(dateTime, '%Y-%m-%d %H:%i') AS time_interval, 
            //     AVG(temperature) AS average_temperature
            // FROM 
            //     sensors
            // WHERE 
            //     sensor = '02FAI'
            //     AND dateTime >= '2023-01-01 0:00:00' AND dateTime <= '2023-01-31 0:00:00'
            //     AND MINUTE(dateTime) % 1440 = 0
            // GROUP BY 
            //     time_interval
            // ORDER BY 
            //     time_interval ASC;

            // SELECT DATE(dateTime) AS date, MIN(temperature) AS min_temp, MAX(temperature) AS max_temp
            // FROM temperature_sensors
            // WHERE sensor_name = 'your_sensor_name'
            // AND dateTime >= 'your_start_date_time' AND dateTime <= 'your_end_date_time'
            // GROUP BY DATE(dateTime)

            // $sql = "SELECT t.Sensor, t.GroupNum, t.StartDateTime, t.EndDateTime, t.MinTemperature, t.MaxTemperature, t.AvgTemperature
            // FROM (
            //   SELECT Sensor, FLOOR((@row_number:=@row_number+1)/".$x.") AS GroupNum, MIN(DateTime) AS StartDateTime, MAX(DateTime) AS EndDateTime, 
            //     MIN(Temperature) AS MinTemperature, MAX(Temperature) AS MaxTemperature, ROUND(AVG(Temperature),2) AS AvgTemperature 
            //   FROM ". $table. ", (SELECT @row_number:=0) AS t 
            //   WHERE Sensor IN (?) AND DateTime BETWEEN ? AND ? 
            //   GROUP BY Sensor
            // ) t
            // WHERE t.GroupNum = 0
            // ORDER BY t.Sensor DESC;";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $sensor, $dateTimeStart, $dateTimeEnd);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>Sensor</th><th>GroupNum</th><th>Start DateTime</th><th>End DateTime</th><th>Min Temperature</th><th>Max Temperature</th><th>Avg Temperature</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $sensor . "</td>";
                    echo "<td>" . $row["GroupNum"] . "</td>";
                    echo "<td>" . $row["StartDateTime"] . "</td>";
                    echo "<td>" . $row["EndDateTime"] . "</td>";
                    echo "<td>" . $row["MinTemperature"] . "</td>";
                    echo "<td>" . $row["MaxTemperature"] . "</td>";
                    echo "<td>" . $row["AvgTemperature"] . "</td>";
                    echo "</tr>";

                    $temp[] = $row['AvgTemperature'];
                    $date[] = $row['EndDateTime'];

                    $allArrays[] = array(
                        'label' => $sensor,
                        'temp' => $temp,
                        'date' => $date
                    );
                }
                echo "</table>";
                
            }
        }

        $data = json_encode($allArrays);
        echo '<canvas id="myChart"></canvas>
        <script>
            var allArrays = ' . $data . ';
            var datasets = [];
            for (var i = 0; i < allArrays.length; i++) {
                var data = allArrays[i].temp.map(Number);
                var labels = allArrays[i].date;
                datasets.push({
                    label: allArrays[i].label,
                    data: data,
                    borderColor: getRandomColor(),
                    fill: false
                });
            }
    
            new Chart("myChart", {
                type: "line",
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    legend: {display: true}
                }
            });
    
            function getRandomColor() {
                var letters = "0123456789ABCDEF";
                var color = "#";
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }
        </script>';
    }
?>