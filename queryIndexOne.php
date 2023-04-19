
<body>
    <div id="map" style="height: 540px; width: 860px;"></div>
    <div id="sidebarContainer">
        <div id="sidebar"><h2>Click on a sensor to learn more.</h2></div>
        <div id="sidebarList"></div>
    </div>
    <input type="hidden" id="sensor-set-input" name="sensor-set-input" value="">
</body>
<script>
    //Defines a set of sensors the user wants to see data for
    var sensorSet = new Set();

    // Initialize the map
    var mymap = L.map('map').setView([38.64955, -106.94685], 11);
          
    // Add the tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(mymap);
          

    // Adds markers to map based on inputs
    function addMarker(id, lat, lng, elevation, dateInstalled, recordsHumidity, map, sensorSet){
            
        //Checks if marker is a humidity sensor
        var marker = null;
        if(recordsHumidity == "Yes"){
            marker = L.circleMarker([lat, lng] , {color: 'red', radius: 10}).addTo(map);
        }else{
            marker = L.circleMarker([lat, lng], {color: 'blue', radius: 10}).addTo(map);
        }
            
        /* 
        Add a click listener to each marker that displays a little about for 
        each sensor with an add or remove button to add to a set of sensors
        that the user wants to see data about
        */ 

        marker.on('click', function(e) {
            sidebar.innerHTML = "<h2>Sensor " + id + "</h2>\
            <p>\
            Latitude: " + lat + "\
            <br>\
            Longitude: " + lng + "\
            <br>\
            Elevation: " + elevation + "\
            <br>\
            Date installed: " + dateInstalled + "\
            <br>\
            Records humidity: " + recordsHumidity + "\
            <br>\
            <br>\
            <button type = \'button\' id=\'remove-btn\'>Remove</button>\
            <br>\
            <button type = \'button\' id=\'add-btn\'>Add</button>\
            <br>\
            <button type = \'button\' id=\'clear-btn\'>Clear</button>\
            </p>";

            //Clear button logic
            var clearBtn = document.getElementById('clear-btn');
            clearBtn.addEventListener('click', function() {
                sensorSet.clear();
                sidebarList.innerHTML = '<p>Current sensors selected: ' + Array.from(sensorSet).join(", ") + '</p>';
                document.getElementById('sensor-set-input').value = JSON.stringify(Array.from(sensorSet));                  
            });

            //Remove button logic
            var removeBtn = document.getElementById('remove-btn');
            removeBtn.addEventListener('click', function() {
                sensorSet.delete(id);
                sidebarList.innerHTML = '<p>Current sensors selected: ' + Array.from(sensorSet).join(", ") + '</p>';
                document.getElementById('sensor-set-input').value = JSON.stringify(Array.from(sensorSet));                  
            });

            //Add button logic
            var addBtn = document.getElementById('add-btn');
            addBtn.addEventListener('click', function() {
                sensorSet.add(id);
                sidebarList.innerHTML = '<p>Current sensors selected: ' + Array.from(sensorSet).join(", ") + '</p>';
                document.getElementById('sensor-set-input').value = JSON.stringify(Array.from(sensorSet));
    
            });
        });
    }

    markers = [];
    <?php
        global $conn;
        $markers = array();

        $sql = "SELECT id, lat, lng, elevation, DATE_FORMAT(Date, '%Y-%m-%d') as dateInstalled, recordsHumidity FROM SensorData";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $markers[] = array(
                    "id" => $row['id'],
                    "lat" => $row['lat'],
                    "lng" => $row['lng'],
                    "elevation" => $row['elevation'],
                    "dateInstalled" => $row['dateInstalled'],
                    "recordsHumidity" => $row['recordsHumidity']
                );
            }
        }

        echo json_encode($markers);
    ?>
            
    window.onload = function() {
        // create XMLHttpRequest object
        var xhr = new XMLHttpRequest();
        
        // send GET request to PHP script that returns the array
        xhr.open('GET', 'queryIndexOne.php', true);
        xhr.send();
        
        // handle response from PHP script
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                markers = JSON.parse(xhr.responseText);
                console.log(markers); // do something with the array
            }
        };
    }

    //Defines all sensors that we are adding to map
    // const markers = [
    //     {id: '01OBS', lat: 38.50949, lng: -106.93991, elevation: 7457, dateInstalled: '02/16/2022', recordsHumidity: 'Yes'},
    //     {id: '02FAI', lat: 38.54062, lng: -106.9284, elevation: 7694, dateInstalled: '02/15/2022', recordsHumidity: 'No' },
    //     {id: '03IBA', lat: 38.54087, lng: -106.90678, elevation: 7707, dateInstalled: '02/15/2022', recordsHumidity: 'No' },
    //     {id: '04MIB', lat: 38.70076, lng: -107.02626, elevation: 8465, dateInstalled: '02/26/2022', recordsHumidity: 'No' },
    //     {id: '05VAN', lat: 38.55585, lng: -106.93838, elevation: 7758, dateInstalled: '02/15/2022', recordsHumidity: 'No' },
    //     {id: '06MYS', lat: 38.584, lng: -106.9101, elevation: 7788, dateInstalled: '02/16/2022', recordsHumidity: 'No' },
    //     {id: '07PAR', lat: 38.5656, lng: -106.94043, elevation: 7724, dateInstalled: '02/17/2022', recordsHumidity: 'No' },
    //     {id: '08CHI', lat: 38.58282, lng: -106.92633, elevation: 7772, dateInstalled: '02/15/2022', recordsHumidity: 'No' },
    //     {id: '09MIC', lat: 38.71917, lng: -106.99816, elevation: 8478, dateInstalled: '02/18/2022', recordsHumidity: 'No' },
    //     {id: '10NEM', lat: 38.59285, lng: -106.92626, elevation: 7807, dateInstalled: '02/15/2022', recordsHumidity: 'Yes' },
    //     {id: '11MAG', lat: 38.6239, lng: -106.94113, elevation: 7904, dateInstalled: '02/15/2022', recordsHumidity: 'No' },
    //     {id: '12FAV', lat: 38.64955, lng: -106.94685, elevation: 8030, dateInstalled: '02/16/2022', recordsHumidity: 'No' },
    //     {id: '13EAG', lat: 38.67508, lng: -106.98376, elevation: 8124, dateInstalled: '02/16/2022', recordsHumidity: 'No' },
    //     {id: '14MIA', lat: 38.70238, lng: -107.00021, elevation: 8257, dateInstalled: '02/26/2022', recordsHumidity: 'No' },
    //     {id: '15CAS', lat: 38.73612, lng: -107.03187, elevation: 8448, dateInstalled: '02/18/2022', recordsHumidity: 'No' },
    //     {id: '16BAL', lat: 38.75819, lng: -107.04936, elevation: 8600, dateInstalled: '02/18/2022', recordsHumidity: 'No' },
    //     {id: '17WIL', lat: 38.77481, lng: -107.06917, elevation: 8745, dateInstalled: '02/18/2022', recordsHumidity: 'Yes' },
    //     {id: '18ALL', lat: 38.6167, lng: -106.90837, elevation: 7878, dateInstalled: '02/15/2022', recordsHumidity: 'No' },
    //     {id: '19ROC', lat: 38.61066, lng: -106.86569, elevation: 7908, dateInstalled: '02/15/2022', recordsHumidity: 'No' },
    //     {id: '20LOS', lat: 38.64281, lng: -106.84723, elevation: 7987, dateInstalled: '02/15/2022', recordsHumidity: 'No' },
    //     {id: '21ALM', lat: 38.66134, lng: -106.84723, elevation: 8046, dateInstalled: '02/15/2022', recordsHumidity: 'Yes' },
    //     {id: '22CRA', lat: 38.59877, lng: -106.89007, elevation: 7847, dateInstalled: '02/16/2022', recordsHumidity: 'No' },
    //     {id: '23TRA', lat: 38.60136, lng: -106.90439, elevation: 7874, dateInstalled: '02/16/2022', recordsHumidity: 'No' },
    //     {id: '24CAM', lat: 38.60075, lng: -106.94217, elevation: 7906, dateInstalled: '02/15/2022', recordsHumidity: 'Yes' },
    //     {id: '25MEA', lat: 38.53937, lng: -106.95069, elevation: 7700, dateInstalled: '02/15/2022', recordsHumidity: 'No' },
    //     {id: '26CRB', lat: 38.586, lng: -106.892, elevation: 7999, dateInstalled: '10/23/2022', recordsHumidity: 'No' },
    //     {id: '27CHR', lat: 38.52242, lng: -106.80191, elevation: 7882, dateInstalled: '10/23/2022', recordsHumidity: 'No' },
    //     {id: '28TOM', lat: 38.54018, lng: -106.8555, elevation: 7765, dateInstalled: '10/23/2022', recordsHumidity: 'No' },
    //     {id: '29CAB', lat: 38.71671, lng: -107.00556, elevation: 8325, dateInstalled: '10/23/2022', recordsHumidity: 'Yes' },
    //     {id: '30HIN', lat: 38.6876, lng: -106.9903, elevation: 8148, dateInstalled: '10/23/2022', recordsHumidity: 'No' }
    // ];

    //Adds each sensor to map
    markers.forEach(marker => {
        addMarker(marker.id, marker.lat, marker.lng, marker.elevation, marker.dateInstalled, marker.recordsHumidity, mymap, sensorSet);
    });                    
</script>
