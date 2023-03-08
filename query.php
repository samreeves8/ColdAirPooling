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
        $sensors = $_POST['sensors'];
        $dateStart = $_POST['dateStart'];
        $dateEnd = $_POST['dateEnd'];
        $timeStart = $_POST['timeStart'];
        $timeEnd = $_POST['timeEnd'];
<<<<<<< HEAD
        $tempMin = $_POST['tempMin'];
        $tempMax = $_POST['tempMax'];
        $dateTimeStart = $dateStart . ' '.$timeStart;
        $dateTimeEnd = $dateEnd . ' ' . $timeEnd;
=======
        $tempMin = $_POST['tempStart'];
        $tempMax = $_POST['tempEnd'];
>>>>>>> 98861ddd89bd5ede25dff2b66b2c1803b40f311e
        
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
        }
        
        foreach($sensors as $sensor){
<<<<<<< HEAD
            $sql = "SELECT Temperature FROM ".$table." WHERE Sensor IS ".$sensor." AND Datetime BETWEEN ".$dateTimeStart." AND ".$dateTimeEnd." AND Temperature BETWEEN ".$tempMin." AND " .$tempMax."";
            echo $sql;
        }
        
        
        
        
=======
            $sql = "SELECT Temperature FROM ".$table." WHERE Sensor IS ".$sensor." AND Datetime BETWEEN ".$dateTimeStart." AND ".$dateTimeEnd." AND "..;
        }
        
        // comment
        WHERE Sensor IS NULL 
        AND Date BETWEEN '2022-01-01' AND '2022-01-31'
        AND TIME(Date) BETWEEN '08:00:00' AND '18:00:00'
        AND Temperature BETWEEN 20 AND 30
>>>>>>> 98861ddd89bd5ede25dff2b66b2c1803b40f311e

    }
    

    
?>









