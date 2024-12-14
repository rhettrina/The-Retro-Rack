<?php
// create_admin.php

// Include the database connection
require_once 'config.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare an insert statement
    $sql = "INSERT INTO admins (username, role, password) VALUES (?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement
        mysqli_stmt_bind_param($stmt, "sss", $username, $role, $hashed_password);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Success
            header("Location: admin_settings.php?message=Admin created successfully.");
            exit();
        } else {
            // Error executing statement
            header("Location: admin_settings.php?error=Error creating admin.");
            exit();
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        // Error preparing statement
        header("Location: admin_settings.php?error=Error preparing database statement.");
        exit();
    }
} else {
    // If the form was not submitted via POST
    header("Location: admin_settings.php");
    exit();
}

// Close the database connection
mysqli_close($conn);
?>