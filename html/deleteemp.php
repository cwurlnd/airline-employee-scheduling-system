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

// Process scheduling operation after confirmation
if(isset($_POST["id"])){
    $id = $_POST["id"];
    // Include config file
    putenv("ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe/");
    // Connect to the database
    $conn = oci_connect('timmy', 'timmy', 'localhost/XE');

    $error = "";
    
    $sql = "select * from pilots where user_id = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $id);
    oci_execute($stmt);

    if (oci_fetch($stmt)) {
      $sql2 = "delete from pilots where user_id = :id";
    } else {
      $sql2 = "delete from flightattendants where user_id = :id";
    }

    $stmt2 = oci_parse($conn, $sql2);
    oci_bind_by_name($stmt2, ":id", $id);
    oci_execute($stmt2);
    $e = oci_error($stmt2);
    $error .= $e['message'];
   	$numRowsDeleted = oci_num_rows($stmt2);
  
    $sql = "delete from users where id = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $_POST["id"]);
    oci_execute($stmt);
    $e = oci_error($stmt);
    $error .= $e['message'];
    $numRowsDeleted = oci_num_rows($stmt);

    if(empty($error)){
        header("location: adjust-employees.php");
        exit;
    } else{
        echo $error;
    }
     
    // Close statement
    oci_free_statement($stmt);
    oci_free_statement($stmt2);
    
    // Close connection
    oci_close($conn);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Employee</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
            padding: 1em;
            background-color: white;
            opacity: 1;
            border-radius: 25px;
            margin-top:25px;
        }
        body {
            background-image: url("clouds.jpg");
            background-size: cover;
            opacity: 1;
            font-family: 'League Gothic', sans-serif;  
            font-style: italic;
            font-weight: normal;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&display=swap" rel="stylesheet" />
</head>
<body style="background-color:white;">
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2>Delete Employee</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert">
                            <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                            <p>Are you sure you want to delete this employee?</p>
                            <p>
                                <input type="submit" value="Yes" class="btn btn-success">
                                <a href="adjust-employees.php" class="btn btn-danger">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
