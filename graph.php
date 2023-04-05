<?php
session_start();

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Temperature Sensor Readings</title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
        <link rel="stylesheet" href="nav.css">
    </head>
    <body>
    <div class="navbar">
         <ul class="menu">
            <li><a href="/">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
            <?php
            // Check if the user is logged in
            if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 1) {
            // If the user is logged in, show the link to the update page
            echo '<li><a href="importCSV.php">Import CSV</a></li>';
            }
        ?>
            <li><a href="query.php">Query</a></li>
            <li><a href="#">Members</a></li>
            <li><a href="login.php">Log In</a></li>
         </ul>
    </div>
    </body>
</html>
<?php

    $temps = array();  
    $dates = array();

    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT DISTINCT Sensor FROM SensorData";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<form action="graph.php" method="POST">';
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
    <input type="submit" value="Submit"></form>';

    if($_SERVER['REQUEST_METHOD']==='POST'){
        $sensors = isset($_POST['sensors']) ? $_POST['sensors'] : array();
        $dateStart = $_POST['dateStart'];
        $dateEnd = $_POST['dateEnd'];
        $timeStart = $_POST['timeStart'];
        $timeEnd = $_POST['timeEnd'];
        $dateTimeStart = $dateStart . ' '.$timeStart;
        $dateTimeEnd = $dateEnd . ' ' . $timeEnd;

        $allArrays = array();

        $humidity = array("01OBS", "10NEM", "17WIL", "21ALM", "24CAM", "29CAB");

        foreach($sensors as $sensor){
            $temp = array();
            $date = array();

            if(in_array($sensor, $humidity)){
                $table = "HumidData";
            }else{
                $table = "TempData";
            }

            $sql = "SELECT Temperature, DateTime FROM " . $table . " TempData WHERE Sensor = ? AND DateTime BETWEEN ? AND ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $sensor, $dateTimeStart, $dateTimeEnd);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
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


