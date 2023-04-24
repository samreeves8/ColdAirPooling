<head>
    <link rel="stylesheet" href="styles/header.css">
</head>
<body>
    <header>
        <div class="header-wrapper">
          <div class="logo"><img src="images/Western Logo.png" alt="Header Image"></div>
          <?php
            if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 1) {
              echo '<div class="login-link"><a href="logout.php">Logout</a></div>';
            } else {
              echo '<div class="login-link"><a href="login.php">Login</a></div>';
            }
          ?>
        </div>
    </header>
<hr>