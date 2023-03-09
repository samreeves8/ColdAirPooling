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

        if (strtotime($dateTimeEnd) <= strtotime($dateTimeStart)) {
            echo "End date and time must be after start date and time";
            exit();
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

            $sql = "SELECT Temperature, DateTime FROM " . $table . " WHERE Sensor = ? AND DateTime BETWEEN ? AND ? AND Temperature BETWEEN ? AND ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdd", $sensor, $dateTimeStart, $dateTimeEnd, $tempMin, $tempMax);
            
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "Sensor: " . $sensor . ", DateTime: " . $row["DateTime"] . ", Temperature: " . $row["Temperature"] . "<br>"; 
                }
            }
        
        }
        
        // SELECT 
        // Sensor,
        // FLOOR((@row_number:=@row_number+1)/x) AS GroupNum,
        // MIN(DateTime) AS StartDateTime,
        // MAX(DateTime) AS EndDateTime,
        // MIN(Temperature) AS MinTemperature, 
        // MAX(Temperature) AS MaxTemperature, 
        // ROUND(AVG(Temperature),2) AS AvgTemperature
        // FROM TempData, (SELECT @row_number:=0) AS t
        // WHERE Sensor IN ("S")
        // AND DateTime BETWEEN startDate AND endDate
        // GROUP BY Sensor, GroupNum  
        // ORDER BY `Sensor`  DESC

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









