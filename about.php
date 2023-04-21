<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="icon" type="image/png" href="images/Western Logo.png">
    <link rel = "stylesheet" href = "styles/about.css">
    <link rel="stylesheet" href="styles/nav.css">
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

<?php include 'navBar.php';?>

<div class="about-wrapper">
    <h1>
      About Us
    </h1>
    <p>
    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
    et dolore magna aliqua. Dignissim sodales ut eu sem integer. Lorem mollis aliquam ut porttitor leo.
    Suspendisse faucibus interdum posuere lorem. Eros in cursus turpis massa tincidunt. Ante in nibh mauris
    cursus mattis. Condimentum mattis pellentesque id nibh tortor. Pulvinar pellentesque habitant morbi tristique
    senectus et. Aliquam malesuada bibendum arcu vitae. Mauris rhoncus aenean vel elit scelerisque mauris
    pellentesque pulvinar pellentesque. Enim nunc faucibus a pellentesque sit amet porttitor eget dolor.
    Massa id neque aliquam vestibulum morbi blandit cursus risus at. Nec nam aliquam sem et tortor consequat
    id porta.
    </p>
</div>

<footer>
  <p>    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
    et dolore magna aliqua. Dignissim sodales ut eu sem integer. Lorem mollis aliquam ut porttitor leo.
    Suspendisse faucibus interdum posuere lorem. Eros in cursus turpis massa tincidunt. Ante in nibh mauris
    cursus mattis.</p>
</footer>

</body>
</html>