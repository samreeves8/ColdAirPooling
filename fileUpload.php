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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // FTP server details
    $ftp_server = "ftp.gunnisoncoldpooling.net";
    $ftp_username = "Admin@gunnisoncoldpooling.net";
    $ftp_password = "14MIA is cold";

    // connect to FTP server
    $conn_id = ftp_connect($ftp_server);

    // login with username and password
    $login_result = ftp_login($conn_id, $ftp_username, $ftp_password);

    if ($login_result) {
        // turn on passive mode transfers
        ftp_pasv($conn_id, true);

        // get the local file path
        $local_file = $_FILES["file"]["tmp_name"];

        // get the original file name
        $file_name = $_FILES["file"]["name"];

        // open the local file for reading
        $handle = fopen($local_file, "r");
        
        //run database insertion
        parseData($handle, $file_name);

        // initiate the upload
        $upload_result = ftp_nb_fput($conn_id, $file_name, $handle, FTP_BINARY);

        // check the progress of the upload
        while ($upload_result == FTP_MOREDATA) {
            // continue uploading the file
            $upload_result = ftp_nb_continue($conn_id);
        }

        // close the file handle
        fclose($handle);

        // check if the upload was successful
        if ($upload_result == FTP_FINISHED) {
            echo "File uploaded successfully: " . $file_name;
        } else {
            echo "Upload failed: " . $file_name;
        }

        // close the FTP connection
        ftp_close($conn_id);
    } else {
        echo "Login failed.";
    }
}

function parseData($handle, $file_name) {
  $Sensor = substr($file_name, 0, 5);

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
  fclose($file);
  
  $conn->commit();
  mysqli_stmt_close($stmt);
  mysqli_close($conn);
}
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
    for (var i = 0; i < files.length; i++) {
      var fileData = new FormData();
      fileData.append('file', files[i]);
      $.ajax({
        url: 'fileUpload.php',
        type: 'POST',
        data: fileData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log(response);
            var fileName = response.substring(response.indexOf(": ") + 2);
            fileName = fileName.substring(0, 5);
            console.log(fileName);
            $('#status').append('<p>Success: ' + fileName + '</p>');
            uploadedCount++; // Increment the count of uploaded files
            if (uploadedCount === files.length) { // Check if all files have been uploaded
              $('#status').append('<p>All files uploaded!</p>'); // Display a message indicating that all files have been uploaded
            }
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

