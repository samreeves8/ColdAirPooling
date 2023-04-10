<div class="navbar">
    <ul class="menu">
       <li><a href="/">Home</a></li>
       <li><a href="#">About</a></li>
       <li><a href="#">Contact</a></li>
       <?php
       if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 1) {
           echo '<li><a href="importCSV.php">Import CSV</a></li>';
        }
        ?>
       <li><a href="query.php">Query</a></li>
       <li><a href="#">Members</a></li>
       <?php
       if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 1) {
           echo '<li><a href="logout.php">Logout</a></li>';
        } else {
           echo '<li><a href="login.php">Login</a></li>';
        }
        ?>
    </ul>
</div>