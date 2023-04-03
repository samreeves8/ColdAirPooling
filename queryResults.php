<?php
    if (isset($_POST['val'])){
        $val = isset($_POST['val']) ? $_POST['val'] : null;
        $dateTimeStart = $_POST['dateTimeStart'];
        $dateTimeEnd = $_POST['dateTimeEnd'];
        $serializedArray = $_POST['sensors'];
        $unserializedArray = unserialize($serializedArray);
        
        if ($val !== null) {
          // Do something with the value
          echo "The value is: " . $val;
        }

        foreach($unserializedArray as $sensor){
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
?>