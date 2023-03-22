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
        $dateTimeStart = $dateStart . ' '.$timeStart;
        $dateTimeEnd = $dateEnd . ' ' . $timeEnd;

        if(isset($dateTimeEnd) && $dateTimeEnd <= $dateTimeStart){
            echo "Error: End date must be after start date";
        }
        
        echo "Start date: " . $dateStart . "<br>";
        echo "Start time: " .$timeStart . "<br>";
        echo "End date: " . $dateEnd . "<br>";
        echo "End time: " . $timeEnd . "<br>";
        echo "Min temp: " . $tempMin . "<br>";
        echo "Max temp: " . $tempMax . "<br>";
        foreach($sensors as $sensor) {
            echo $sensor . "<br>";
        }
        
        $humidity = array("01OBS", "10NEM", "17WIL", "21ALM", "24CAM", "29CAB");
        
        foreach($sensors as $sensor){
            $table = null;
            if(in_array($sensor, $humidity)){
                $table = "HumidData";
            }else{
                $table = "TempData";
            }

            $sqlString = "SELECT Temperature, DateTime FROM " . $table . " WHERE Sensor = ".$sensor ." AND DateTime BETWEEN ".$dateTimeStart." AND ".$dateTimeEnd;
            $sql = "SELECT Temperature, DateTime FROM " . $table . " WHERE Sensor = ? AND DateTime BETWEEN ? AND ? AND Temperature BETWEEN ? AND ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdd", $sensor, $dateTimeStart, $dateTimeEnd, $tempMin, $tempMax);
            
            $stmt->execute();
            echo $sqlString."<br>";
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "Sensor: " . $sensor . ", DateTime: " . $row["DateTime"] . ", Temperature: " . $row["Temperature"] . "<br>"; 
                }
            }
        
        }


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

        echo "<br><select id = 'range', style = 'font-size: 48px;'>";
        $counter = 0;
        foreach($rangeArr as $currRange){
            echo "<option value = '" . $counter . "'>" . $currRange . "</option>";
            $counter += 1;
        }
        echo "</select><br>";


        // $sqlString = 
        // "SELECT Sensor,
        // FLOOR((@row_number:=@row_number+1)/?) AS GroupNum,
        // MIN(DateTime) AS StartDateTime,
        // MAX(DateTime) AS EndDateTime,
        // MIN(Temperature) AS MinTemperature, 
        // MAX(Temperature) AS MaxTemperature, 
        // ROUND(AVG(Temperature),2) AS AvgTemperature
        // FROM ?, (SELECT @row_number:=0) AS t
        // WHERE Sensor IN ("S")
        // AND DateTime BETWEEN ? AND ?
        // GROUP BY Sensor, GroupNum  
        // ORDER BY `Sensor`  DESC";


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


    }
    
?>









