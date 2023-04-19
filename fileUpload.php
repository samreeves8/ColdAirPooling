<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== 1) {
  // user is not logged in, redirect to login page
  echo "<script>location.href='login.php';</script>";

  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/nav.css">
  <link rel="stylesheet" href="styles/import.css">
  <title>File Upload</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script>
    $(document).ready(function() {
      // Bind event listener to the form submission
      $('#upload-form').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData($('#upload-form')[0]);
        var files = formData.getAll('files[]');
        $('#upload-form')[0].reset();
        $('#status').empty();
        var uploadedCount = 0; // Initialize the count of uploaded files to zero
        for (var i = 0; i < files.length; i++) {
          var fileData = new FormData();
          fileData.append('file', files[i]);
          var progressElement = $('<div id="progress' + i + '">Uploading ' + files[i].name + ': 0%</div>'); // Create progress bar element
          $('#status').append(progressElement);
          var xhr = new XMLHttpRequest();
          xhr.open('POST', 'fileUpload.php');
          xhr.upload.addEventListener('progress', (function(progressElement, file) {
            return function(e) {
              if (e.lengthComputable) {
                var percent = Math.round((e.loaded / e.total) * 100);
                progressElement.text('Uploading ' + file.name + ': ' + percent + '%'); // Update progress bar element
              }
            };
          })(progressElement, files[i]), false);
          xhr.addEventListener('error', function() {
            progressElement.text('Error uploading ' + file.name + '. Please try again.'); // Display error message
          });
          xhr.send(fileData);
        }
      });
    });
  </script>
</head>
<body>
<?php include 'navBar.php';?>

<div id="form">
  <form action="fileUpload.php" method="post" enctype="multipart/form-data" id="upload-form">
    <input type="file" id="file" class="file" name="files[]" multiple>
    <input type="submit" id="button" value="Upload">
  </form>
</div>
<div id="status"></div>
</body>
</html>

<?php

$conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

$humidity = array();  // Humidity Sensors

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
    
//Bind indices for excel documents
$dateTimeIndex=1;
$tempIndex = 2;
$rhIndex = 3;
$dewPointIndex = 4;

$Sensor = NULL;
$DateTime = NULL;
$Temperature = NULL;
$RH = NULL;
$DewPoint = NULL;
$h = NULL;

$sql_humidity = "INSERT INTO HumidData (Sensor, DateTime, Temperature, RH, DewPoint) VALUES (?, ?, ?, ?, ?)";
$stmt_humidity = mysqli_prepare($conn, $sql_humidity);
mysqli_stmt_bind_param($stmt_humidity, "ssddd", $Sensor, $DateTime, $Temperature, $RH, $DewPoint);

$sql_temp = "INSERT INTO TempData (Sensor, DateTime, Temperature) VALUES (?, ?, ?)";
$stmt_temp = mysqli_prepare($conn, $sql_temp);
mysqli_stmt_bind_param($stmt_temp, "ssd", $Sensor, $DateTime, $Temperature);

if($_SERVER['REQUEST_METHOD'] == 'POST') {

  // get the local file path
  $local_file = $_FILES["file"]["tmp_name"];

  // get the original file name
  $file_name = $_FILES["file"]["name"];
        
  // open the local file for reading
  $handle = fopen($local_file, "r");

  $Sensor = substr($file_name, 0, 5);
  $humidity = array("01OBS", "10NEM", "17WIL", "21ALM", "24CAM", "29CAB");  // Humidity Sensors

  //Checks which table to access (HumidData or TempData)
  if(in_array($Sensor, $humidity)){
    $stmt = $stmt_humidity;
    $h = true;    
  } else {
    $stmt = $stmt_temp;
    $h = false;
  }

  //Skip the first line
  fgetcsv($handle);

  while (($row = fgetcsv($handle)) !== false) {  
    //Accounts for date time differences
    $DateTime = DateTime::createFromFormat('m/d/Y H:i:s', $row[$dateTimeIndex]);
    if (!$DateTime) {
      $DateTime = DateTime::createFromFormat('m/d/Y H:i', $row[$dateTimeIndex]);
        if (!$DateTime) {
          $DateTime = DateTime::createFromFormat('m-d-Y H:i:s', $row[$dateTimeIndex]);
            if (!$DateTime) {
              $DateTime = DateTime::createFromFormat('m-d-Y H:i', $row[$dateTimeIndex]);
                if (!$DateTime) {
                  echo $file_name . ": Invalid file format";
                  exit();
                }
            }
        }
    }
    // Set the parameter values
    $DateTime = $DateTime->format('Y-m-d H:i:s');
    $Temperature = $row[$tempIndex];
    if($h && $Temperature!=null){
    $RH = $row[$rhIndex];
    $DewPoint = $row[$dewPointIndex];
    mysqli_stmt_execute($stmt);    

    } else if(!$h && $Temperature!=null) {
        mysqli_stmt_execute($stmt);
    }
  }
  // Close the statement and connection
  fclose($handle);
        
  $conn->commit();
  mysqli_stmt_close($stmt);
  
}
mysqli_close($conn);
?>


