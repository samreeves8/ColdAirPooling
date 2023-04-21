
<body>
    <div id="map" style="height: 540px; width: 860px;"></div>
    <div id="sidebarContainer">
        <div id="sidebar"><h2>Click on a sensor to learn more.</h2></div>
        <div id="sidebarList"></div>
    </div>
    <input type="hidden" id="sensor-set-input" name="sensor-set-input" value="">
</body>
<script>

    // Get the value of the selectedSensors key from localStorage
    var sensorSet= localStorage.getItem("selectedSensors");
    
    var sensorSetInput =  localStorage.getItem("sensor_set_input");
    console.log(sensorSet);
    console.log(sensorSetInput);

    // If a value was found, set the value of the sensor-set-input field
    if(sensorSet !== NULL){
        sidebarList.innerHTML = '<p>Current sensors selected: ' + Array.from(sensorSet).join(", ") + '</p>';
        document.getElementById('sensor-set-input').value = JSON.stringify(Array.from(sensorSet));                  
    } else {
        //Defines a set of sensors the user wants to see data for
        var sensorSet = new Set();
    }

    console.log(sensorSet);



    // Initialize the map
    var mymap = L.map('map').setView([38.64955, -106.94685], 11);
          
    // Add the tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(mymap);
          

    // Adds markers to map based on inputs
    function addMarker(id, lat, lng, elevation, dateInstalled, recordsHumidity, map){
        //Checks if marker is a humidity sensor
        if(recordsHumidity == 1){
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
            var value = "";
            if(recordsHumidity == 0){
                value = "No"
            } else {
                value = "Yes"
            }
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
            Records humidity: " + value + "\
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
                var sensorSetArray = Array.from(sensorSet);
                localStorage.setItem("selectedSensors", sensorSetArray); 
                sidebarList.innerHTML = '<p>Current sensors selected: ' + Array.from(sensorSet).join(", ") + '</p>';
                document.getElementById('sensor-set-input').value = JSON.stringify(Array.from(sensorSet));                  
                localStorage.setItem("sensor_set_input",document.getElementById('sensor-set-input').value);
            });

            //Remove button logic
            var removeBtn = document.getElementById('remove-btn');
            removeBtn.addEventListener('click', function() {
                sensorSet.delete(id);
                var sensorSetArray = Array.from(sensorSet);
                localStorage.setItem("selectedSensors", sensorSetArray); 
                sidebarList.innerHTML = '<p>Current sensors selected: ' + Array.from(sensorSet).join(", ") + '</p>';
                document.getElementById('sensor-set-input').value = JSON.stringify(Array.from(sensorSet));                  
                localStorage.setItem("sensor_set_input",document.getElementById('sensor-set-input').value);
            });

            //Add button logic
            var addBtn = document.getElementById('add-btn');
            addBtn.addEventListener('click', function() {
                sensorSet.add(id);
                var sensorSetArray = Array.from(sensorSet);
                localStorage.setItem("selectedSensors", sensorSetArray);
                console.log(sensorSetArray); 
                console.log(localStorage.getItem("selectedSensors"));
                sidebarList.innerHTML = '<p>Current sensors selected: ' + Array.from(sensorSet).join(", ") + '</p>';
                document.getElementById('sensor-set-input').value = JSON.stringify(Array.from(sensorSet));
                localStorage.setItem("sensor_set_input",document.getElementById('sensor-set-input').value);
            });
        });
    }    
    
                  
</script>
