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

        // get the local file path
        $local_file = $_FILES["file"]["tmp_name"];

        // get the original file name
        $file_name = $_FILES["file"]["name"];

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
                var startIndex = response.indexOf("File uploaded successfully: ");
                if (startIndex !== -1) {
                    var fileName = response.substring(startIndex + 30); // add length of phrase
                    // Update user interface with status of file upload
                    alert(fileName);
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

