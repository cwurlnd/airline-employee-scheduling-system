<?php 
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && ($_SESSION["title"] === 'p' || $_SESSION["title"] === 'f')){
  header("location: empindex.php");
  exit;
} else if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["title"] !== 'a') {
  header("location: landing.php");
  exit;
}

putenv("ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe/");
$conn = oci_connect('timmy', 'timmy', 'localhost/XE');

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Add/Remove Employees | ND Air</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="main.css">
	<link rel="stylesheet" href="/css/table-page.css"/>
	<link href="https://fonts.googleapis.com/css2?family=League+Gothic&display=swap" rel="stylesheet" />
  </head>
  <body>
    <nav>
      <a href="adminindex.php">
		<img src="../icons/NDAIR.png" alt="ND Air logo"  class="logo" />
	  </a>
      <ul>
        <li><a href="a-current-requests.php">Current Requests</a></li>
        <li><a href="#" class="current">Add/Remove Employees</a></li>
        <li><a href="insert-flights.php">Insert Flights</a></li>
        <li><a href="logout.php">Log Out</a></li>
      </ul>
    </nav>
    <main>
	  <div class="add">	
	  	<div class="title">Add/Remove Employees</div>
        <div class="add-buttons">
			<a class="button" href="addpilot.php">Add New Pilot</a>
        	<a class="button" href="addfa.php">Add New Flight Attendant</a>
		</div>
	  </div>

      <?php
      $query = "SELECT COALESCE(p.user_id, f.user_id) AS id, 
      u.first_name, 
      u.last_name, 
      u.email, 
      CASE WHEN p.user_id IS NOT NULL THEN 'Pilot' 
           WHEN f.user_id IS NOT NULL THEN 'Flight Attendant' 
           ELSE 'Unknown' END AS Position
      FROM users u 
      LEFT JOIN pilots p ON u.id = p.user_id 
      LEFT JOIN flightattendants f ON u.id = f.user_id 
      WHERE (p.user_id IS NOT NULL OR f.user_id IS NOT NULL) order by last_name
      ";
      

      $stmt = oci_parse($conn, $query);
      oci_execute($stmt);
      $e = oci_error($stmt);

      echo "<table>";
        echo "<tr>";
      	  echo "<th>ID</th>";
          echo "<th>First Name</th>";
          echo "<th>Last Name</th>";
          echo "<th>Email</th>";
          echo "<th>Position</th>";
	      echo "<th></th>";
        echo "</tr>";

		$i = 1;
        while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
          if ($i % 2 == 0) {
			$odd = '';
		  } else {
		    $odd = 'odd';
		  }
		  $i += 1;
		  echo '<tr class="'.$odd.'">';
            echo "<td>".$row['ID']."</td>";
            echo "<td>".$row['FIRST_NAME']."</td>";
            echo "<td>".$row['LAST_NAME']."</td>";
            echo '<td><a href="mailto:'.$row['EMAIL'].'">'.$row['EMAIL']."</a></td>";
            echo "<td>".$row['POSITION']."</td>";
            echo "<td>";
              echo "<a class='button' href='deleteemp.php?id=". $row['ID'] . "'> Delete Employee </a>";
            echo "</td>";
          echo "</tr>";
      	}
      echo "</table>";
      ?>

    </main>
  </body>
</html>

