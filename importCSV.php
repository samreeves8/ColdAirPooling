<form action="importCSV.php" method="post" name="frmCSVImport" id="frmCSVImport" enctype="multipart/form-data" onsubmit="return validateFile()">
    
	<label>Choose your file. </label> <input type="file" name="file[]" id="file" class="file" accept=".csv,.xls,.xlsx" multiple>
	<button type="submit" id="submit" name="import">Import CSV and Save Data</button>
</form>

<?php 
    
    ini_set('max_execution_time', 300); // Set maximum execution time to 5 minutes
    ini_set('memory_limit', '512M'); // Set memory limit to 512 MB

    $humidity = array("01OBS", "10NEM", "17WIL", "21ALM", "24CAM", "29CAB");  // Humidity Sensors
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Check if file was uploaded successfully
        if (!isset($_FILES['file']) || $_FILES['file']['error'][0] != UPLOAD_ERR_OK) {
            echo "File upload error.";
            exit;
        }

        $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $conn->autocommit(FALSE);

        $Sensor = NULL;
        $DateTime = NULL;
        $Temperature = NULL;
        $RH = NULL;
        $DewPoint = NULL;

        $sql_humidity = "INSERT INTO HumidData (Sensor, DateTime, Temperature, RH, DewPoint) VALUES (?, ?, ?, ?, ?)";
        $stmt_humidity = mysqli_prepare($conn, $sql_humidity);
        mysqli_stmt_bind_param($stmt_humidity, "ssddd", $Sensor, $DateTime, $Temperature, $RH, $DewPoint);

        $sql_temp = "INSERT INTO TempData (Sensor, DateTime, Temperature) VALUES (?, ?, ?)";
        $stmt_temp = mysqli_prepare($conn, $sql_temp);
        mysqli_stmt_bind_param($stmt_temp, "ssd", $Sensor, $DateTime, $Temperature);

        // Checks all of the files that are uploaded
        foreach($_FILES['file']['name'] as $key=>$value){
            if($_FILES['file']['error'][$key] == UPLOAD_ERR_OK){
                $filename = $_FILES['file']['name'][$key]; 
                $tmpfilename = $_FILES['file']['tmp_name'][$key];
                echo $filename."<br>";
                $file = fopen($tmpfilename, "r");
                $Sensor = substr($filename, 0, 5);
                //Check each file for existance
                if ($file) {
                    
                    //Checks which table to access (HumidData or TempData)
                    $stmt = in_array($Sensor, $humidity) ? $stmt_humidity : $stmt_temp;

                    if(in_array($Sensor, $humidity) == true){
                        $h = true;
                    } else {
                        $h = false;
                    }
                    
                    //Begins accessing files

                    fgetcsv($file);
                
                    while (($row = fgetcsv($file)) !== false) {
                        
                        //Accounts for date time differences

                        $DateTime = DateTime::createFromFormat('m/d/Y H:i:s', $row[$dateTimeIndex]);
                        if (!$DateTime) {
                            $DateTime = DateTime::createFromFormat('m/d/Y H:i', $row[$dateTimeIndex]);
                                if (!$DateTime) {
                                    $DateTime = DateTime::createFromFormat('m-d-Y H:i:s', $row[$dateTimeIndex]);
                                        if (!$DateTime) {
                                            $DateTime = DateTime::createFromFormat('m-d-Y H:i', $row[$dateTimeIndex]);
                                                if (!$DateTime) {
                                                    echo "Invalid datetime value: " . $row[$dateTimeIndex] . "\n";
                                                    continue;
                                                }
                                        }
                                }
                        }
                        // Set the parameter values
                        $DateTime = $DateTime->format('Y-m-d H:i:s');
                        $Temperature = $row[$tempIndex];
                        if($h){
                            $RH = $row[$rhIndex];
                            $DewPoint = $row[$dewPointIndex];
                        }

                        //Adds to database if there is a value
                        
                        if($Temperature!=null){
                            mysqli_stmt_execute($stmt);
                        }
                    }
                
                    // Close the statement and file
                    mysqli_stmt_close($stmt);
                    fclose($file);
                    
                } else {
                    echo "Failed to open file: $filename";
                }
            }
            $conn->commit();
        }
    }
    
    mysqli_close($conn);
?>