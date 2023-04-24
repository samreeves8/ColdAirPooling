<?php
    session_start();
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/nav.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel = "stylesheet" href = "styles/query.css">
    <title>Document</title>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'navBar.php';?>

    <script>

        var markers = [];
        window.onload = function() {
            // create XMLHttpRequest object
            var xhr = new XMLHttpRequest();
            
            // handle response from PHP script
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    markers = JSON.parse(xhr.responseText);
                }
            };

            // send GET request to PHP script that returns the array
            xhr.open('GET', 'sensorMarkers.php', true);
            xhr.send();
            
        }

        function submitDates() {
        
            var dateStart = document.getElementById("dateStart").value;
            var timeStart = document.getElementById("timeStart").value;
            var dateEnd = document.getElementById("dateEnd").value;
            var timeEnd = document.getElementById("timeEnd").value;
            console.log("Start datetime:", dateStart, timeStart);
            console.log("End datetime:", dateEnd, timeEnd);
            var startDateTime = new Date(dateStart + " " + timeStart);
            var endDateTime = new Date(dateEnd + " " + timeEnd);
            var timeDiff = endDateTime.getTime() - startDateTime.getTime();
            console.log(timeDiff);
            if (timeDiff <= 0) {
                console.log("Start date is greater than end date");
                return;
            }
            var rangeArr;

            // Check if there is an existing select element on the page
            var existingSelectElem = document.getElementById("range");
            if (existingSelectElem) {
                // If there is an existing select element, remove it
                existingSelectElem.remove();
            }

            var rangeArr;
            switch (true) {
                case (timeDiff <= 10800000): // Less than 3 hours
                    console.log("Less than 3 hours");
                    rangeArr = ['3 Minutes', '6 Minutes', '15 Minutes', '30 Minutes'];
                    break;
                case (timeDiff <= 21600000): // Between 3 hours and 6 hours
                    console.log("Between 3 hours and 6 hours");
                    rangeArr = ['3 Minutes', '6 Minutes', '15 Minutes', '30 Minutes', '1 Hour'];
                    break;
                case (timeDiff <= 86400000): // Between 6 hours and 1 day
                    console.log("Between 6 hours and 1 day");
                    rangeArr = ['3 Minutes', '6 Minutes', '15 Minutes', '30 Minutes', '1 Hour', '2 Hours'];
                    break;
                case (timeDiff <= 518400000): // Between 1 day and 1 week
                    console.log("Between 1 day and 1 week");
                    rangeArr = ['15 Minutes', '30 Minutes', '1 Hour', '2 Hours', '4 Hours', '12 Hours', 'Daily'];
                    break;
                case (timeDiff <= 5266800000): // Between 1 week and 2 months
                    console.log("Between 1 week and 2 months");
                    rangeArr = ['1 Hour', '4 Hours', '12 Hours', 'Daily', 'Bi-Daily', 'Weekly'];
                    break;
                case (timeDiff <= 31536000000): // Between 2 months and 1 year
                    console.log("Between 2 months and 1 year");
                    rangeArr = ['Daily', 'Bi-Daily', 'Weekly', 'Monthly'];
                    break;
                case (timeDiff <= 63158400000): // Between 1 year and 2 years
                    console.log("Between 1 year and 2 years");
                    rangeArr = ['Weekly', 'Bi-Weekly', 'Monthly'];
                    break;
                default: // Greater than 2 years
                    console.log("Greater than 2 years");
                    rangeArr = ['Monthly', 'Yearly'];
                    break;
            }


            var parent = document.getElementById('buttondrop');
            // Create dropdown menu using rangeArr
            var selectElem = document.createElement("select");
            selectElem.id = "range";
            var defaultOptionElem = document.createElement("option");
            defaultOptionElem.value = "";
            defaultOptionElem.disabled = true;
            defaultOptionElem.selected = true;
            defaultOptionElem.text = "Select an option";
            selectElem.appendChild(defaultOptionElem);
            for (var i = 0; i < rangeArr.length; i++) {
                var optionElem = document.createElement("option");
                optionElem.value = i;
                optionElem.text = rangeArr[i];
                selectElem.appendChild(optionElem);
            }

            parent.appendChild(selectElem);
            selectElem.onchange = rangeSelected;
        }

        function rangeSelected() {
            const mySelect = document.getElementById('range');
            
            const selectedOption = mySelect.options[mySelect.selectedIndex];
            
            const val = selectedOption.value;
            console.log(val);
            const selectedRange = selectedOption.text;
            console.log(selectedRange);
            document.getElementById('interval').value = selectedRange;
    
            document.getElementById('myForm').submit();
        }
    </script>
    
    <form  id="myForm" action = 'queryResults.php' method = 'POST'>
        <?php
            include ("queryIndexOne.php");
        ?>
        <script>
            //Adds each sensor to map
            // create XMLHttpRequest object
            var xhr = new XMLHttpRequest();
            
            // handle response from PHP script
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    markers = JSON.parse(xhr.responseText);
                    markers.forEach(marker => {
                        addMarker(marker.id, marker.lat, marker.lng, marker.elevation, marker.dateInstalled, marker.recordsHumidity, mymap);
                    });
                }
            };

            // send GET request to PHP script that returns the array
            xhr.open('GET', 'sensorMarkers.php', true);
            xhr.send();
        
        </script>
        <div class = "form">
        <h1> Insert date and time range for data you want to see: </h1>
        <label for="dateStart">Select a start date:</label>
        <input type="date" id="dateStart" name="dateStart">
        <label for="timeStart">Select a start time:</label>
        <input type="time" id="timeStart" name="timeStart" value = "00:00">
        <br>
        <label for="dateEnd">Select an end date:</label>
        <input type="date" id="dateEnd" name="dateEnd">
        <label for="timeEnd">Select an end time:</label>
        <input type="time" id="timeEnd" name="timeEnd" value = "00:00">
        <input type="hidden" id="interval" name="interval" value="3 Minutes">
        <br>
        <div id = "buttondrop">
        <button type="button" onclick="submitDates()">Submit</button>
        </div>
    </form>
    
</body>
</html>

<?php
    
?>

