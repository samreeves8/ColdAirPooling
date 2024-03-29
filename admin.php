<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== 1) {
    // user is not logged in, redirect to login page
    echo "<script>location.href='login.php';</script>";

    //header('Location: login.php');
    exit;
}

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

if (isset($_POST['update'])) {
    $existing_username = $_POST['existing_username'];
    $update_username = $_POST['update_username'];
    $update_password = password_hash($_POST['update_password'], PASSWORD_DEFAULT);

    // prepare SQL statement to check if existing username is in the database
    $stmt = $con->prepare("SELECT COUNT(*) FROM accounts WHERE username = :existing_username");
    $stmt->bindParam(':existing_username', $existing_username);
    $stmt->execute();
    $result = $stmt->fetchColumn();

    if ($result > 0) {
        // prepare SQL statement to update the username and password in the database
        $stmt = $con->prepare("UPDATE accounts SET username = :update_username, password = :update_password WHERE username = :existing_username");
        $stmt->bindParam(':existing_username', $existing_username);
        $stmt->bindParam(':update_username', $update_username);
        $stmt->bindParam(':update_password', $update_password);

        // execute SQL statement and display success or error message
        if ($stmt->execute()) {
            echo "Admin user updated successfully.";
        } else {
            echo "Error updating admin user: " . $stmt->errorInfo()[2];
        }
    } else {
        echo "Admin user with username " . $existing_username . " does not exist in the database.";
    }
}

// check if the delete button has been pressed
if (isset($_POST['toDelete'])) {
    // get the IDs of the selected rows as an array
    $selected_rows = $_POST['delete'] ?? array();

    echo $selected_rows;
    
    if (count($selected_rows) == 1) {
        $stmt = $con->prepare("DELETE FROM accounts WHERE id = ?");
        $stmt->bindParam(1, $selected_rows[0]);
    } else {
        // convert the array of IDs into a comma-separated string
        $id_list = implode(',', $selected_rows);
        
        // prepare SQL statement to delete selected rows from the database
        $stmt = $con->prepare("DELETE FROM accounts WHERE id IN ($id_list)");
    }
    
    // execute SQL statement and display success or error message
    if ($stmt->execute()) {
        echo "Selected rows deleted successfully.";
    } else {
        echo "Error deleting selected rows: " . $stmt->errorInfo()[2];
    }
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
    <link rel="stylesheet" href="styles/admin.css">
    <title>Admin</title>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'navBar.php';?>

<h1>Welcome, <?php echo $_SESSION['name']; ?>!</h1><br>

<div class="create">
<h2>Create New Admin User</h2><br>
<form method="POST">
    <label for="new_username">Username:</label>
    <input type="text" id="new_username" name="new_username" required>
    <br>
    <label for="new_password">Password:</label>
    <input type="password" id="new_password" name="new_password" required>
    <br>
    <input type="submit" name="submit" value="Create User">
</form>
</div>
<h2>Existing Admin Users</h2><br>
    <form method="POST">
    <table>
        <tr>
            <th>Username</th>
            <th>Select</th>
        </tr>
        <?php
        // prepare SQL statement to select existing admin users from database
        $stmt = $con->prepare("SELECT username, password, id FROM accounts");
        $stmt->execute();
        $result = $stmt->fetchAll();

        // loop through each row of the result and display the data in a table
        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td><input type='checkbox' id='delete' name='delete[]' value='" . $row['id'] . "'></td>"; 
            echo "</tr>";
        }
        ?>
    </table>
    <input type="submit" name="toDelete" value="Delete Selected">
    </form>

<h2>Update Admin User</h2><br>
<form method="POST">
    <label for="existing_username">Existing Username:</label>
    <input type="text" id="existing_username" name="existing_username" required>
    <br>
    <label for="update_username">New Username:</label>
    <input type="text" id="update_username" name="update_username" required>
    <br>
    <label for="update_password">New Password:</label>
    <input type="password" id="update_password" name="update_password" required>
    <br>
    <input type="submit" name="update" value="Update User">
</form>
</body>
</html>