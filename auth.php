<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // User is not logged in
    // Store the page they were trying to access
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];

    // Set a message to display on the login page
    $_SESSION['message'] = "You need to log in to access this page.";

    // Redirect to login page
    header("Location: login.php");
    exit();
}
?>