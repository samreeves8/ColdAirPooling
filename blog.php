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

    $query = "SELECT title, content, member_id FROM BlogPosts";
    $result = $mysqli->query($query);

    // Loop through the result set and display data in containers
    while ($row = $result->fetch_assoc()) {
        echo '<div style="border: 1px solid #000; padding: 10px; margin-bottom: 10px;">';
        echo '<h2>' . $row['title'] . '</h2>';
        echo '<p>' . $row['content'] . '</p>';
        echo '<p style="text-align: right;">Posted by Member ID: ' . $row['member_id'] . '</p>';
        echo '</div>';
    }

    // Close database connection
    $mysqli->close();

    

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $blogTitle = $_POST['title'];
        $blogContent = $_POST['content'];

        //Get the current user's id 
        $queryID = "SELECT id FROM accounts WHERE username = ?";
        $stmt_id = mysqli_prepare($conn, $queryID);
        mysqli_stmt_bind_param($stmt_id, "s", $_SESSION['name']);
        mysqli_stmt_execute($stmt_id);
        $member_id = $stmt_id->get_result();
        mysqli_stmt_close($stmt_id);


        $m_id = null;
        if($member_id->num_rows == 1){
            while ($row = $member_id->fetch_assoc()) {
                //set member id
                $m_id = $row["id"];
            }
        }


        $sqlBlog = "INSERT INTO BlogPosts (title, content, member_id) VALUES (?, ?, ?)";

        //insert blog post
        $stmt_blog = mysqli_prepare($conn, $sqlBlog);
        mysqli_stmt_bind_param($stmt_blog, "ssd", $blogTitle, $blogContent, $m_id);
        mysqli_stmt_execute($stmt_blog);
        mysqli_stmt_close($stmt_blog);
        mysqli_close($conn);
    }
?>