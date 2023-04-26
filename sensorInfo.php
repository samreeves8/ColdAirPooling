<?php
    if($_SERVER['REQUEST_METHOD']==='GET'){
        $sensor =  $_GET['sensor'];
        echo $sensor;
    }
?>