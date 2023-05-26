<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && ($_SESSION["title"] === 'p' || $_SESSION["title"] === 'f')){
    header("location: empindex.php");
    exit;
} else if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["title"] === 'a') {
    header("location: adminindex.php");
    exit;
}

putenv("ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe/");
// Connect to the database
$conn = oci_connect('timmy', 'timmy', 'localhost/XE');

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if email is empty
    $email = trim($_POST["email"]);
    if(empty($email)){
        $email_err = "Please enter email.";
    }

    $password = trim($_POST["password"]);
    if(empty($password)){
        $password_err = "Please enter password.";
    }

    // Validate credentials
    if(empty($email_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT p.user_id, u.first_name, u.email, u.password FROM pilots p, users u WHERE u.email = :email AND u.id = p.user_id AND u.password = :password";

        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':email', $email);
        oci_bind_by_name($stmt, ':password', $password);

        // Attempt to execute the prepared statement
        if(oci_execute($stmt)){
            // Check if username exists, if yes then verify password
            if(oci_fetch($stmt)){
                // Bind result variables
                $id = oci_result($stmt, 'USER_ID');
                $first_name = oci_result($stmt, 'FIRST_NAME');
                session_start();

                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["first_name"] = $first_name;
                $_SESSION["title"] = "p";

                    // Redirect user to welcome page
                header("location: empindex.php");
            } else{
                $login_err = "Incorrect username or password. Please try again.";
            }
        } else{
            $login_err = "Incorrect username or password. Please try again.";
        }
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }

        // Free statement
        oci_free_statement($stmt);
}

// Close connection
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
<body>
    <div class="wrapper">
        <h2>Pilot Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? Please talk to an admin or database administrator.</p>
        </form>
    </div>
</body>
</html>