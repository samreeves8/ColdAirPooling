<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="login.css">
    <title>Document</title>
</head>
<body>
<div class="container">
    <div class="navbar">
        <ul class="menu">
            <li><a href="#">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="query.php">Query</a></li>
            <li><a href="#">Members</a></li>
            <li><a href="login.php">Log In</a></li>
            <li><a href="graph.php">Graph's</a></li>
        </ul>
    </div>

    <form action="login.php" method="POST" id="login">
        <div class="loginbox">
            <h1>Login</h1>

            <div class="textbox">
                <i class="fa fa-user" aria-hidden="true"></i>
                <input type="text" placeholder="Username" name="username" value="">
            </div>

            <div class="textbox">
                <i class="fa fa-lock" aria-hidden="true"></i>
                <input type="password" placeholder="Password" name="password" value="">
            </div>

            <input class="button" type="submit" name="login" value="Sign In">
        </div>
    </form>
    
</div>    
</body>
</html>

<?php

// Include config file
try {
    $servername = "localhost";
    $dbname = "gunniso1_SensorData";
    $dbusername = "gunniso1_Admin";
    $dbpassword = "gunnisoncoldair";

    $con = new PDO("mysql:host=$servername; dbname=gunniso1_SensorData", $dbusername, $dbpassword);

    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch(PDOException $e){
    echo "Connection Failed: " . $e->getMessage();
}
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM accounts WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    //if(empty(trim($_POST["confirm_password"]))){
       // $confirm_password_err = "Please confirm password.";     
   // } else{
      //  $confirm_password = trim($_POST["confirm_password"]);
      //  if(empty($password_err) && ($password != $confirm_password)){
          //  $confirm_password_err = "Password did not match.";
      //  }
  //  }
    
    // Check input errors before inserting in database
    //if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
    if(empty($username_err) && empty($password_err)){

        
        // Prepare an insert statement
        $sql = "INSERT INTO accounts (username, password) VALUES (:username, :password)";
         
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($pdo);
}
?>

/*
?>


try {
    $servername = "localhost";
    $dbname = "gunniso1_SensorData";
    $username = "gunniso1_Admin";
    $password = "gunnisoncoldair";

    $con = new PDO("mysql:host=$servername; dbname=gunniso1_SensorData", $username, $password);

    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch(PDOException $e){
    echo "Connection Failed: " . $e->getMessage();
}

if ( !isset($_POST['username'], $_POST['password']) ) {
	//exit('Please fill both the username and password fields!');
}

if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	// Bind parameters.
	$stmt->bindParam(1, $_POST['username']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        $hashed_password = $row['password'];

        // Account exists, now we verify the password.
        if (password_verify($_POST['password'], $hashed_password)) {
            // Verification success
            // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
            $_SESSION['loggedin'] = 1;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            echo "<script>location.href='admin.php';</script>";

        } 
        
        else {
           // echo "<script>alert('Incorrect username and/or password!');</script>";
        }
    } else {
        //echo '<div class="error">Incorrect username and/or password!</div>';
    }
	$stmt->closeCursor();
}
?>
