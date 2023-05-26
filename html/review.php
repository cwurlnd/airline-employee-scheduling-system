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

if(isset($_POST["id"]) && isset($_POST["flight_id"])){
    $id = $_POST["id"];
    $flight_id = $_POST["flight_id"];
    $status = $_POST["status"];
    $error = "";

    putenv("ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe/");
    // Connect to the database
    $conn = oci_connect('timmy', 'timmy', 'localhost/XE');
    
    // Prepare an insert statement
    $sql = "select * from pilots where user_id = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $id);
    oci_execute($stmt);

    if (oci_fetch($stmt)) {
        if ($status == "A") {
            $sql = "update flys set curr_status = 'A' where pilot_id = :pilot_id and flight_id = :flight_id";
            $sql2 = "update flights set pilots_needed = pilots_needed - 1 where id = :flight_id";
            $stmt2 = oci_parse($conn, $sql2);
            oci_bind_by_name($stmt2, ":flight_id", $flight_id);
            oci_execute($stmt2);
            $e = oci_error($stmt2);
            $error .= $e["message"];
        } else {
            $sql = "update flys set curr_status = 'D' where pilot_id = :pilot_id and flight_id = :flight_id";
        }
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":pilot_id", $id);
        oci_bind_by_name($stmt, ":flight_id", $flight_id);
        oci_execute($stmt);
        $e = oci_error($stmt);
        $error .= $e["message"];
        
    } else {
        if ($status == "A") {
            $sql = "update attends set curr_status = 'A' where fa_id = :fa_id and flight_id = :flight_id";
            $sql2 = "update flights set fa_needed = fa_needed - 1 where id = :flight_id";
            $stmt2 = oci_parse($conn, $sql2);
            oci_bind_by_name($stmt2, ":flight_id", $flight_id);
            oci_execute($stmt2);
            $e = oci_error($stmt2);
            $error .= $e["message"];
        } else {
            $sql = "update attends set curr_status = 'D' where fa_id = :fa_id and flight_id = :flight_id";
        }
        $stmt = oci_parse($conn, $sql);
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":fa_id", $id);
        oci_bind_by_name($stmt, ":flight_id", $flight_id);
        oci_execute($stmt);
        $e = oci_error($stmt);
        $error .= $e["message"];
    }    

    if (empty($error)) {
        header("location:a-current-requests.php");
        exit;
    } else {
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
    <title>Admin Approval</title>
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
                    <h2>Admin Approval</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                        <input type="hidden" name="flight_id" value="<?php echo trim($_GET["flight_id"]); ?>"/>
                        <div class="form-group">
                            <label>Approval Status</label>
                            <select name="status" class="form-control">
                                <option value="A">Approve</option>
                                <option value="D">Deny</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Submit" class="btn btn-success">
                            <a href="a-current-requests.php" class="btn btn-danger">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
