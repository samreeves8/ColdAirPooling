<?php
    session_start();
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== 1) {
        // user is not logged in, redirect to login page
        echo "<script>location.href='login.php';</script>";
      
        exit;
      }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/Western Logo.png">
    <link rel="stylesheet" href="styles/nav.css">
    <link rel="stylesheet" href="styles/delete.css">
    <link rel="stylesheet" href="styles/import.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <title>Delete</title>
</head>
<body>
    <?php include 'header.php';?>
    <?php include 'navBar.php';?> 

    
    
    <form id="myForm" action="delete.php" method="POST">
  <div class="form">
    <h1>Select Sensors and time range for data you want to <span id="DELETE">Delete</span>:</h1>
    <?php
    $sensorList = array();
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT DISTINCT Sensor FROM SensorData";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sensorList[] = $row['Sensor'];
        }
    }
    ?>

    <label for="dateStart">Select a start date:</label>
    <input type="date" id="dateStart" name="dateStart">

    <label for="timeStart">Select a start time:</label>
    <input type="time" id="timeStart" name="timeStart" value="00:00">

    <br>

    <label for="dateEnd">Select an end date:</label>
    <input type="date" id="dateEnd" name="dateEnd" value="<?php echo date('Y-m-d'); ?>">

    <label for="timeEnd">Select an end time:</label>
    <input type="time" id="timeEnd" name="timeEnd" value="00:00">

    <br>
    <br>

    <div class="checkbox-container">
    <?php foreach ($sensorList as $sensor) { ?>
    <label>
      <input type="checkbox" name="sensors[]" value="<?php echo $sensor; ?>">
      <?php echo $sensor; ?>
    </label>
    <?php } ?>
    </div>

    <input type="hidden" id="interval" name="interval" value="3 Minutes">
    <div id="buttondrop">
      <input type="submit" value="Submit">
    </div>
  </div>
</form>


    
</body>
</html>
<?php 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sensorSet = $_POST['sensors'];
    $dateStart = $_POST['dateStart'];
    $dateEnd = $_POST['dateEnd'];
    $timeStart = $_POST['timeStart'];
    $timeEnd = $_POST['timeEnd'];
    $dateTimeStart = $dateStart . ' '.$timeStart;
    $dateTimeEnd = $dateEnd . ' ' . $timeEnd;

    $humidity = array();
    //gather sensors that gather humidity
    $humiditySQL = "SELECT DISTINCT Sensor from HumidData";
    $stmt = $conn->prepare($humiditySQL);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $humidity[] = $row['Sensor'];
        }
    }

    foreach($sensorSet as $sensor){
        //Determine which table to query 
        $table = null;
        if(in_array($sensor, $humidity)){
            $table = "HumidData";
        }else{
            $table = "TempData";
        }
        $sql = "DELETE FROM ".$table." WHERE Sensor = ? AND dateTime BETWEEN ? AND ?;";
        //prepare the query to prevent sql injection
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $sensor, $dateTimeStart, $dateTimeEnd);
        $stmt->execute();

        $numRowsAffected = $stmt->affected_rows;
        if ($numRowsAffected > 0) {
            echo "Deletion for sensor " . $sensor . " was successful. " . $numRowsAffected . " rows were affected.<br>";
        } else {
            echo "Deletion for sensor " . $sensor . " was unsuccessful.<br>";
        }
    }
}
?>
