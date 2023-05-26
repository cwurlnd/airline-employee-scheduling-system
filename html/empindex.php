<?php 
session_start();
	
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["title"] !== 'p' && $_SESSION["title"] !== 'f')){
  header("location: landing.php");
  exit;
} else if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["title"] === 'a') {
      header("location: adminindex.php");
  exit;
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>ND Air Scheduling</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="main.css">
	<link href="https://fonts.googleapis.com/css2?family=League+Gothic&display=swap" rel="stylesheet"/>
    <style>
      
      /* Main content style */
      body {
        background-image: url("airplane.jpg");
        background-size: cover;
      }
      
      main {
        max-width: 800px;
        margin: 50px auto;
        text-align: center;
        background-color: rgba(255, 255, 255, 0.8);
        padding: 20px;
      }
      
      h2 {
        font-size: 3em;
        margin: 0;
      }
      
      p {
        font-size: 1.2em;
        margin: 20px 0;
      }
    </style>
  </head>
  <body>
    <nav>
      <a href="#">
		<img src="../icons/NDAIR.png" alt="ND Air logo" class="logo">
	  </a>
      <ul>
        <li><a href="search-flights.php">Search Flights</a></li>
        <li><a href="current-requests.php">Current Requests</a></li>
        <li><a href="my-schedule.php">My Schedule</a></li>
        <li><a href="logout.php">Log Out</a></li>
      </ul>
    </nav>
    <main>
      <h2>Welcome, <?php
      if ($_SESSION["title"] == 'p') {
        echo "Pilot " . $_SESSION["first_name"];
      } else {
        echo "Flight Attendant " . $_SESSION["first_name"];
      }
      ?></h2>
      <p>Fly high with ND Air!</p>
    </main>
  </body>
</html>

