<?php
session_start();

require_once "config.php";

// Function to handle errors
function handleError($message, $loginInput = '')
{
    // Store the error message in a session variable
    $_SESSION['error_message'] = $message;
    $_SESSION['login_input'] = $loginInput; // Preserve user input
    header("location: loginview.php");
    exit;
}

// Initialize variables
$login = $password = "";

// If request method is POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if login field is empty
    if (empty(trim($_POST['login']))) {
        handleError("Please enter your username or email.");
    } else {
        $login = trim($_POST['login']);
    }

    // Check if password field is empty
    if (empty(trim($_POST['password']))) {
        handleError("Please enter your password.", $login);
    } else {
        $password = trim($_POST['password']);
    }

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
                        handleError("Invalid username/email or password.", $login);
                    }
                } else {
                    // Fetch failed
                    handleError("An unexpected error occurred. Please try again later.", $login);
                }
            } else {
                // No account found with the provided username/email
                handleError("Invalid username/email or password.", $login);
            }
        } else {
            // SQL execution error
            handleError("An error occurred. Please try again later.", $login);
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        // SQL preparation error
        handleError("An error occurred. Please try again later.", $login);
    }

    // Close connection
    mysqli_close($conn);
} else {
    // If request method is not POST, redirect to login page
    header("location: loginview.php");
    exit;
}
?>