<?php
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT DISTINCT Sensor FROM SensorData"; // only select unique sensor names
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<form>';
        while($row = $result->fetch_assoc()) {
            echo '<label><input type="checkbox" name="sensors[]" value="' . $row['Sensor'] . '">' . $row['Sensor'] . '</label><br>';
        }
        echo '<input type="submit" value="Submit"></form>';
    } else {
        echo "0 results";
    }
?>


<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/smoothness/jquery-ui.css">
</head>


<label for="dateStart">Select a start date:</label>
<input type="date" id="dateStart" name="dateStart">

<label for="timeStart">Select a start time:</label>
<input type="time" id="timeStart" name="timeStart">

<br>

<label for="dateEnd">Select an end date:</label>
<input type="date" id="dateEnd" name="dateEnd">

<label for="timeEnd">Select an end time:</label>
<input type="time" id="timeEnd" name="timeEnd">




