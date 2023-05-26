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

// Process scheduling operation after confirmation
if(isset($_POST["id"])){
    // Include config file
    putenv("ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe/");
    // Connect to the database
    $conn = oci_connect('timmy', 'timmy', 'localhost/XE');
    
    // Prepare an insert statement
    if ($_SESSION['title'] == 'p') {
        $sql = "DELETE FROM flys WHERE pilot_id = :pilot_id and flight_id = :flight_id";
    } else {
        $sql = "DELETE FROM attends WHERE fa_id = :pilot_id and flight_id = :flight_id";
    }
    
    // Prepare statement
    $stmt = oci_parse($conn, $sql);
    
    // Bind variables to the prepared statement as parameters
    oci_bind_by_name($stmt, ":pilot_id", $_SESSION["id"]);
    oci_bind_by_name($stmt, ":flight_id", $_POST["id"]);
        
    if(oci_execute($stmt)){
        header("location: current-requests.php");
        exit();
    } else{
        $e = oci_error($stmt);
        echo $e['message'];
    }
     
    // Close statement
    oci_free_statement($stmt);
    
    // Close connection
    oci_close($conn);
} else{
    $temp = trim($_GET["id"]);
    if(empty($temp)){
        echo "Oops! Something went wrong. Please try again later.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cancel Request</title>
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
                    <h2>Cancel Request</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert">
                            <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                            <p>Are you sure you want to cancel this request?</p>
                            <p>
                                <input type="submit" value="Yes" class="btn btn-success">
                                <a href="current-requests.php" class="btn btn-danger">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
