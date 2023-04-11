<form action="fileUpload.php" method="post" enctype="multipart/form-data" id="upload-form">
  <input type="file" name="files[]" multiple>
  <input type="submit" value="Upload">
</form>

<script>
$(document).ready(function() {
    $('form').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData($('form')[0]);
        var totalBytes = 0;
        for (var i = 0; i < formData.getAll('file[]').length; i++) {
        totalBytes += formData.getAll('file[]')[i].size;
    }
        $.ajax({
        url: 'fileUpload.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
            var xhr = new XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(event) {
            if (event.lengthComputable) {
                var bytesUploaded = 0;
                for (var i = 0; i < formData.getAll('file[]').length; i++) {
                if (event.loaded >= bytesUploaded + formData.getAll('file[]')[i].size) {
                    bytesUploaded += formData.getAll('file[]')[i].size;
                } else {
                    bytesUploaded += event.loaded - bytesUploaded;
                    break;
                }
                }
                var percentComplete = bytesUploaded / totalBytes * 100;
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

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // FTP server details
    $ftp_server = "108.167.182.245";
    $ftp_username = "gunniso1";
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
