<div class="navbar">
    <ul class="menu">
    <li><a href="/">Home</a></li>
    <li><a href="about.php">About</a></li>
    <li><a href="#">Contact</a></li>
    <?php
        if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 1) {
            echo '<li><a href="admin.php">Admin</a></li>';
            echo '<li><a href="fileUpload.php">Upload Data</a></li>';
            echo '<li><a href="delete.php">Delete Data</a></li>';
            echo '<li><a href="addSensor.php">Add Sensor</a></li>';
        }
    ?>
    <li><a href="query.php">Query</a></li>
    <li><a href="blog.php">Blog</a></li>
    <?php
        if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 1) {
           echo '<li><a href="logout.php">Logout</a></li>';
        } else {
            echo '<li><a href="login.php">Login</a></li>';
        }
    ?>
    </ul>
</div>