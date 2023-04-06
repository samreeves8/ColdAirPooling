<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <title>Document</title>
</head>
<body>
    <div class="navbar">
        <ul class="menu">
            <li><a href="/">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="query.php">Query</a></li>
            <li><a href="#">Members</a></li>
            <?php
            if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 1) {
                echo '<li><a href="logout.php">Logout</a></li>';
            } else {
                echo '<li><a href="login.php">Login</a></li>';
            }
            ?>         
        </ul>
    </div>
    <script>
            function rangeSelected() {
                const mySelect = document.getElementById('range');
                const selectedOption = mySelect.options[mySelect.selectedIndex];
                const val = selectedOption.value;
                const selectedRange = selectedOption.text;
                document.getElementById('valField').value = selectedRange;
    
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
        </script>
</body>
</html>

<?php
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo "<form action = 'query.php' method = 'POST'>";


    include("queryIndexOne.html");

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
    

    echo '<input type="submit" value="Submit"></form>';
    
    
    if($_SERVER['REQUEST_METHOD']==='POST'){
        $sensors = isset($_POST['sensor-set-input']) ? $_POST['sensor-set-input'] : array();
        echo "<script>console.log(".json_encode($sensors).");</script>";
        
        $dateStart = $_POST['dateStart'];
        $dateEnd = $_POST['dateEnd'];
        $timeStart = $_POST['timeStart'];
        $timeEnd = $_POST['timeEnd'];
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
            $rangeArr = array('3 Minutes', '6 Minutes', '15 Minutes', '30 Minutes', '1 Hour');
        }else if($timedif <= 86400){
            echo "Between 6 hours and 1 day";
            $rangeArr = array('3 Minutes', '6 Minutes', '15 Minutes', '30 Minutes', '1 Hour', '2 Hours');
        }else if($timedif <= 604800){
            echo "Between 1 day and 1 week";
            $rangeArr = array('15 Minutes','30 Minutes', '1 Hour', '2 Hours', '4 Hours', '12 Hours', 'Daily');
        }else if($timedif <= 5184000){
            echo "Between 1 week and 2 months";
            $rangeArr = array('1 Hour', '4 Hours', '12 Hours', 'Daily', 'Bi-Daily', 'Weekly');
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

        $serializedArray = serialize($sensors);
        
        echo "<script>console.log(".json_encode($serializedArray).");</script>";
    
        echo "<form id = 'rangeForm' action='queryResults.php' method='POST'><br><select id = 'range' style = 'font-size: 24px;' onchange='rangeSelected()'>";
        echo "<option value='' disabled selected>Select an option</option>";
        $counter = 0;
        foreach($rangeArr as $currRange){
            echo "<option value = '" . $counter . "'>" . $currRange . "</option>";
            $counter += 1;
        }
        echo "</select>";
        echo "<input type='hidden' name='sensors' value='$serializedArray'>";
        echo "<input type='hidden' name='dateTimeStart' value='$dateTimeStart'>";
        echo "<input type='hidden' name='dateTimeEnd' value='$dateTimeEnd'>";
        echo "<input type='hidden' name='val' id='valField' value='$val'>";
        echo "</form>";

    
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
        // startDate and endDate are variable
    
?>
