<?php
    $currentFormIndex = 0;

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $currentFormIndex = $_POST['currentFormIndex'];

        if(isset($_POST['next'])){
            
            $currentFormIndex++;

        }elseif(isset($_POST['previous'])){
            $currentFormIndex--;
        }
    }

    if($currentFormIndex >= 0 && $currentFormIndex < 3){
        
        echo "<form method = 'POST'>";
        if($currentFormIndex == 0){
            echo "<h1> index 1 </h1>";
            echo
            '<head>
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
                <style>
                    #sidebar {
                    position: absolute;
                    top: 0;
                    right: 0;
                    width: 200px;
                    height: 100%;
                    background-color: #f0f0f0;
                    padding: 10px;
                    box-sizing: border-box;
                    overflow: auto;
                }
            </style>
            </head>
            <body>
                <div id="map" style="height: 640px; width: 960px;"></div>
                <div id="sidebar"></div>
            </body>
            <script>
                var sensorSet = new Set();

                // Initialize the map
                var mymap = L.map(\'map\').setView([38.64955, -106.94685], 10);
          
                // Add the tile layer
                L.tileLayer(\'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png\', {
                attribution: \'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors\',
                maxZoom: 18,
            }).addTo(mymap);
          
            // Add the marker and on-click listener
            var marker01 = L.marker([38.50949, -106.93991]).addTo(mymap);
            marker01.on(\'click\', function(e) {
                sensorSet.add(\'01OBS\');
                const temp = Array.from(sensorSet).join(", ");
                sidebar.innerHTML = \'<h2>Sensor 01OBS</h2>\
                <p>\
                Latitude: 38.50949\
                <br>\
                Longitude: -106.93991\
                <br>\
                Elevation: 7457 ft.\
                <br>\
                Date installed: 02/16/2022\
                <br>\
                Records Humidity: Yes\
                <br>\
                <br>\
                Current sensors selected: \' + temp + \' \
                </p>\';
            });
          </script>';

            
        }else if($currentFormIndex == 1){
            echo "<h1> Insert date and time range for data you want to see: </h1>";
            echo 
            '<label for="dateStart">Select a start date:</label>
            <input type="date" id="dateStart" name="dateStart" value = "2022-08-16">
            <label for="timeStart">Select a start time:</label>
            <input type="time" id="timeStart" name="timeStart" value = "00:00">
            <br>
            <label for="dateEnd">Select an end date:</label>
            <input type="date" id="dateEnd" name="dateEnd" value="'. date('Y-m-d') .'">
            <label for="timeEnd">Select an end time:</label>
            <input type="time" id="timeEnd" name="timeEnd" value = "00:00">
            <br>'
            ;
        }else if($currentFormIndex == 2){
            echo "<h1> index 3 </h1>";
        }

        echo "<input type = 'hidden' name = 'sensorList' value = sensorSet>";
        echo "<input type='hidden' name='currentFormIndex' value='$currentFormIndex'>";
        

        if($currentFormIndex > 0){
            echo "<input type = 'submit' name = 'previous' value = 'Previous'>";
        }
        if($currentFormIndex < 2){
            echo "<input type = 'submit' name = 'next' value = 'Next'>";
        }else{
            echo "<input type = 'submit' name = 'graph' value = 'Graph'>";
        }
        echo "</form>";
    }

?>