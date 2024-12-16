<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: loginview.php');
    exit();
}

// Fetch the user ID from the session
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
} else {
    $_SESSION['message'] = "User ID not found. Please log in again.";
    header("Location: loginview.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    // Remove the item from the cart in the database
    $stmt = $conn->prepare("DELETE FROM `cart_items` WHERE `user_id` = ? AND `product_id` = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
}

header('Location: cart.php');
exit();
?>