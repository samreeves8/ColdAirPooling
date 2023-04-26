<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/Western Logo.png">
    <title>Home</title>
    <link rel="stylesheet" href="styles/nav.css">
    <link rel = "stylesheet" href = "styles/query.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'navBar.php';?>
</body>
</html>

<?php

    //get's connection to database
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if($_SERVER['REQUEST_METHOD']==='GET'){
        $sensor =  $_GET['sensor'];
        $sql = "SELECT * FROM SensorData WHERE sensor = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $sensor);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $lat = $row['Latitude'];
                $lon = $row['Longitude'];
                $elevation = $row['Elevation'];
                $date = $row['Date'];
                $humidity = $row['humidity'];
                $picture = $row['picture'];
                $description = $row['Description'];
            }
        }
        echo"<h2>".$sensor."</h2>
        <div style='display: flex; flex-wrap: wrap;'>
          <div style='flex: 1; margin-right: 20px;'>
            <img src=".$picture." style='width: 100%; height: auto;'>
          </div>
          <div style='flex: 2;'>
            <h3 style='margin-top: 0;'>Elevation: ". $elevation ."</h3>
            <p>Latitude: ".$lat."</p>
            <p>Longitude: " . $lon . "</p>
            <p>Date Installed: ". $date ."</p>
            <p>".$description."</p>
          </div>
        </div>";
    }
?>