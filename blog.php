<?php
    session_start();
    $conn = new mysqli('localhost', 'gunniso1_Admin', 'gunnisoncoldair', 'gunniso1_SensorData');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && isset($_POST['content'])){
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



            $current_date = date('Y-m-d');
            $date = new DateTime();
            $date_string = $date->format('Y-m-d');



            $sqlBlog = "INSERT INTO BlogPosts (title, content, member_id, date) VALUES (?, ?, ?, ?)";

            //insert blog post
            $stmt_blog = mysqli_prepare($conn, $sqlBlog);
            mysqli_stmt_bind_param($stmt_blog, "ssds", $blogTitle, $blogContent, $m_id, $date_string);
            mysqli_stmt_execute($stmt_blog);
            mysqli_stmt_close($stmt_blog);
            mysqli_close($conn);

            header("Location: {$_SERVER['REQUEST_URI']}?success=true");
            exit();

        }

        if (isset($_POST['post_id'])) {
            $post_id = $_POST['post_id'];
            $query = "DELETE FROM BlogPosts WHERE post_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $post_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            $response = array('success' => true, 'message' => 'Post deleted successfully!');
            echo json_encode($response);
            header("Location: {$_SERVER['REQUEST_URI']}?success=true"); 
            exit();
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
    <link rel="stylesheet" href="styles/import.css">
    <link rel="stylesheet" href="styles/blog.css">
    <title>Blog</title>
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'navBar.php';?>
</body>


<!-- Deletes Post -->
<script>
    function deletePost(post_id) {
        if (confirm("Are you sure you want to delete this post?")) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status == 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            alert(response.message);
                            location.href = '<?php echo $_SERVER['REQUEST_URI']; ?>';
                        } else {
                            alert(response.message);
                        }
                    } else {
                        alert('There was a problem with the request.');
                    }   
                }
            };
        xhr.open('POST', 'blog.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('post_id=' + post_id);
        }
    }
</script>


</html>

<?php
    if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 1) {
        include ("blog.html");
    }


    $query_main = "SELECT post_id, title, content, member_id, date FROM BlogPosts LIMIT 5";
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
        echo '<p class="date">Posted on: ' . $row['date'] . '</p>';
        
        
        if($_SESSION['name'] == $curr_member){
            // Get the post_id
            $post_id = $row['post_id'];
            // Add a delete button
            echo '<button onclick="deletePost(' . $post_id . ')">Delete</button>';
            
        }
        echo '</div>';
    }

    
?>