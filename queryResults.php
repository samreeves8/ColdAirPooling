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

    $temps = array();  
    $dates = array();
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
        $minute = false;
        $hour = false;

        if ($val !== null) {
            if($val == "3 Minutes"){
                $x = 3;
                $minute =  true;
            } else if($val == "6 Minutes"){
                $x = 6;
                $minute = true;
            } else if($val == "15 Minutes"){
                $x = 15;
                $minute = true;
            } else if($val == "30 Minutes"){
                $x = 30;
                $minute = true;
            } else if($val == "1 Hour"){
                $x = 1;
                $hour = true;
            } else if($val == "2 Hours"){
                $x = 2;
                $hour = true;
            } else if($val == "4 Hours"){
                $x = 4;
                $hour = true;
            } else if($val == "12 Hours"){
                $x = 12;
                $hour = true;
            } else if($val == "Daily"){
                $x = 24;
                $hour = true;
            } else if($val == "Bi-Daily"){
                $x = 48;
                $hour = true;
            } else if($val == "Weekly"){
                $x = 168;
                $hour = true;
            } else if($val == "Bi-Weekly"){
                $x = 336;
                $hour = true;
            } else {
                // handle any other cases or errors here
            }
        }

        $allArrays = array();
        echo "<table>";
        echo "<tr><th>Sensor</th><th>DateTime</th><th>Average Temperature (F)</th></tr>";
        foreach($unserializedArray as $sensor){
            $table = null;
            if(in_array($sensor, $humidity)){
                $table = "HumidData";
            }else{
                $table = "TempData";
            }
            
            if($minute == true){
                $sql = "SELECT Sensor, DATE_FORMAT(dateTime, '%Y-%m-%d %H:%i:00') AS DateTime, FORMAT(AVG(temperature * 1.8 + 32), 2) AS Temperature
                FROM ".$table." WHERE Sensor = ? AND dateTime BETWEEN ? AND ?
                GROUP BY Sensor, TIMESTAMPDIFF(MINUTE, '2000-01-01 00:00:00', dateTime) DIV ? ORDER BY DateTime ASC;";
            } else if($hour == true){
                $sql = "SELECT Sensor, DATE_FORMAT(dateTime, '%Y-%m-%d %H:00:00') AS DateTime, FORMAT(AVG(temperature * 1.8 + 32), 2) AS Temperature
                FROM ".$table." WHERE Sensor = ? AND dateTime BETWEEN ? AND ?
                GROUP BY Sensor, TIMESTAMPDIFF(HOUR, '2000-01-01 00:00:00', dateTime) DIV ? ORDER BY DateTime ASC;";
            }    

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssd", $sensor, $dateTimeStart, $dateTimeEnd, $x);
            $stmt->execute();
            $result = $stmt->get_result();

            $temp = array();
            $date = array();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $sensor . "</td>";
                    echo "<td>" . $row["DateTime"] . "</td>";
                    echo "<td>" . $row["Temperature"] . "</td>";
                    echo "</tr>";

                    $temp[] = $row['Temperature'];
                    $date[] = $row['DateTime'];
                }
                $allArrays[] = array(
                    'label' => $sensor,
                    'temp' => $temp,
                    'date' => $date
                );
                
                
            }
        }
        echo "</table>";
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


<!-- // $sql = "SELECT DISTINCT Sensor, FLOOR((@row_number:=@row_number+1)/". $x .") AS GroupNum, Min(DateTime) AS StartDateTime, MAX(DateTime) AS EndDateTime,
            // MIN(Temperature) AS MinTemperature, MAX(Temperature) AS MaxTemperature, ROUND(AVG(Temperature),2) AS AvgTemperature
            // FROM " . $table . ", (SELECT @row_number:=0) AS t WHERE Sensor IN (?) AND DateTime BETWEEN ? AND ? GROUP BY GroupNum ORDER BY `Sensor` DESC;";

            // SET @interval = 24; -- Specify the interval length in hours
            // SELECT Sensor, DATE_FORMAT(dateTime, '%Y-%m-%d %H:00:00') AS interval_start, AVG(temperature) AS average_temperature 
            // FROM TempData WHERE Sensor = '02FAI' AND dateTime BETWEEN '2023-01-01 0:00:00' AND '2023-01-31 0:00:00' 
            // GROUP BY Sensor, TIMESTAMPDIFF(HOUR, '2000-01-01 00:00:00', dateTime) DIV 24 ORDER BY interval_start ASC -->