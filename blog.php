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
    include ("blog.html");

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $blogTitle = $_POST['title'];
        $blogContent = $_POST['content'];

        $sqlBlog = "INSERT INTO BlogPosts (title, content) VALUES (?, ?)";
        $stmt_blog = mysqli_prepare($conn, $sqlBlog);
        mysqli_stmt_bind_param($sqlBlog, "ss", $blogTitle, $blogContent);
    }
?>