<?php

$conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

$humidity = array("01OBS", "10NEM", "17WIL", "21ALM", "24CAM", "29CAB");  // Humidity Sensors
    
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
                  echo "Invalid file format";
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
  // close the file handle
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>File Upload with Progress Bar</title>
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
    var totalProgress = 0;
    var totalSize = 0; // Initialize the total size of uploaded files to zero
    for (var i = 0; i < files.length; i++) {
      totalSize += files[i].size; // Add the size of each file to the total size
      var fileData = new FormData();
      fileData.append('file', files[i]);
      $.ajax({
        url: 'fileUpload.php',
        type: 'POST',
        data: fileData,
        processData: false,
        contentType: false,
        xhr: function() {
          var xhr = new window.XMLHttpRequest();
          xhr.upload.addEventListener('progress', function(event) {
            if (event.lengthComputable) {
              var currentProgress = event.loaded / event.total * 100; // Calculate the progress of the current file
              totalProgress += currentProgress; // Update the total progress
              var overallProgress = totalProgress / files.length; // Calculate the overall progress
              if (overallProgress > 100) {
                overallProgress = 100; // Limit progress to 100%
              }
              $('#status').html('Overall Progress: ' + overallProgress.toFixed(2) + '%'); // Update the progress bar
            }
          }, false);
          return xhr;
        }
      });
    }
  });
});




  </script>
</head>
<body>
<div id="form">
  <form action="fileUpload.php" method="post" enctype="multipart/form-data" id="upload-form">
    <input type="file" name="files[]" multiple>
    <input type="submit" value="Upload">
  </form>
</div>

<div id="status">

</div>
</body>
</html>

