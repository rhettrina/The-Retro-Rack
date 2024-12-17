<?php
// create_coupon.php

session_start();
// Include your database connection
require_once 'config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $code = mysqli_real_escape_string($conn, trim($_POST['code']));
    $discount = intval($_POST['discount']);
    $expiry = mysqli_real_escape_string($conn, $_POST['expiry']);

    // Initialize an array to store errors
    $errors = [];

    // Validate inputs
    if (empty($code)) {
        $errors[] = "Coupon code is required.";
    }

    if ($discount <= 0 || $discount > 100) {
        $errors[] = "Discount percentage must be between 1 and 100.";
    }

    if (empty($expiry)) {
        $errors[] = "Expiry date is required.";
    } else {
        // Check if the date is valid and in the future
        $expiry_timestamp = strtotime($expiry);
        if ($expiry_timestamp === false) {
            $errors[] = "Invalid expiry date format.";
        } elseif ($expiry_timestamp < time()) {
            $errors[] = "Expiry date must be in the future.";
        } else {
            // Format expiry date for the database
            $expiry_date = date('Y-m-d', $expiry_timestamp);
        }
    }

    // If there are no validation errors, proceed to insert into the database
    if (empty($errors)) {
        // Check for duplicate coupon codes
        $check_query = "SELECT id FROM coupons WHERE code = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, 's', $code);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            // Coupon code already exists
            $errors[] = "Coupon code already exists. Please choose a different code.";
        } else {
            // Insert the new coupon into the database
            $insert_query = "INSERT INTO coupons (code, discount_percentage, expiry_date) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, 'sis', $code, $discount, $expiry_date);

            if (mysqli_stmt_execute($stmt)) {
                // Success
                $_SESSION['success'] = "Coupon created successfully.";
                header("Location: admin_settings.php");
                exit();
            } else {
                $errors[] = "Database insertion failed: " . mysqli_error($conn);
            }
        }
        mysqli_stmt_close($stmt);
    }

    // Close the database connection
    mysqli_close($conn);

    // If there are errors, redirect back with error messages
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: admin_settings.php");
        exit();
    }
} else {
    // If the form was not submitted via POST, redirect to the settings page
    header("Location: admin_settings.php");
    exit();
}
?>