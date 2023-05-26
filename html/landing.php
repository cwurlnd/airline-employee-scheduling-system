<?php 
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && ($_SESSION["title"] === 'p' || $_SESSION["title"] === 'f')){
  header("location: empindex.php");
  exit;
} else if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["title"] === 'a') {
  header("location: adminindex.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="landing.css">
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&display=swap" rel="stylesheet">
    <title>ND Air</title>
  </head>
    
  <body>
    <div class="content">
      <div class="links-container">
        <img src="../icons/NDAIR.png" alt="ND Air logo" class="logo">
        <a href="pilotlogin.php" class="login-button">Pilot Login</a>
        <a href="falogin.php" class="login-button">Flight Attendant Login</a>
        <a href="adminlogin.php" class="login-button">Admin Login</a>
        <a href="register.php" class="login-button">Register</a>
      </div>
    </div>
  </body>
</html>
