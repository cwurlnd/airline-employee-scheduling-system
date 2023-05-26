<?php 
session_start();
	
// Check if the user is logged in, if not then redirect him to login page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && ($_SESSION["title"] === 'p' || $_SESSION["title"] === 'f')) {
  header("location:empindex.php");
  exit;
} else if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || ($_SESSION["title"] !== 'a')){
  header("location: landing.php");
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
	<link href="https://fonts.googleapis.com/css2?family=League+Gothic&display=swap" rel="stylesheet" />
  </head>
  <body>
  <nav>
      <a href="adminindex.php">
	  	<img src="../icons/NDAIR.png" alt="ND Air logo" class="logo" />
	  </a>
      <ul>
        <li><a href="#" class="current">Current Requests</a></li>
        <li><a href="adjust-employees.php">Add/Remove Employees</a></li>
        <li><a href="insert-flights.php">Insert Flights</a></li>
        <li><a href="logout.php">Log Out</a></li>
      </ul>
    </nav>
    <main>
      <div class="title search-bar">Current Requests</div>
      <?php
	  $query = "SELECT pilot_id AS eid, id, flight_num, dept_city, arr_city, flight_date, dept_time, arr_time, curr_status, tail_num
    FROM flys
    INNER JOIN flights ON flys.flight_id = flights.id
    WHERE flys.curr_status = 'P'
    UNION
    SELECT fa_id AS eid, id, flight_num, dept_city, arr_city, flight_date, dept_time, arr_time, curr_status, tail_num
    FROM attends
    INNER JOIN flights ON attends.flight_id = flights.id
    WHERE attends.curr_status = 'P'
    ORDER BY id";	

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
          echo "<td>";
            echo "<a class='button' href='review.php?flight_id=". $row['ID'] . "&id=" . $row['EID'] . "'> Review Request </a>";
          echo "</td>";
          echo "<td>";
            echo '<button class="button" onclick="on('.$row['ID'].')">View Info</button>';
          echo "</td>";
        echo "</tr>";
		echo '<div class="overlay" onclick="off('.$row['ID'].')" style="display: none;" id="'.$row['ID'].'">';
		  echo '<div class="info">';
			echo '<h2>Flight '.$row['FLIGHT_NUM'].'</h2>';
			echo '<p>'.$formatted_date.'</p>';
			echo '<h4>'.minutesToTime($row['DEPT_TIME']).' Departure from '.$row['DEPT_CITY'].'</h4>';
			$query = "select * from airports where code='".$row['DEPT_CITY']."'";
			$air_stmt = oci_parse($conn, $query);
			oci_execute($air_stmt);
			$airport = oci_fetch_array($air_stmt, OCI_ASSOC);
			echo '<p>'.$airport['CITY'].', '.$airport['AIRPORT_STATE'].' ('.$airport['AIRPORT_NAME'].')</p>';

			echo '<h4>'.minutesToTime($row['ARR_TIME']).' Departure from '.$row['ARR_CITY'].'</h4>';
			$query = "select * from airports where code='".$row['ARR_CITY']."'";
			$air_stmt = oci_parse($conn, $query);
			oci_execute($air_stmt);
			$airport = oci_fetch_array($air_stmt, OCI_ASSOC);
			echo '<p>'.$airport['CITY'].', '.$airport['AIRPORT_STATE'].' ('.$airport['AIRPORT_NAME'].')</p>';

			echo '<h4>Plane #'.$row['TAIL_NUM'].'</h4>';
			$query = "select * from planes where tail_num='".$row['TAIL_NUM']."'";
			$air_stmt = oci_parse($conn, $query);
			oci_execute($air_stmt);
			$plane = oci_fetch_array($air_stmt, OCI_ASSOC);
			echo '<p>Number of seats: '.$plane['SEATS'].'</p>';
			echo '<p>Manufacturer: '.$plane['MANUF'].'</p>';

			$query = "select * from pilots where user_id = ".$row['EID'];
			$user_stmt = oci_parse($conn, $query);
			oci_execute($user_stmt);

			if (oci_fetch($user_stmt)) {
				echo '<h4>Pilot</h4>';			
			} else {
				echo '<h4>Flight Attendant</h4>';			
			}

			$query = "select * from users where id = ".$row['EID'];
			$u_stmt = oci_parse($conn, $query);
			oci_execute($u_stmt);
			$user = oci_fetch_array($u_stmt, OCI_ASSOC);
			echo '<p>'.$user['FIRST_NAME'].' '.$user['LAST_NAME'].'</p>';
			echo '<p><a href="mailto:'.$user['EMAIL'].'">'.$user['EMAIL'].'</a></p>';	
		  echo '</div>';
		echo '</div>';
	  }
	echo "</table>";
      ?>
    </main>
	<script src="overlay.js"></script>
  </body>
</html>

