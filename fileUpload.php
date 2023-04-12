<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>File Upload with Progress Bar</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
  <form action="fileUpload.php" method="post" enctype="multipart/form-data" id="upload-form">
    <input type="file" name="files[]" multiple>
    <input type="submit" value="Upload">
  </form>

  <div class="progress">
    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
  </div>

  <script>
$(document).ready(function() {
  $('#upload-form').on('submit', function(event) {
    event.preventDefault();
    console.log("successfully prevented default");
    var formData = new FormData($('#upload-form')[0]);
    var files = formData.getAll('files[]');
    var totalBytes = 0;
    for (var i = 0; i < files.length; i++) {
      totalBytes += files[i].size;
    }
    var bytesUploaded = 0;
    var percentComplete = 0;
    var currentFileIndex = 0;

    function uploadFile() {
      var file = files[currentFileIndex];
      var xhr = new XMLHttpRequest();
      xhr.withCredentials = true;
      xhr.upload.addEventListener('progress', function(event) {
        if (event.lengthComputable) {
          bytesUploaded += event.loaded - bytesUploaded;
          percentComplete = bytesUploaded / totalBytes * 100;
          console.log(percentComplete);
          $('.progress-bar').width(percentComplete + '%').html(Math.round(percentComplete) + '%');
        }
      }, false);
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            console.log(xhr.responseText);
            currentFileIndex++;
            if (currentFileIndex < files.length) {
              uploadFile();
            } else {
              // all files have been uploaded
            }
          } else {
            console.error('Upload failed:', xhr.status);
          }
        }
      };
      var formData = new FormData();
      formData.append('file', file);
      xhr.open('POST', 'fileUpload.php');
      xhr.send(formData);
    }

    if (files.length > 0) {
      uploadFile();
    }
  });
});

  </script>
</body>
</html>


<?php
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

        // loop through uploaded files
        foreach ($_FILES["files"]["tmp_name"] as $key => $tmp_name) {
            // get the local file path
            $local_file = $_FILES["files"]["tmp_name"][$key];

            // get the original file name
            $file_name = $_FILES["files"]["name"][$key];

            // open the local file for reading
            $handle = fopen($local_file, "r");

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
            if ($upload_result != FTP_FINISHED) {
                echo "Upload failed: " . $file_name . "<br>";
            }
        }

        // close the FTP connection
        ftp_close($conn_id);
    } else {
        echo "Login failed.";
    }
}
?>

