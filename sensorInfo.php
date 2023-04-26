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
        echo $sensor;
    }
?>