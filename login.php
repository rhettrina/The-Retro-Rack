<?php
session_start();

require_once "config.php";

// Initialize variables
$login = $password = "";
$error = "";

// If request method is POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if login field is empty
    if (empty(trim($_POST['login']))) {
        $error = "empty_login";
    } else {
        $login = trim($_POST['login']);
    }

    // Check if password field is empty
    if (empty(trim($_POST['password']))) {
        if ($error === "") { // Only set if no previous error
            $error = "empty_password";
        }
    } else {
        $password = trim($_POST['password']);
    }

    // Validate credentials if no errors
    if ($error === "") {
        // Prepare SQL statement to check username or email
        $sql = "SELECT id, username, password FROM users WHERE username = ? OR email = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind parameters: two strings
            mysqli_stmt_bind_param($stmt, "ss", $param_login, $param_login);

            // Set parameters
            $param_login = $login;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if user exists
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        // Verify password
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            session_start();
                            session_regenerate_id(true); // Prevent session fixation

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            // Redirect user to welcome page
                            header("location: index.php");
                            exit;
                        } else {
                            // Password is not valid
                            $error = "invalid_credentials";
                        }
                    }
                } else {
                    // No account found with the provided username/email
                    $error = "no_account";
                }
            } else {
                // SQL execution error
                $error = "sql_error";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            // SQL preparation error
            $error = "sql_error";
        }
    }

    // Close connection
    mysqli_close($conn);

    // Redirect back to login page with error if any
    if ($error !== "") {
        header("location: login.html?error=" . $error);
        exit;
    }
}
?>