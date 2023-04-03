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
          // Do something with the value
          echo "The value is: " . $val;
            if($val == "3 Minutes"){
                $x = 1;
            } else if($val == "6 Minutes"){
                $x = 3;
            } else if($val == "15 Minutes"){
                $x = 6;
            } else if($val == "30 Minutes"){
                $x = 11;
            } else if($val == "1 Hour"){
                $x = 21;
            } else if($val == "2 Hours"){
                $x = 41;
            } else if($val == "4 Hours"){
                $x = 81;
            } else if($val == "12 Hours"){
                $x = 241;
            } else if($val == "Daily"){
                $x = 481;
            } else if($val == "Bi-Daily"){
                $x = 961;
            } else if($val == "Weekly"){
                $x = 3361;
            } else if($val == "Bi-Weekly"){
                $x = 6721;
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

            $sql = "SELECT Sensor, FLOOR((@row_number:=@row_number+1)/". $x .") AS GroupNum, Min(DateTime) AS StartDateTime, MAX(DateTime) AS EndDateTime,
            MIN(Temperature) AS MinTemperature, MAX(Temperature) AS MaxTemperature, ROUND(AVG(Temperature),2) AS AvgTemperature
            FROM " . $table . ", (SELECT @row_number:=0) AS t WHERE Sensor IN (?) AND DateTime BETWEEN ? AND ? GROUP BY Sensor, GroupNum ORDER BY `Sensor` DESC;";
               
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $sensor, $dateTimeStart, $dateTimeEnd);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "Sensor: " . $sensor . ", StartDateTime: " . $row["StartDateTime"] . ", EndDateTime: " . $row["EndDateTime"] . "
                    , MinTemperature: " . $row["MinTemperature"] . ", MaxTemperature: " . $row["MaxTemperature"] . ", AvgTemperature: " . $row["AvgTemperature"]. "<br>";
                }
            }
        }
    }
?>