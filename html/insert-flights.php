<?php 
// Initialize the session
session_start();

// Check if the user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["title"] !== 'a'){
  header("location: landing.php");
  exit;
}

$login_err = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
  // Get the uploaded file information
  $file_name = $_FILES['csv_file']['name'];
  $file_tmp_name = $_FILES['csv_file']['tmp_name'];

  // Open a connection to the database
  putenv("ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe/");

  $conn = oci_connect('timmy', 'timmy', 'localhost/XE');

  // Open the CSV file for reading
  if (($handle = fopen($file_tmp_name, "r")) !== FALSE) {
    // Loop through each row in the CSV file
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      if (count($data) != 10) {
        $login_err = "Error: Incorrect number or columns.";
        break;
      }

      // Insert the new flight into the flights table
      $stmt = oci_parse($conn, "INSERT INTO flights (flight_num, dept_city, arr_city, flight_date, dept_time, arr_time, distance, tail_num, pilots_needed, fa_needed)
                                VALUES (:flight_num, :dept_city, :arr_city, TO_DATE(:flight_date, 'MM/DD/YY'), :dept_time, :arr_time, :distance, :tail_num, :pilots_needed, :fa_needed)");

      oci_bind_by_name($stmt, ":flight_num", $data[0]);
      oci_bind_by_name($stmt, ":dept_city", $data[1]);
      oci_bind_by_name($stmt, ":arr_city", $data[2]);
      oci_bind_by_name($stmt, ":flight_date", $data[3]);
      oci_bind_by_name($stmt, ":dept_time", $data[4]);
      oci_bind_by_name($stmt, ":arr_time", $data[5]);
      oci_bind_by_name($stmt, ":distance", $data[6]);
      oci_bind_by_name($stmt, ":tail_num", $data[7]);
      oci_bind_by_name($stmt, ":pilots_needed", $data[8]);
      oci_bind_by_name($stmt, ":fa_needed", $data[9]);
      if (!oci_execute($stmt)) {
        $login_err = "Error: Check rows for valid data and please enter correct format. Valid rows properly inserted.";
      }
    }

    // Close the CSV file handle
    fclose($handle);
    // Close the database connection
    oci_close($conn);

    // Redirect to the index page
    if (empty($login_err)) {
      $login_err = "Success! Flights added to the database!";
    }
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Insert Flights | ND Air</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="main.css">
	<link rel="stylesheet" href="/css/insert-flights.css"/>
	<link href="https://fonts.googleapis.com/css2?family=League+Gothic&display=swap" rel="stylesheet" />
  </head>
  <body>
    <nav>
      <a href="adminindex.php">
		<img src="../icons/NDAIR.png" alt="ND Air logo" class="logo" />
	  </a>
      <ul>
        <li><a href="a-current-requests.php">Current Requests</a></li>
        <li><a href="adjust-employees.php">Add/Remove Employees</a></li>
        <li><a href="#" class="current">Insert Flights</a></li>
        <li><a href="logout.php">Log Out</a></li>
      </ul>
    </nav>
    <main>
      <div class="title">Insert Flights</div>
      <?php 
        if(!empty($login_err)){
            echo '<br><h3>' . $login_err . '</h3>';
        }        
      ?>
      <form method="POST" enctype="multipart/form-data">
	  	<div class="csv-upload">
          <label for="csv_file">Select CSV file to upload:</label>
          <input type="file" name="csv_file" id="csv_file" accept=".csv">
		</div>
        <button type="submit" class = "submit-button">Upload</button>
      </form>
      <br>
	  <div class="columns">
      	<div class="columns-title">Columns, from left to right</div>
      	<ul>
          <li>Flight Number, such as 4617 (datatype: number)</li>
          <li>Departure Airport, such as MIA or LAX (datatype: varchar)</li>
          <li>Arrival Airport, such as MIA or LAX (datatype: varchar)</li>
          <li>Date, such as 04/09/2001 (datatype: date)</li>
          <li>Departure Time, such as 360 hours past midnight (datatype: number)</li>
          <li>Arrival Time, such as 360 hours past midnight (datatype: number)</li>
          <li>Tail Number, such as N396SW (datatype: varchar)</li>
          <li>Pilots needed, such as 3 (datatype: number)</li>
          <li>Flight Attendants needed, such as 3 (datatype: number)</li>
      	</ul>
      <br>
	  </div>
      <form method="GET" action="example.csv">
        <button type="submit" class="submit-button">Download Example CSV</button>
      </form>
    </main>
  </body>
</html>

