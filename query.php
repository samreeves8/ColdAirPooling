<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="nav.css">
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

    <label for="tempStart">Select a temperature range (Celcius):</label>
    <input type="number" id="tempMin" name="tempMin">

    <input type="number" id="tempMax" name="tempMax">
    <input type="submit" value="Submit"></form>';
    
    
    if($_SERVER['REQUEST_METHOD']==='POST'){
        $sensors = isset($_POST['sensors']) ? $_POST['sensors'] : array();
        $dateStart = $_POST['dateStart'];
        $dateEnd = $_POST['dateEnd'];
        $timeStart = $_POST['timeStart'];
        $timeEnd = $_POST['timeEnd'];
        $tempMin = $_POST['tempMin'];
        $tempMax = $_POST['tempMax'];
        $val = $_POST['val'] ?? NULL;
        $dateTimeStart = $dateStart . ' '.$timeStart;
        $dateTimeEnd = $dateEnd . ' ' . $timeEnd;

        if(isset($dateTimeEnd) && $dateTimeEnd <= $dateTimeStart){
            echo "Error: End date must be after start date";
        }
        
        $humidity = array("01OBS", "10NEM", "17WIL", "21ALM", "24CAM", "29CAB");
        
        $x = 0;
        $rangeArr = null;
        $timedif = strtotime($dateTimeEnd) - strtotime($dateTimeStart);
        if($timedif <= 0 && ($dateTimeStart != "" && $dateTimeEnd != "")){
            echo "Start date is greater than end date";
            exit();
        }
        if ($timedif <= 10800) {
            echo "Less than 3 hours";
            $rangeArr = array('3 Minutes', '6 Minutes', '15 Minutes', '30 Minutes');
        }else if($timedif <= 21600){
            echo "Between 3 hours and 6 hours";
            $rangeArr = array('6 Minutes', '15 Minutes', '30 Minutes', '1 Hour');
        }else if($timedif <= 86400){
            echo "Between 6 hours and 1 day";
            $rangeArr = array('15 Minutes', '30 Minutes', '1 Hour', '2 Hours');
        }else if($timedif <= 604800){
            echo "Between 1 day and 1 week";
            $rangeArr = array('1 Hour', '2 Hours', '4 Hours', '12 Hours', 'Daily');
        }else if($timedif <= 5184000){
            echo "Between 1 week and 2 months";
            $rangeArr = array('12 Hours', 'Daily', 'Bi-Daily', 'Weekly');
        }else if($timedif <= 31536000){
            echo "Between 2 months and 1 year";
            $rangeArr = array('Daily', 'Bi-Daily', 'Weekly', 'Monthly');
        }else if($timedif <= 63072000){
            echo "Between 1 year and 2 years";
            $rangeArr = array('Weekly', 'Bi-Weekly', 'Monthly');
        }else{
            echo "Greater than 2 years";
            $rangeArr = array('Monthly', 'Yearly');
        }

        echo "<form id = 'rangeForm' action='query.php' method='POST'><br><select id = 'range' style = 'font-size: 24px;' onchange='rangeSelected()'>";
        $counter = 0;
        foreach($rangeArr as $currRange){
            echo "<option value = '" . $counter . "'>" . $currRange . "</option>";
            $counter += 1;
        }
        echo "</select>";
        echo "<input type='hidden' name='sensors' value='$sensors'>";
        echo "<input type='hidden' name='dateStart' value='$dateStart'>";
        echo "<input type='hidden' name='dateEnd' value='$dateEnd'>";
        echo "<input type='hidden' name='timeStart' value='$timeStart'>";
        echo "<input type='hidden' name='timeEnd' value='$timeEnd'>";
        echo "<input type='hidden' name='dateTimeStart' value='$dateTimeStart'>";
        echo "<input type='hidden' name='dateTimeEnd' value='$dateTimeEnd'>";
        echo "<input type='hidden' name='table' value='$table'>";
        echo "<input type='hidden' name='val' id='valField' value='$val'>";
        echo "</form>";

        echo 
        "<script>
            function rangeSelected() {
                const mySelect = document.getElementById('range');
                const val = mySelect.value;
                document.getElementById('valField').value = val;
        
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'query.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
                // Set the POST parameters
                const params = 'val=' + encodeURIComponent(val);
                xhr.send(params);
                console.log(params);
                console.log('sent request');

                document.getElementById('rangeForm').submit();
            }
        </script>";

        if (isset($_POST['val'])){
            $val = isset($_POST['val']) ? $_POST['val'] : null;
            $sensors = isset($_POST['sensors']) ? $_POST['sensors'] : array();
            $dateStart = $_POST['dateStart'];
            $dateEnd = $_POST['dateEnd'];
            $timeStart = $_POST['timeStart'];
            $timeEnd = $_POST['timeEnd'];
            $table = $_POST['table'];
            $dateTimeStart = $dateStart . ' '.$timeStart;
            $dateTimeEnd = $dateEnd . ' ' . $timeEnd;
            if ($val !== null) {
              // Do something with the value
              echo "The value is: " . $val;
            }
    
            foreach($sensors as $sensor){
                $table = null;
                if(in_array($sensor, $humidity)){
                    $table = "HumidData";
                }else{
                    $table = "TempData";
                }
    
                $sqlString = "SELECT Sensor, FLOOR((@row_number:=@row_number+1)/'$val') AS GroupNum, MIN(DateTime) AS StartDateTime, MAX(DateTime) AS EndDateTime, 
                MIN(Temperature) AS MinTemperature, MAX(Temperature) AS MaxTemperature, ROUND(AVG(Temperature),2) AS AvgTemperature 
                FROM $table, (SELECT @row_number:=0) AS t WHERE Sensor IN ('$sensor') AND DateTime BETWEEN '$dateTimeStart' AND '$dateTimeEnd' GROUP BY Sensor, GroupNum  ORDER BY `Sensor`  DESC;";
    
                echo $sqlString;
            }
        }
    
    }
        //x, table, startdatetime, endadatetime

        // 3 minutes - x is 1 or 2
        // 6 minutes - x is 3
        // 15 minutes - x is 6
        // 30 minutes - x is 11
        // 1 hour - x is 21
        // 2 hours - x is 41
        // 4 hours - x is 81
        // 12 hours - x is 241
        // 1 day - x is 481
        // 2 day - x is 961
        // Weekly - x is 3361
        // Bi-Weekly - x is 6721
        // S is sensor
        // startDate and endDate are variables

            // $sqlString = "SELECT Temperature, DateTime FROM " . $table . " WHERE Sensor = ".$sensor ." AND DateTime BETWEEN ".$dateTimeStart." AND ".$dateTimeEnd;
            // $sql = "SELECT Temperature, DateTime FROM " . $table . " WHERE Sensor = ? AND DateTime BETWEEN ? AND ? AND Temperature BETWEEN ? AND ?";
            // $stmt = $conn->prepare($sql);
            // $stmt->bind_param("sssdd", $sensor, $dateTimeStart, $dateTimeEnd, $tempMin, $tempMax);
            
            // $stmt->execute();
            // echo $sqlString."<br>";
            // $result = $stmt->get_result();

            // if ($result->num_rows > 0) {
            //     while ($row = $result->fetch_assoc()) {
            //         echo "Sensor: " . $sensor . ", DateTime: " . $row["DateTime"] . ", Temperature: " . $row["Temperature"] . "<br>"; 
            //     }
            // }
        
        // }
    
?>










