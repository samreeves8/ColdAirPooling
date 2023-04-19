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

    $query_main = "SELECT title, content, member_id FROM BlogPosts";
    $stmt_main = mysqli_prepare($conn, $query_main);
    mysqli_stmt_execute($stmt_main);
    $result_main = $stmt_main->get_result();
    mysqli_stmt_close($stmt_main);


    // Loop through the result set and display data in containers
    while ($row = $result_main->fetch_assoc()) {
        echo '<div class="container-main">';
        echo '<h2>' . $row['title'] . '</h2>';
        echo '<p>' . $row['content'] . '</p>';


        //query's for member   
        $query_member = "SELECT username FROM accounts WHERE id = ?";
        $stmt_member = mysqli_prepare($conn, $query_member);
        mysqli_stmt_bind_param($stmt_member, "i", $row['member_id']);
        mysqli_stmt_execute($stmt_member);
        $result_member = $stmt_member->get_result();
        mysqli_stmt_close($stmt_member);
        $curr_member = null;
        while($row2 = $result_member->fetch_assoc()){
            //Set's member
            $curr_member = $row2["username"];
        }


        echo '<p class="member">Posted by: ' . $curr_member . '</p>';
        
        if($_SESSION['name]'] == $curr_member){
            // Get the post_id
            $post_id = $row['id'];
        
            // Add a delete button
            echo '<button onclick="deletePost(' . $post_id . ')">Delete</button>';
            

            echo '
            <script>
            function deletePost(post_id) {
                if(confirm("Are you sure you want to delete this post?")) {
                    // Send AJAX request to delete the post
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "delete_post.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            // Reload the page to show the updated list of posts
                            location.reload();
                        }
                    };
                    xhr.send("post_id=" + post_id);
                }
            }
            </script>';


        }

        echo '</div>';
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
        $post_id = $_POST['post_id'];

        // Prepare and execute the SQL statement to delete the row with the specified post_id
        $stmt = mysqli_prepare($conn, "DELETE FROM BlogPosts WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $post_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $blogTitle = $_POST['title'];
        $blogContent = $_POST['content'];

        //Get the current user's id 
        $query_id = "SELECT id FROM accounts WHERE username = ?";
        $stmt_id = mysqli_prepare($conn, $query_id);
        mysqli_stmt_bind_param($stmt_id, "s", $_SESSION['name']);
        mysqli_stmt_execute($stmt_id);
        $result_id = $stmt_id->get_result();
        mysqli_stmt_close($stmt_id);


        $m_id = null;
        if($result_id->num_rows == 1){
            while ($row = $result_id->fetch_assoc()) {
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