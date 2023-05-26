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

$conn = oci_connect('timmy', 'timmy', 'localhost/XE');
 
// Define variables and initialize with empty values
$email = $password = $confirm_password = $code = $firstname = $lastname = "";
$email_err = $password_err = $confirm_password_err = $code_err = $firstname_err = $lastname_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate email
    $email = trim($_POST["email"]);
    if(empty($email)){
        $email_err = "Please enter a email.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = :email";

        $stmt = oci_parse($conn, $sql);

        if($stmt){
            // Bind variables to the prepared statement as parameters
            oci_bind_by_name($stmt, ":email", $email);

            // Attempt to execute the prepared statement
            if(oci_execute($stmt)){
                /* store result */
                $num_rows = oci_fetch_all($stmt, $res);

                if($num_rows == 1){
                    $email_err = "This email is already taken.";
                }
            } else{
                $e = oci_error($stmt);
                echo $e['message'] . $e['sqltext']. $e['offset'];
            }

            // Close statement
            oci_free_statement($stmt);
        }
    }
    
    // Validate password
    $password = trim($_POST["password"]);
    if(empty($password)){
        $password_err = "Please enter a password.";     
    }
    
    // Validate confirm password
    $confirm_password = trim($_POST["confirm_password"]);
    if(empty($confirm_password)){
        $confirm_password_err = "Please confirm password.";     
    } 
    if(empty($password_err) && ($password != $confirm_password)){
        $confirm_password_err = "Password did not match.";
    }

    $code = trim($_POST["code"]);
    if(empty($code)){
        $code_err = "Please confirm secret code.";     
    } 
    if (!empty($code) && $code != "ramzi") {
        $code_err = "Secret code is not correct.";
    }

    $firstname = trim($_POST["firstname"]);
    if(empty($firstname)){
        $firstname_err = "Please enter a first name.";     
    }

    $lastname = trim($_POST["lastname"]);
    if(empty($lastname)){
        $lastname_err = "Please enter a last name.";     
    } 
    
    // Check input errors before inserting in database
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($code_err) && empty($firstname_err) && empty($lastname_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO users (email, password, first_name, last_name) VALUES (:email, :password, :firstname, :lastname)";

        $stmt = oci_parse($conn, $sql);

        if($stmt){
            // Bind variables to the prepared statement as parameters
            oci_bind_by_name($stmt, ":email", $email);
            oci_bind_by_name($stmt, ":password", $password);
            oci_bind_by_name($stmt, ":firstname", $firstname);
            oci_bind_by_name($stmt, ":lastname", $lastname);

            // Attempt to execute the prepared statement
            if(oci_execute($stmt)){

                // Get the user ID
                $user_id_sql = "SELECT id FROM users WHERE email = :email";
                $user_id_stmt = oci_parse($conn, $user_id_sql);

                oci_bind_by_name($user_id_stmt, ":email", $email);
                
                if(oci_execute($user_id_stmt)){
                    $row = oci_fetch_assoc($user_id_stmt);
                    $user_id = $row['ID'];
                    
                    // Insert the user ID into the admins table
                    $admin_sql = "INSERT INTO admins (user_id) VALUES (:user_id)";
                    $admin_stmt = oci_parse($conn, $admin_sql);
                    
                    oci_bind_by_name($admin_stmt, ":user_id", $user_id);
                    
                    if(oci_execute($admin_stmt)){
                        header("location: adminlogin.php");
                    } else{
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    oci_free_statement($admin_stmt);
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
                
                oci_free_statement($user_id_stmt);
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            oci_free_statement($stmt);
        }
    }

}
oci_close($conn);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
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
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2>Sign Up</h2>
                    <p>Please fill this form to create an account.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>    
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                            <span class="invalid-feedback"><?php echo $password_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="firstname" class="form-control <?php echo (!empty($firstname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $firstname; ?>">
                            <span class="invalid-feedback"><?php echo $firstname_err; ?></span>
                        </div> 
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lastname" class="form-control <?php echo (!empty($lastname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $lastname; ?>">
                            <span class="invalid-feedback"><?php echo $lastname_err; ?></span>
                        </div> 
                        <div class="form-group">
                            <label>Secret Code</label>
                            <input type="text" name="code" class="form-control <?php echo (!empty($code_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $code; ?>">
                            <span class="invalid-feedback"><?php echo $code_err; ?></span>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <a href="landing.php" class="btn btn-danger ml-2">Cancel</a>
                        </div>
                        <p>Already have an account? <a href="adminlogin.php">Login here</a>.</p>
                    </form>
                </div>
            </div>        
        </div>
    </div>    
</body>
</html>