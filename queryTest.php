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
          

            

            function addMarker(id, lat, lng, elevation, dateInstalled, recordsHumidity, map, sensorSet){
                var marker = L.marker([lat, lng]).addTo(map);
                marker.on(\'click\', function(e) {
                    sensorSet.add(id);
                    const temp = Array.from(sensorSet).join(", ");
                    sidebar.innerHTML = \'<h2>Sensor \' + id + \'</h2>\
                    <p>\
                    Latitude: \' + lat + \'\
                    <br>\
                    Longitude: \' + lng + \'\
                    <br>\
                    Elevation: \' + elevation + \'\
                    <br>\
                    Date installed: \' + dateInstalled + \'\
                    <br>\
                    Records humidity: \' + recordsHumidity + \'\
                    <br>\
                    <br>\
                    Current sensors selected: \' + temp + \'\
                    <\p>\';
                });
            }

            

            const markers = [
                {id = \'01OBS\', lat = 38.50949, lng = -106.93991, elevation = 7457, dateInstalled: \'02/16/2022\', recordsHumidity: \'Yes\' },
                {id = \'02FAI\', lat = 38.54062, lng = -106.9284, elevation = 7694, dateInstalled: \'02/15/2022\', recordsHumidity: \'No\' },
                {id = \'03IBA\', lat = 38.54087, lng = -106.90678, elevation = 7707, dateInstalled: \'02/15/2022\', recordsHumidity: \'No\' },
                {id = \'04MIB\', lat = 38.70076, lng = -107.02626, elevation = 8465, dateInstalled: \'02/26/2022\', recordsHumidity: \'No\' },
                {id = \'05VAN\', lat = 38.55585, lng = -106.93838, elevation = 7758, dateInstalled: \'02/15/2022\', recordsHumidity: \'No\' },
                {id = \'06MYS\', lat = 38.584, lng = -106.9101, elevation = 7788, dateInstalled: \'02/16/2022\', recordsHumidity: \'No\' },
                {id = \'07PAR\', lat = 38.5656, lng = -106.94043, elevation = 7724, dateInstalled: \'02/17/2022\', recordsHumidity: \'No\' },
                {id = \'08CHI\', lat = 38.58282, lng = -106.92633, elevation = 7772, dateInstalled: \'02/15/2022\', recordsHumidity: \'No\' },
                {id = \'09MIC\', lat = 38.71917, lng = -106.99816, elevation = 8478, dateInstalled: \'02/18/2022\', recordsHumidity: \'No\' },
                {id = \'10NEM\', lat = 38.59285, lng = -106.92626, elevation = 7807, dateInstalled: \'02/15/2022\', recordsHumidity: \'Yes\' },
                {id = \'11MAG\', lat = 38.6239, lng = -106.94113, elevation = 7904, dateInstalled: \'02/15/2022\', recordsHumidity: \'No\' },
                {id = \'12FAV\', lat = 38.64955, lng = -106.94685, elevation = 8030, dateInstalled: \'02/16/2022\', recordsHumidity: \'No\' },
                {id = \'13EAG\', lat = 38.67508, lng = -106.98376, elevation = 8124, dateInstalled: \'02/16/2022\', recordsHumidity: \'No\' },
                {id = \'14MIA\', lat = 38.70238, lng = -107.00021, elevation = 8257, dateInstalled: \'02/26/2022\', recordsHumidity: \'No\' },
                {id = \'15CAS\', lat = 38.73612, lng = -107.03187, elevation = 8448, dateInstalled: \'02/18/2022\', recordsHumidity: \'No\' },
                {id = \'16BAL\', lat = 38.75819, lng = -107.04936, elevation = 8600, dateInstalled: \'02/18/2022\', recordsHumidity: \'No\' },
                {id = \'17WIL\', lat = 38.77481, lng = -107.06917, elevation = 8745, dateInstalled: \'02/18/2022\', recordsHumidity: \'Yes\' },
                {id = \'18ALL\', lat = 38.6167, lng = -106.90837, elevation = 7878, dateInstalled: \'02/15/2022\', recordsHumidity: \'No\' },
                {id = \'19ROC\', lat = 38.61066, lng = -106.86569, elevation = 7908, dateInstalled: \'02/15/2022\', recordsHumidity: \'No\' },
                {id = \'20LOS\', lat = 38.64281, lng = -106.84723, elevation = 7987, dateInstalled: \'02/15/2022\', recordsHumidity: \'No\' },
                {id = \'21ALM\', lat = 38.66134, lng = -106.84723, elevation = 8046, dateInstalled: \'02/15/2022\', recordsHumidity: \'Yes\' },
                {id = \'22CRA\', lat = 38.59877, lng = -106.89007, elevation = 7847, dateInstalled: \'02/16/2022\', recordsHumidity: \'No\' },
                {id = \'23TRA\', lat = 38.60136, lng = -106.90439, elevation = 7874, dateInstalled: \'02/16/2022\', recordsHumidity: \'No\' },
                {id = \'24CAM\', lat = 38.60075, lng = -106.94217, elevation = 7906, dateInstalled: \'02/15/2022\', recordsHumidity: \'Yes\' },
                {id = \'25MEA\', lat = 38.53937, lng = -106.95069, elevation = 7700, dateInstalled: \'02/15/2022\', recordsHumidity: \'No\' },
                {id = \'26CRB\', lat = 38.586, lng = -106.892, elevation = 7999, dateInstalled: \'10/23/2022\', recordsHumidity: \'No\' },
                {id = \'27CHR\', lat = 38.52242, lng = -106.80191, elevation = 7882, dateInstalled: \'10/23/2022\', recordsHumidity: \'No\' },
                {id = \'28TOM\', lat = 38.54018, lng = -106.8555, elevation = 7765, dateInstalled: \'10/23/2022\', recordsHumidity: \'No\' },
                {id = \'29CAB\', lat = 38.71671, lng = -107.00556, elevation = 8325, dateInstalled: \'10/23/2022\', recordsHumidity: \'Yes\' },
                {id = \'30HIN\', lat = 38.6876, lng = -106.9903, elevation = 8148, dateInstalled: \'10/23/2022\', recordsHumidity: \'No\' }
            ];

            markers.forEach(marker => {
                addMarker(marker.id, marker.lat, marker.lng, marker.elevation, marker.dateInstalled, marker.recordsHumidity, mymap, sensorSet);
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