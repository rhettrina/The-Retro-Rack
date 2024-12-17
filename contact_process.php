<?php
// contact_process.php

// Include the database configuration file
require_once 'config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $issue_type = mysqli_real_escape_string($conn, $_POST['issue_type']);
    $customer_check = mysqli_real_escape_string($conn, $_POST['customercheck']);
    $message = mysqli_real_escape_string($conn, $_POST['msg']);

    // Insert data into the database
    $sql = "INSERT INTO contact_messages (fullname, email, issue_type, customer_check, message)
            VALUES ('$fullname', '$email', '$issue_type', '$customer_check', '$message')";

    if (mysqli_query($conn, $sql)) {
        // Redirect or display a success message
        echo "<script>alert('Your message has been sent successfully! We will get back to you soon.'); window.location.href='contact.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    // Redirect back to the contact form if the form was not submitted properly
    header("Location: contact.php");
    exit();
}
?>