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
      Introduction
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
<div class="imgcontainer">
  <img src="images/29CAB2.JPG" alt="Sensor 29CAB" class="img29">
</div>

<div class="about-wrapper">
  <h1>Cold Air Pooling</h1>
  <p>
    Outside of cold arctic air masses, which can make the larger region bitterly cold, Gunnison is 
    particularly and persistently cold because of its location and geography. Gunnison rests in a high 
    elevation valley and is nearly encircled by mountain ranges between 10,000 and 13,000 ft in elevation. 
    On clear winter nights, and especially those with significant snow cover, radiative cooling or heat loss 
    from the surrounding mountains generates parcels of cold air that are denser than the air in the valley 
    bottoms. This cold, dense air flows downslope, replacing the air in the valleys, and ‘pools’ in the valley 
    bottoms, often creating well stratified temperature inversions where the surface temperatures are colder 
    than the atmosphere above. In the meteorological literature this process is called ‘cold-air-pooling’ 
    (AMS; Burns and Chemil, 2014) and has been well studied in a few locations. These cold air pools and 
    associated temperature inversions can persist for extended periods of time, and in valleys with large 
    atmospheric pollution loads, can lead to significantly diminished air quality.  
  </p>
</div>

<div class="teamcontactwrapper">
<div class="team-wrapper">
  <h1>Team Members</h1>
  <ul>
    <li>Suzanne Taylor, Professor of Physics</li>
    <li>Bruce Bartleson, Professor Emeritus – Geology</li>
    <li>David Marchetti, Professor of Geology</li>
    <li>David Primus, WCU Graduate & Community Member</li>
    <li>Zachary Treisman, Assistant Professor of Mathematics</li>
    <li>Kevin Cabral, Computer Science Student</li>
    <li>Michael Matthews, Computer Science Student</li>
    <li>Sam Reeves, Computer Science Student</li>
    <li>Jacob Vogel, Computer Science Student</li>
    <li>Nathan Zimmerman, Chemistry Student</li>
  </ul>
</div>

<div class="contact-wrapper">
  <div class="contact-info">
    <h1>Contact Us</h1>
    <p>If you are interseted in learning more about our project and research, please reach 
      out to one of the emails listed below for more information. 
    </p>
    <h4>Suzanne Taylor</h4>
    <a href="mailto:mstaylor@western.edu">mstaylor@western.edu</a>
    <br>
    <h4>David Primus</h4>
    <a href="mailto:dprimus@gmail.com">dprimus@gmail.com</a>
  </div>
</div>
</div>
 

<footer>
  <h3>References</h3>
  <p>
    AMS, American Meteorological Society, Glossary of Meteorology, 
    (https://glossary.ametsoc.org/wiki/Welcome), last searched 10/17/2021. 

    Burns, P., & Chemel, C., 2013, Evolution of Cold-Air-Pooling Processes in Complex Terrain. 
    Boundary-Layer Meteorology, 150, 423-447.
  </p>
  <p>    
    This project was made possible by the generous contributions of local donors, grants from 
    Western Colorado University, and local landowners hosting sensors.
  </p>
</footer>

</body>
</html>