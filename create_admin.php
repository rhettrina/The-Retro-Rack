<?php
// create_admin.php

// Include the database connection
require_once 'config.php';
session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate inputs
    $errors = [];

    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    if (empty($role)) {
        $errors[] = "Role is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username already exists
        $check_query = "SELECT id FROM admins WHERE username = ?";
        if ($stmt = mysqli_prepare($conn, $check_query)) {
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                $errors[] = "Username already exists. Please choose a different username.";
            }
            mysqli_stmt_close($stmt);
        }

        if (empty($errors)) {
            // Prepare an insert statement
            $sql = "INSERT INTO admins (username, role, password) VALUES (?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                // Bind variables to the prepared statement
                mysqli_stmt_bind_param($stmt, "sss", $username, $role, $hashed_password);

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Success
                    $_SESSION['success'] = "Admin created successfully.";
                    header("Location: admin_settings.php");
                    exit();
                } else {
                    // Error executing statement
                    $errors[] = "Error creating admin.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            } else {
                // Error preparing statement
                $errors[] = "Error preparing database statement.";
            }
        }
    }

    // Close the database connection
    mysqli_close($conn);

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: admin_settings.php");
        exit();
    }
} else {
    // If the form was not submitted via POST
    header("Location: admin_settings.php");
    exit();
}
?>