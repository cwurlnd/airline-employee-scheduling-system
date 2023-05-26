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

  putenv("ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe/");
  
  $conn = oci_connect("timmy","timmy","xe")
	  or die("<br>Couldn't connect");

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
    <title>Search Flights | ND Air</title>
    <link rel="stylesheet" href="nav.css" />
    <link rel="stylesheet" href="main.css" />
    <link rel="stylesheet" href="/css/table-page.css" type="text/css" />
	<link href="https://fonts.googleapis.com/css2?family=League+Gothic&display=swap" rel="stylesheet">
  </head>
  <body>
    <nav>
      <a href="empindex.php">
		<img src="../icons/NDAIR.png" alt="ND Air logo" class="logo">
	  </a>
      <ul>
        <li><a href="#" class="current">Search Flights</a></li>
        <li><a href="current-requests.php">Current Requests</a></li>
        <li><a href="my-schedule.php">My Schedule</a></li>
        <li><a href="logout.php">Log Out</a></li>
      </ul>
    </nav>
    <main>
      <form method="GET" action="search-flights.php">

	<?php 
	  echo '<div class="search-bar">';
	    echo '<div class="search-field">';
	      echo '<label for="flight_num">Flight Number:</label>';
  	      echo '<input type="text" name="flight_num" id="flight_num" value='.$_GET['flight_num'].'>';
	    echo '</div>';
	  
	  	$query = "select distinct dept_city from flights order by dept_city";
	  	$stmt = oci_parse($conn, $query);
	  	oci_execute($stmt);
	  	echo '<div class="search-field">';
	      echo '<label for="dept_city">Departure City</label>';
  	      echo '<select name="dept_city" id="dept_city">';
	        echo '<option value=""></option>';
	        while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
	          if ($row['DEPT_CITY'] == $_GET['dept_city']) {
	            $selected = 'selected'; 
	          } else {
 	            $selected = '';
	          }
	          echo '<option value="'.$row['DEPT_CITY'].'" '.$selected.'>'.$row['DEPT_CITY'].'</option>';
	        }
	      echo '</select>';
		echo '</div>';
	  
 	  	$query = "select distinct arr_city from flights order by arr_city";
	    $stmt = oci_parse($conn, $query);
	    oci_execute($stmt);
	 	echo '<div class="search-field">';
	      echo '<label for="arr_city">Arrival City</label>';
	      echo '<select name="arr_city" id="arr_city">';
	        echo '<option value=""></option>';
	        while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
	          if ($row['ARR_CITY'] == $_GET['arr_city']) {
	            $selected = 'selected'; 
	          } else {
 	            $selected = '';
	          }
	          echo '<option value="'.$row['ARR_CITY'].'" '.$selected.'>'.$row['ARR_CITY'].'</option>';
	        }
	      echo '</select>';
		echo '</div>';
	  
	    $query = "select distinct flight_date from flights order by flight_date";
	    $stmt = oci_parse($conn, $query);
	    oci_execute($stmt);
	    echo '<div class="search-field">';
	      echo '<label for="flight_date">Flight Date</label>';
	      echo '<select name="flight_date" id="flight_date">';
	        echo '<option value=""></option>';
	        while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
	          $date = $row['FLIGHT_DATE'];	  
	          $formatted_date = date("F j, Y", strtotime($date));
	          if ($row['FLIGHT_DATE'] == $_GET['flight_date']) {
	            $selected = 'selected'; 
	          } else {
 	            $selected = '';
	          }
	          echo '<option value="'.$date.'" '.$selected.'>'.$formatted_date.'</option>';
	        }
	      echo '</select>';
	    echo '</div>';
	  
	    $query = "select distinct dept_time from flights order by dept_time";
	    $stmt = oci_parse($conn, $query);
	    oci_execute($stmt);
		echo '<div class="search-field">';
	      echo '<label for="dept_time">Departure Time</label>';
	      echo '<select name="dept_time" id="dept_time">';
	        echo '<option value=""></option>';
	        while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
	          if ($row['DEPT_TIME'] == $_GET['dept_time']) {
	            $selected = 'selected'; 
	          } else {
 	            $selected = '';
	          }
	          echo '<option value="'.$row['DEPT_TIME'].'" '.$selected.'>'.minutesToTime($row['DEPT_TIME']).'</option>';
	        }
	      echo '</select>';
		echo '</div>';
	  ?>
	  <input type="submit" value="Search" class="submit-button">
	</div>
  </form>
  <?php
	  $flight_num  = $_GET['flight_num'];
	  $dept_city   = $_GET['dept_city'];
	  $arr_city    = $_GET['arr_city'];
	  $flight_date = $_GET['flight_date'];
	  $dept_time   = $_GET['dept_time'];
	if ((!isset($flight_num) && !isset($dept_city) && !isset($arr_city) && !isset($flight_date) && !isset($dept_time)) || (strlen($flight_num) == 0 && strlen($dept_city) == 0 && strlen($arr_city) == 0 && strlen($flight_date) == 0 && strlen($dept_time) == 0)) {
	  echo "<h1>Please search the flights to find one you're looking for!";
	} else {

	  $query = "select * from flights where 1=1";

	  if (strlen($flight_num) > 0) {
	    $query .= ' and flight_num = '.$flight_num;
	  }  

	  if (strlen($dept_city) > 0) {
	    $query .= " and dept_city = '".$dept_city."'";
      } 

	  if (strlen($arr_city) > 0) {
	    $query .= " and arr_city = '".$arr_city."'";
	  }

	  if (strlen($flight_date) > 0) {
	    $query .= " and trunc(flight_date) = '".$flight_date."'";
	  } 

	  if (strlen($dept_time) > 0) {
	    $query .= " and dept_time = '".$dept_time."'";
	  }

	  if ($_SESSION['title'] == 'p') {
		$query .= " and pilots_needed > 0";
	  }

	  if ($_SESSION['title'] == 'f') {
		$query .= " and fa_needed > 0";
	  }

	  $stmt = oci_parse($conn, $query);
	  oci_execute($stmt);

  	echo "<table>";
	  echo "<tr>";
	    echo "<th>Flight Number</th>";
	    echo "<th>Departure City</th>";
	    echo "<th>Arrival City</th>";
	    echo "<th>Date</th>";
	    echo "<th>Departure Time</th>";
	    echo "<th>Arrival Time</th>";
	  	if ($_SESSION['title'] == 'p') {
		  echo "<th>Pilots Needed</th>";
	    } else if ($_SESSION['title'] == 'f') {
		  echo "<th>Attendants Needed</th>";
	    }
		echo '<th></th>';
		echo '<th></th>';
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
		if ($i > 100) {
			break;
		}
	    echo '<tr class="'.$odd.'">';
	      echo "<td>".$row['FLIGHT_NUM']."</td>";
	      echo "<td>".$row['DEPT_CITY']."</td>";
	      echo "<td>".$row['ARR_CITY']."</td>";
	      echo "<td>".$formatted_date."</td>";
	      echo "<td>".minutesToTime($row['DEPT_TIME'])."</td>";
	      echo "<td>".minutesToTime($row['ARR_TIME'])."</td>";
	  	  if ($_SESSION['title'] == 'p') {
		    echo "<td>".$row['PILOTS_NEEDED']."</td>";
	      } else if ($_SESSION['title'] == 'f') {
		    echo "<td>".$row['FA_NEEDED']."</td>";
	      }
	      echo "<td>";
	        echo "<a class='button' href='request.php?id=". $row['ID'] . "'>Request</a>";
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
	if ($i > 100) {
		echo '<div class="e-message">Showing first 100 results. Narrow your search to find your flight.</div>';
	}
	oci_close($conn);
	}
      ?>
    </main>
    <script src="overlay.js"></script>
  </body>
</html>

