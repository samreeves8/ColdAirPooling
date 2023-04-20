<body>
    <div id="map" style="height: 540px; width: 860px;"></div>
    <div id="sidebarContainer">
        <div id="sidebar"><h2>Click on a sensor to learn more.</h2></div>
        <div id="sidebarList"></div>
    </div>
    <input type="hidden" id="sensor-set-input" name="sensor-set-input" value="">
</body>
<script>

    markers = [];

    // Make an HTTP request to the PHP script using fetch
    fetch('sensorMarkers.php')
        .then(response => {
            if (response.ok) {
                return response.json(); // Parse the JSON response
            } else {
                throw new Error("Failed to fetch markers data: " + response.status);
            }
        })
        .then(markers => {
            // Now you can use the 'markers' variable which contains the parsed JSON data
            console.log(markers);
            // Perform further operations with the 'markers' data
        })
        .catch(error => {
            // Handle the fetch error
            console.error(error);
        });

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




    //Adds each sensor to map
    markers.forEach(marker => {
        addMarker(marker.id, marker.lat, marker.lng, marker.elevation, marker.dateInstalled, marker.recordsHumidity, mymap, sensorSet);
    });                    
</script>
