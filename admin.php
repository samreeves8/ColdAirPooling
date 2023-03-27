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
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

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

if (isset($_POST['Update User'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // prepare SQL statement to update admin user in database
    $stmt = $con->prepare("UPDATE accounts SET username = :username, password = :password WHERE id = :id");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':id', $id);

    // execute SQL statement and display success or error message
    if ($stmt->execute()) {
        echo "Admin user updated successfully.";
    } else {
        echo "Error updating admin user: " . $stmt->errorInfo()[2];
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

<h2>Existing Admin Users</h2><br>
    <table>
        <tr>
            <th>Username</th>
            <th>Password Hash</th>
        </tr>
        <?php
        // prepare SQL statement to select existing admin users from database
        $stmt = $con->prepare("SELECT username, password FROM accounts");
        $stmt->execute();
        $result = $stmt->fetchAll();

        // loop through each row of the result and display the data in a table
        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['password'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

<h2>Update Admin User</h2><br>
<form method="post">
    <label for="existing_username">Existing Username:</label>
    <input type="text" id="existing_username" name="existing_username" required>
    <br>
    <label for="new_username">New Username:</label>
    <input type="text" id="new_username" name="new_username" required>
    <br>
    <label for="new_password">New Password:</label>
    <input type="password" id="new_password" name="new_password" required>
    <br>
    <input type="submit" name="update" value="Update User">
</form>
</body>
</html>