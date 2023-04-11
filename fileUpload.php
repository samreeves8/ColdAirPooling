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
        var totalBytes = 0;
        for (var i = 0; i < formData.getAll('files[]').length; i++) {
          totalBytes += formData.getAll('files[]')[i].size;
        }
        $.ajax({
          url: 'fileUpload.php',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          xhr: function() {
            var xhr = new XMLHttpRequest();
            xhr.withCredentials = true;
            xhr.upload.addEventListener('progress', function(event) {
              if (event.lengthComputable) {
                var bytesUploaded = 0;
                for (var i = 0; i < formData.getAll('files[]').length; i++) {
                  if (event.loaded >= bytesUploaded + formData.getAll('files[]')[i].size) {
                    console.log(bytesUploaded += formData.getAll('files[]')[i].size);
                    bytesUploaded += formData.getAll('files[]')[i].size;
                  } else {
                    bytesUploaded += event.loaded - bytesUploaded;
                    break;
                  }
                }
                var percentComplete = bytesUploaded / totalBytes * 100;
                console.log(percentComplete);
                $('.progress-bar').width(percentComplete + '%').html(percentComplete + '%');
              }
            }, false);
            return xhr;
          },
          success: function(response) {
            console.log(response);
          }
        });
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

        // upload the file to FTP server
        $upload_result = ftp_put($conn_id, $file_name, $local_file, FTP_BINARY);

        if (!$upload_result) {
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
