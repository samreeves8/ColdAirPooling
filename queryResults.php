<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/nav.css">
    <link rel = "stylesheet" href = "styles/query.css">
    <link rel = "stylesheet" href = "styles/table.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <!-- script to create tabs -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        // get all tab links and panels
        var tabLinks = document.querySelectorAll('.tab-list a');
        var tabPanels = document.querySelectorAll('.tab-panel');

        // set the first tab to active
        tabLinks[0].classList.add('active');
        tabPanels[0].classList.add('active');

        // add click event listeners to tab links
        tabLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
            e.preventDefault();

            // remove active class from all tabs
            tabLinks.forEach(function(link) {
                link.classList.remove('active');
            });

            // add active class to clicked tab
            link.classList.add('active');

            // hide all panels
            tabPanels.forEach(function(panel) {
                panel.classList.remove('active');
            });

            // show clicked panel
            var targetPanelId = link.getAttribute('href').slice(1);
            var targetPanel = document.getElementById(targetPanelId);
            targetPanel.classList.add('active');
            });
        });
        });
    </script>
    <title>Query Results</title>
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
         </ul>
    </div>
</body>
</html>





<?php
    //predefine arrays used for Query/graphs
    $temps = array();  
    $dates = array();
    $humidity = array("01OBS", "10NEM", "17WIL", "21ALM", "24CAM", "29CAB");

    if($_SERVER['REQUEST_METHOD']==='POST'){
        
        //connect to database
        $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        //gather variables from post request, used in query params
        $sensorSet = json_decode($_POST['sensor-set-input']);
        $val = $_POST['interval'];
        $dateStart = $_POST['dateStart'];
        $dateEnd = $_POST['dateEnd'];
        $timeStart = $_POST['timeStart'];
        $timeEnd = $_POST['timeEnd'];
        $dateTimeStart = $dateStart . ' '.$timeStart;
        $dateTimeEnd = $dateEnd . ' ' . $timeEnd;
        $minute = false;
        $hour = false;

        //set interval value either in minutes or hours
        if ($val !== null) {
            if($val == "3 Minutes"){
                $x = 3;
                $minute =  true;
            } else if($val == "6 Minutes"){
                $x = 6;
                $minute = true;
            } else if($val == "15 Minutes"){
                $x = 15;
                $minute = true;
            } else if($val == "30 Minutes"){
                $x = 30;
                $minute = true;
            } else if($val == "1 Hour"){
                $x = 1;
                $hour = true;
            } else if($val == "2 Hours"){
                $x = 2;
                $hour = true;
            } else if($val == "4 Hours"){
                $x = 4;
                $hour = true;
            } else if($val == "12 Hours"){
                $x = 12;
                $hour = true;
            } else if($val == "Daily"){
                $x = 24;
                $hour = true;
            } else if($val == "Bi-Daily"){
                $x = 48;
                $hour = true;
            } else if($val == "Weekly"){
                $x = 168;
                $hour = true;
            } else if($val == "Bi-Weekly"){
                $x = 336;
                $hour = true;
            }
        }
       
        //code to display table and graph
        $allArrays = array();
        $longestDateArray = array();

        foreach($sensorSet as $sensor){
            //Determine which table to query 
            $table = null;
            if(in_array($sensor, $humidity)){
                $table = "HumidData";
            }else{
                $table = "TempData";
            }

            //Determine which query to use based on minute or hour intervals
            if($minute == true){
                $sql = "SELECT Sensor, DATE_FORMAT(DATE_ADD(dateTime, INTERVAL MOD(MINUTE(dateTime), ?) MINUTE), '%Y-%m-%d %H:%i:00') AS DateTime,
                , FORMAT(AVG(temperature * 1.8 + 32), 2) AS Temperature
                FROM ".$table." WHERE Sensor = ? AND dateTime BETWEEN ? AND ?
                GROUP BY Sensor, TIMESTAMPDIFF(MINUTE, '2000-01-01 00:00:00', dateTime) / ? 
                HAVING MOD(TIMESTAMPDIFF(MINUTE, '2000-01-01 00:00:00', dateTime), 60 / ?) = 0 
                ORDER BY DateTime ASC;
                ";

            } else if($hour == true){
                $sql = "SELECT Sensor, DATE_FORMAT(dateTime, '%Y-%m-%d %H:00:00') AS DateTime, FORMAT(AVG(temperature * 1.8 + 32), 2) AS Temperature
                FROM ".$table." WHERE Sensor = ? AND dateTime BETWEEN ? AND ?
                GROUP BY Sensor, TIMESTAMPDIFF(HOUR, '2000-01-01 00:00:00', dateTime) DIV ? 
                HAVING MOD(TIMESTAMPDIFF(HOUR, '2000-01-01 00:00:00', dateTime), ?) = 0 
                ORDER BY DateTime ASC;";
            }    

            //prepare the query to prevent sql injection
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdd", $sensor, $dateTimeStart, $dateTimeEnd, $x, $x);
            $stmt->execute();
            $result = $stmt->get_result();
            $temp = array();
            $date = array();


            //display each row in the table
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $dateTime = new DateTime($row["DateTime"]);
                    $roundedDateTime = clone $dateTime; // create a copy of the original DateTime object
                    $roundedDateTime->modify('-' . $roundedDateTime->format('i') % 5 . ' minutes'); // round down to nearest 5th minute
                    $formattedDateTime = $roundedDateTime->format('M d, Y h:i a');
                    $temp[] = $row['Temperature'];
                    $date[] = $formattedDateTime;

                }
                // Check if this array of dates is longer than the previous longest array
                if (count($date) > count($longestDateArray)) {
                    $longestDateArray = $date;
                }
                //add each row to array for graph
                $allArrays[] = array(
                    'label' => $sensor,
                    'temp' => $temp,
                    'date' => $longestDateArray
                );
            }
        }

        //Code to display graph
        if (empty($allArrays)) {
            echo "No Data Found";
        } else {
            $data = json_encode($allArrays);
            
            echo '<canvas id="myChart"></canvas>';
            echo'<script>
            var allArrays = '.$data.';
            var datasets = [];
            var longestDateArrayLength = 0;
            var longestDateArray = [];
            
            // Find the longest date array and create an array of all possible dates
            for (var i = 0; i < allArrays.length; i++) {
                var labels = allArrays[i].date;
                if (labels.length > longestDateArrayLength) {
                    longestDateArrayLength = labels.length;
                    longestDateArray = labels;
                }
            }
            for (var i = 0; i < allArrays.length; i++) {
                var data = allArrays[i].temp.map(Number);
                var labels = allArrays[i].date;
                var paddedData = [];
                var j = 0;
                // Pad the smaller temperature array with nulls based on the differences in dates
                for (var k = 0; k < longestDateArray.length; k++) {
                    console.log(allArrays[i].label);
                    console.log(labels[j]);
                    console.log(longestDateArray[k]);
                    console.log(labels[j] == longestDateArray[k]);
                    if (labels[j] == longestDateArray[k]) {
                        paddedData.push(data[j]);
                        j++;
                    } else {
                        paddedData.push(null);
                    }
                }
                datasets.push({
                    label: allArrays[i].label,
                    data: paddedData,
                    borderColor: getRandomColor(),
                    fill: false
                });
            }
            
            new Chart("myChart", {
                type: "line",
                data: {
                    labels: longestDateArray,
                    datasets: datasets
                },
                options: {
                    legend: {
                        display: true
                    },
                    scales: {
                        xAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: "DateTime"
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: "Temperature"
                            }
                        }]
                    }
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
    

            echo "<div class='tab-container'>
                <ul class='tab-list'> ";

            foreach ($sensorSet as $sensor){
                echo "<li><a href='#$sensor'>$sensor</a></li>";
            }
            echo "</ul>";
            foreach ($sensorSet as $sensor){
            echo "<div id='$sensor' class='tab-panel'>
                <table>
                <tr><th>Sensor</th><th>Start DateTime</th><th>Average Temperature (F)</th></tr>";
                
                //Determine which table to query 
                $table = null;
                if(in_array($sensor, $humidity)){
                    $table = "HumidData";
                }else{
                    $table = "TempData";
                }

                //Determine which query to use based on minute or hour intervals
                if($minute == true){
                    $sql = "SELECT Sensor, DATE_FORMAT(dateTime, '%Y-%m-%d %H:%i:00') AS DateTime, FORMAT(AVG(temperature * 1.8 + 32), 2) AS Temperature
                    FROM ".$table." WHERE Sensor = ? AND dateTime BETWEEN ? AND ?
                    GROUP BY Sensor, TIMESTAMPDIFF(MINUTE, '2000-01-01 00:00:00', dateTime) DIV ? ORDER BY DateTime ASC;";
                } else if($hour == true){
                    $sql = "SELECT Sensor, DATE_FORMAT(dateTime, '%Y-%m-%d %H:00:00') AS DateTime, FORMAT(AVG(temperature * 1.8 + 32), 2) AS Temperature
                    FROM ".$table." WHERE Sensor = ? AND dateTime BETWEEN ? AND ?
                    GROUP BY Sensor, TIMESTAMPDIFF(HOUR, '2000-01-01 00:00:00', dateTime) DIV ? ORDER BY DateTime ASC;";
                }    

                //prepare the query to prevent sql injection
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssd", $sensor, $dateTimeStart, $dateTimeEnd, $x);
                $stmt->execute();
                $result = $stmt->get_result();
                $temp = array();
                $date = array();

                //display each row in the table
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $sensor . "</td>";

                        $dateTime = new DateTime($row["DateTime"]);
                        $roundedDateTime = clone $dateTime; // create a copy of the original DateTime object
                        $roundedDateTime->modify('-' . $roundedDateTime->format('i') % 5 . ' minutes'); // round down to nearest 5th minute
                        $formattedDateTime = $roundedDateTime->format('M d, Y h:i a');

                        echo "<td>" . $formattedDateTime . "</td>";
                        echo "<td>" . $row["Temperature"] . "</td>";
                        echo "</tr>";

                        $temp[] = $row['Temperature'];
                        $date[] = $formattedDateTime;
                    }
                }
                echo "</table></div>";
            }
            echo "</div>";
        }
    }
?>
