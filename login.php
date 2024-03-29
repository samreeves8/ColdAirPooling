<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === 1) {
    echo "<script>location.href='admin.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/Western Logo.png">
    <link rel="stylesheet" href="styles/nav.css">
    <link rel="stylesheet" href="styles/login.css">
    <title>Login</title>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'navBar.php';?>

<div class="container">

    <form action="login.php" method="POST" id="login">
        <div class="loginbox">
            <h1>Login</h1>

            <div class="textbox">
                <i class="fa fa-user" aria-hidden="true"></i>
                <input type="text" placeholder="Username" name="username" value="" required>
            </div>

            <div class="textbox">
                <i class="fa fa-lock" aria-hidden="true"></i>
                <input type="password" placeholder="Password" name="password" value="" required>
            </div>

            <input class="button" type="submit" name="login" value="Sign In">
        </div>
    </form>
    
</div>    
</body>
</html>

<?php

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

if (isset($_POST['username'], $_POST['password']) ) {

    if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	    // Bind parameters.
	    $stmt->bindParam(1, $_POST['username']);
	    $stmt->execute();
	    // Store the result so we can check if the account exists in the database.

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $row['id'];
            $hashed_password = $row['password'];

            // Account exists -> verify the password.
            if (password_verify($_POST['password'], $hashed_password)) {
                // Verification success
                // Create sessions, so we know the user is logged in.
                $_SESSION['loggedin'] = 1;
                $_SESSION['name'] = $_POST['username'];
                $_SESSION['id'] = $id;
                echo "<script>location.href='admin.php';</script>";
            } else {
                echo "<div class='error'>Incorrect username and/or password!</div>";
            }
        } else {
            echo '<div class="error">Incorrect username and/or password!</div>';
        }
	    $stmt->closeCursor();
    }
}
?>
