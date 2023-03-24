<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Document</title>
</head>
<body>
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

    <form action="login.php" method="POST">
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
</body>
</html>

<?php
//$conn = "";

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

//if ( !isset($_POST['username'], $_POST['password']) ) {
	//exit('Please fill both the username and password fields!');
//}

if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	// Bind parameters.
	$stmt->bindParam(1, $_POST['username']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        $password = $row['password'];
        // Account exists, now we verify the password.
        if ($_POST['password'] === $password) {
            // Verification success
            // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
            session_regenerate_id();
            $_SESSION['loggedin'] = 1;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            echo "<script>location.href='admin.php';</script>";

            //header("location: query.php");
            //exit();
            //echo 'Welcome ' . $_SESSION['name'] . '!';
        } else {
            //echo 'Incorrect username and/or password!';
        }
    } else {
        //echo 'Incorrect username and/or password!';
    }
	$stmt->closeCursor();
}
?>