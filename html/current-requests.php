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

// Connect to the database using OCI
putenv("ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe/");
$conn = oci_connect('timmy', 'timmy', 'localhost/XE');

function minutesToTime($minutes) {
  $hours = floor($minutes / 60);
  $minutes = $minutes % 60;

  $time = sprintf('%02d:%02d',$hours,$minutes);

  return $time;
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Current Requests | ND Air</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="/css/table-page.css">
	<link href="https://fonts.googleapis.com/css2?family=League+Gothic&display=swap" rel="stylesheet">
  </head>
  <body>
    <nav>
      <a href="empindex.php">
	  	<img src="../icons/NDAIR.png" alt="ND Air logo" class="logo">
	  </a>
      <ul>
        <li><a href="search-flights.php">Search Flights</a></li>
        <li><a href="#" class="current">Current Requests</a></li>
        <li><a href="my-schedule.php">My Schedule</a></li>
        <li><a href="logout.php">Log Out</a></li>
      </ul>
    </nav>
    <main>
      <div class="title search-bar">Current Requests</div>
      <?php
	if ($_SESSION["title"] == 'p') {
	  $query = "select f.tail_num, flys.pilot_id, flys.flight_id, f.flight_num, f.dept_city, f.arr_city, f.flight_date, f.dept_time, f.arr_time, flys.curr_status from flys, flights f, users u where flys.pilot_id = u.id and flys.flight_id = f.id and flys.curr_status != 'A' and ";
	  $query .= "u.id = ".$_SESSION["id"];	
	} else {
      $query = "select f.tail_num, attends.fa_id, attends.flight_id, f.flight_num, f.dept_city, f.arr_city, f.flight_date, f.dept_time, f.arr_time, attends.curr_status from attends, flights f, users u where attends.fa_id = u.id and attends.flight_id = f.id and attends.curr_status != 'A' and ";
	  $query .= "u.id = ".$_SESSION["id"];	
  }

	$stmt = oci_parse($conn, $query);
	oci_execute($stmt);
  	$e = oci_error($stmt);

	echo "<table>";
	  echo "<tr>";
	    echo "<th>Flight Number</th>";
      echo "<th>Departure City</th>";
	    echo "<th>Arrival City</th>";
	    echo "<th>Date</th>";
	    echo "<th>Departure Time</th>";
	    echo "<th>Arrival Time</th>";
		echo "<th>Current Status</th>";
		echo "<th></th>";
		echo "<th></th>";
	  echo "</tr>";
	  
	  $i = 1;
	  while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
	    $date = $row['FLIGHT_DATE'];	  
        $formatted_date = date("F j, Y", strtotime($date));

		if ($i % 2 == 0) {
		  $odd = '';
		} else {
          $odd = 'odd';
		}
		$i += 1;
        echo '<tr class="'.$odd.'">';
          echo "<td>".$row['FLIGHT_NUM']."</td>";
          echo "<td>".$row['DEPT_CITY']."</td>";
          echo "<td>".$row['ARR_CITY']."</td>";
          echo "<td>".$formatted_date."</td>";
          echo "<td>".minutesToTime($row['DEPT_TIME'])."</td>";
          echo "<td>".minutesToTime($row['ARR_TIME'])."</td>";
		  echo "<td>".$row['CURR_STATUS']."</td>";
          echo "<td>";
            echo "<a class='button' href='cancel.php?id=". $row['FLIGHT_ID'] . "'> Cancel Request</a>";
          echo "</td>";
          echo "<td>";
            echo '<button class="button" onclick="on('.$row['FLIGHT_ID'].')">View Info</button>';
          echo "</td>";
        echo "</tr>";
		echo '<div class="overlay" onclick="off('.$row['FLIGHT_ID'].')" style="display: none;" id="'.$row['FLIGHT_ID'].'">';
		  echo '<div class="info">';
			echo '<h2>Flight '.$row['FLIGHT_NUM'].'</h2>';
			echo '<p>'.$formatted_date.'</p>';

			echo '<h4>'.minutesToTime($row['DEPT_TIME']).' Departure from '.$row['DEPT_CITY'].'</h4>';
			$query = "select * from airports where code='".$row['DEPT_CITY']."'";
			$air_stmt = oci_parse($conn, $query);
			oci_execute($air_stmt);
			$airport = oci_fetch_array($air_stmt, OCI_ASSOC);
			echo '<p>'.$airport['CITY'].', '.$airport['AIRPORT_STATE'].' ('.$airport['AIRPORT_NAME'].')</p>';

			echo '<h4>'.minutesToTime($row['ARR_TIME']).' Arrival in '.$row['ARR_CITY'].'</h4>';
			$query = "select * from airports where code='".$row['ARR_CITY']."'";
			$air_stmt = oci_parse($conn, $query);
			oci_execute($air_stmt);
			$airport = oci_fetch_array($air_stmt, OCI_ASSOC);
			echo '<p>'.$airport['CITY'].', '.$airport['AIRPORT_STATE'].' ('.$airport['AIRPORT_NAME'].')</p>';

			echo '<h4>Plane No. '.$row['TAIL_NUM'].'</h4>';
			$query = "select * from planes where tail_num='".$row['TAIL_NUM']."'";
			$air_stmt = oci_parse($conn, $query);
			oci_execute($air_stmt);
			$plane = oci_fetch_array($air_stmt, OCI_ASSOC);

			echo '<p>Number of seats: '.$plane['SEATS'].'</p>';
			echo '<p>Manufacturer: '.$plane['MANUF'].'</p>';
		  echo '</div>';
		echo '</div>';
	  }
	  echo "</table>";
      ?>
    </main>
	<script src="overlay.js"></script>
  </body>
</html>

