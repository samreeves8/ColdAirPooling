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
            echo '<iframe src="https://www.google.com/maps/d/embed?mid=1QKCUJdVptPLWD9xI8WVnY4fxXUM5NmBl&ehbc=2E312F" width="640" height="480"
                style="border:none; z-index:1; margin:-150px; position:relative; padding-left: 150px" frameborder="0" allowfullscreen="false" ></iframe>';
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