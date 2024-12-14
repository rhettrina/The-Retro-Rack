<?php
session_start();

// Include config file to get the database connection
require_once 'config.php';

// Initialize response array
$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form inputs
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    // Prepare a SELECT statement using prepared statements
    $sql = "SELECT `id`, `username`, `password`, `role`, `active` FROM `admins` WHERE `username` = ? LIMIT 1";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement
        mysqli_stmt_bind_param($stmt, "s", $param_username);

        // Set the value of the parameter
        $param_username = $username;

        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store the result
            mysqli_stmt_store_result($stmt);

            // Check if the username exists
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $id, $db_username, $hashed_password, $role, $active);

                if (mysqli_stmt_fetch($stmt)) {
                    // Verify the password
                    if (password_verify($password, $hashed_password)) {
                        // Check if the account is active
                        if ($active == 1) {
                            // Regenerate session ID to prevent session fixation attacks
                            session_regenerate_id(true);

                            // Store data in session variables
                            $_SESSION['admin_id'] = $id;
                            $_SESSION['admin_username'] = $db_username;
                            $_SESSION['admin_role'] = $role;

                            // Set success response
                            $response['success'] = true;
                        } else {
                            $response['error'] = "Your account is inactive. Please contact the system administrator.";
                        }
                    } else {
                        $response['error'] = "Invalid username or password.";
                    }
                } else {
                    $response['error'] = "An error occurred. Please try again.";
                }
            } else {
                $response['error'] = "Invalid username or password.";
            }
        } else {
            $response['error'] = "Oops! Something went wrong. Please try again later.";
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        $response['error'] = "Oops! Something went wrong. Please try again later.";
    }
} else {
    $response['error'] = "Invalid request method.";
}

// Close the database connection
mysqli_close($conn);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>