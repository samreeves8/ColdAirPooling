<?php
    session_start();
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/nav.css">
    <link rel="stylesheet" href="styles/import.css">
    <link rel="stylesheet" href="styles/blog.css">
    <title>Document</title>
</head>

<body>
    <?php include 'navBar.php';?>
</body>

</html>

<?php
    if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 1) {
        include ("blog.html");
    }
    

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $blogTitle = $_POST['title'];
        $blogContent = $_POST['content'];

        $queryID = "SELECT id FROM accounts WHERE username = ?;";
        mysqli_stmt_bind_param($queryID, "s", $_SESSION['name']);
        mysqli_stmt_execute($queryID);
        $member_id = $queryID->get_result;
        mysqli_stmt_close($queryID);

        if($member_id->num_rows == 1){
            while ($row = $member_id->fetch_assoc()) {
                //Echo's rows based on table
                echo $row["id"];
            }
        }


        $sqlBlog = "INSERT INTO BlogPosts (title, content) VALUES (?, ?);";


        $stmt_blog = mysqli_prepare($conn, $sqlBlog);
        mysqli_stmt_bind_param($stmt_blog, "ss", $blogTitle, $blogContent);
        mysqli_stmt_execute($stmt_blog);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
?>