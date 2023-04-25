<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About</title>
  <link rel="icon" type="image/png" href="images/Western Logo.png">
  <link rel = "stylesheet" href = "styles/about.css">
  <link rel="stylesheet" href="styles/nav.css">
</head>

<body>
<?php include 'header.php'; ?>

<?php include 'navBar.php';?>

<div class="about-wrapper">
    <h1>
      About Us
    </h1>
    <p>
      Gunnison, Colorado is frequently one of the coldest inhabited locations in the contiguous United States.
      Those who have lived in the Gunnison Valley for a winter will recognize that some places in the valley 
      are colder than others. This ongoing professional research project uses a set of sensitive temperature 
      data loggers distributed throughout the Gunnison Valley to monitor the formation and spatial extent of 
      these pockets of cold air (called cold-air pools). Through this project we are attempting to identify the 
      larger scale climatological and geographical factors that enhance cold-air pooling.  

      The initial 25 temperature sensors were installed in February 2022. Another five sensors were added in
      October 2022. Six of the sensors also record humidity. All sensors are set to record every three minutes, 
      24 hours a day. The data is downloaded every two months for analysis and display on this website. See the 
      sensor locations here. 
    </p>
</div>
<img src="images/29CAB.JPG" alt="Sensor 29CAB">

<footer>
  <p>    
    This project was made possible by the generous contributions of local donors, grants from 
    Western Colorado University, and local landowners hosting sensors. 
  </p>
</footer>

</body>
</html>