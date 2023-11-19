<!DOCTYPE html>
<html lang="en">
<head>
  <title>Page Title</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="header">
  
  <h1>ELECTRIK</h1>
  <p>Welcome to Group Electrik</p>

</div>

<div class="navbar">
  <a href="aboutus.html">ABOUT US</a>
  <a href="forgotpassword.html">RESET PASSWORD</a>
  <a href="register.html">REGISTER</a>
  <a href="contact.html">CONTACT</a>
  <a href="login.html">LOGIN</a>
  <a href="dashboard.html">DASHBOARD</a>
  <a href="logout.php" class="right">LOGOUT</a>


  <?php
  session_start();

  if (isset($_SESSION['USER_ID'])) {
    
    echo "You are logged in as: " . $_SESSION['user_id'];
  } else {
    echo "You are not logged in.";
  }
  ?>
</div>
<?php include('footer.php'); ?>

</body>
</html>
