<!DOCTYPE html>
<html>
  <head>
    <title>Temperature Sensor Readings</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
        
  </head>
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

        foreach($sensors as $sensor){
            $temp = array();
            $date = array();

            $sql = "SELECT Temperature, DateTime FROM TempData WHERE Sensor = ? AND DateTime BETWEEN ? AND ?";
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
        echo '<canvas id="myChart"></canvas>
        <script>
            var datasets = [];
            for (var i = 0; i < allArrays.length; i++) {
                var data = allArrays[i]['temp'].map(Number);
                var labels = allArrays[i]['date'];
                datasets.push({
                    label: allArrays[i]['label'],
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

  <!-- <body>
    <canvas id="myChart"></canvas>
    <script>
        var datasets = [];
        for (var i = 0; i < allArrays.length; i++) {
            var data = allArrays[i]['temp'].map(Number);
            var labels = allArrays[i]['date'];
            datasets.push({
                label: allArrays[i]['label'],
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
    </script>
  </body> -->

