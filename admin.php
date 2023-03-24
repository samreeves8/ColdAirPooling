<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== 1) {
    // user is not logged in, redirect to login page
    echo "<script>location.href='login.php';</script>";

    //header('Location: login.php');
    exit;
}

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

if (isset($_POST['submit'])) {
    $new_username = $_POST['new_username'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // hash password

    // prepare SQL statement to insert new admin user into database
    $stmt = $con->prepare("INSERT INTO accounts (username, password) VALUES (:username, :password)");
    $stmt->bindParam(':username', $new_username);
    $stmt->bindParam(':password', $new_password);

    // execute SQL statement and display success or error message
    if ($stmt->execute()) {
        echo "New admin user created successfully.";
    } else {
        echo "Error creating new admin user: " . $stmt->errorInfo()[2];
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h1>Welcome, <?php echo $_SESSION['name']; ?>!</h1><br>

<h2>Create New Admin User</h2><br>
<form method="post">
    <label for="new_username">Username:</label>
    <input type="text" id="new_username" name="new_username" required>
    <br>
    <label for="new_password">Password:</label>
    <input type="password" id="new_password" name="new_password" required>
    <br>
    <input type="submit" name="submit" value="Create User">
</form>
</body>
</html>