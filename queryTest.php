<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel = "stylesheet" href = "query.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>
<body>
    
</body>
</html>
<?php
    $currentFormIndex = 0;
    $sensorSet = null;
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $currentFormIndex = $_POST['currentFormIndex'];
        $sensorSet = ($_POST['sensor-set-input']);
        echo "<script>console.log(JSON.parse(json_encode($sensorSet)));</script>";
        
        

        if(isset($_POST['next'])){
            $currentFormIndex++;

        }elseif(isset($_POST['previous'])){
            $currentFormIndex--;
        }

        
        if($currentFormIndex>0){
            $sensorSet = json_decode($_POST['sensor-set-input']);
            echo "<script>console.log(JSON.parse(json_encode($sensorSet)));</script>";
        }
    }

    if($currentFormIndex >= 0 && $currentFormIndex < 3){
        
        echo "<form method = 'POST'>";
        if($currentFormIndex == 0){
            include("queryIndexOne.html");
        }else if($currentFormIndex == 1){
            
            echo 
            '<h1> Insert date and time range for data you want to see: </h1>
            <label for="dateStart">Select a start date:</label>
            <input type="date" id="dateStart" name="dateStart" value = "2022-08-16">
            <label for="timeStart">Select a start time:</label>
            <input type="time" id="timeStart" name="timeStart" value = "00:00">
            <br>
            <label for="dateEnd">Select an end date:</label>
            <input type="date" id="dateEnd" name="dateEnd" value="'. date('Y-m-d') .'">
            <label for="timeEnd">Select an end time:</label>
            <input type="time" id="timeEnd" name="timeEnd" value = "00:00">
            <br>';


            echo "<h2>" . $sensorSet . "</h2>";
            

            
        }else if($currentFormIndex == 2){
            echo "<h1> index 3 </h1>";
        }

    
        echo "<input type='hidden' name='currentFormIndex' value='$currentFormIndex'>";
        
        

        if($currentFormIndex > 0){
            echo "<input type = 'submit' name = 'previous' value = 'Previous'>";
            //echo "<input type='hidden' id='sensor-set-input' value='$sensorSet'>";
        }
        if($currentFormIndex < 2){
            echo "<input type = 'submit' name = 'next' value = 'Next'>";
        }else{
            echo "<input type = 'submit' name = 'graph' value = 'Graph'>";
        }
        echo "</form>";
    }

?>