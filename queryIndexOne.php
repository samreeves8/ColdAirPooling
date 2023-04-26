
<body>
    <div id="container">
    <div id="map"></div>
    <div id="sidebarContainer">
        <div id="sidebar"><h2>Click on a sensor to learn more.</h2></div>
        <div id="sidebarList"></div>
    </div>
    </div>
    <input type="hidden" id="sensor-set-input" name="sensor-set-input" value="">
</body>
<script>
    var sensorSet = new Set();
    window.onload = function() {
        
        // Get the value of the selectedSensors key from localStorage
        var sensorSetArray= localStorage.getItem("selectedSensors");

        // If a value was found, set the value of the sensor-set-input field
        if(sensorSetArray){
            sensorSet = new Set(sensorSetArray.split(","));
            sidebarList.innerHTML = '<p>Current sensors selected: ' + Array.from(sensorSet).join(", ") + '</p>';
            document.getElementById('sensor-set-input').value = JSON.stringify(Array.from(sensorSet));                  
        }
    }


    // Initialize the map
    var mymap = L.map('map').setView([38.64955, -106.94685], 11);
          
    // Add the tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(mymap);
          

    // Adds markers to map based on inputs
    function addMarker(id, lat, lng, elevation, dateInstalled, recordsHumidity,description, map){
        //Checks if marker is a humidity sensor
        if(recordsHumidity == 1){
            marker = L.circleMarker([lat, lng] , {color: 'red', radius: 10, label: id}).addTo(map);
        }else{
            marker = L.circleMarker([lat, lng], {color: 'blue', radius: 10, label: id}).addTo(map);
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
            Description: " + description + "\
            <br>\
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
                console.log(sensorSetArray);
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
                console.log("add button");
                sensorSet.add(id);
                var sensorSetArray = Array.from(sensorSet);
                localStorage.setItem("selectedSensors", sensorSetArray);
                console.log(sensorSetArray); 
                sidebarList.innerHTML = '<p>Current sensors selected: ' + Array.from(sensorSet).join(", ") + '</p>';
                document.getElementById('sensor-set-input').value = JSON.stringify(Array.from(sensorSet));
            });
        });
    }    
    
                  
</script>
