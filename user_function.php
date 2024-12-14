<?php
// functions.php

// Include the database configuration file
include('config.php');

// Function to fetch all users from the database
function getAllUsers() {
    global $conn;
    
    $sql = "SELECT `id`, `fullname`, `username`, `phone`, `gender`, `dob`, `created_at`, `email` FROM `users`";
    $result = mysqli_query($conn, $sql);
    
    $users = array();
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    
    return $users;
}