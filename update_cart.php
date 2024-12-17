<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['message'] = "You need to log in to access this page.";
    header("Location: loginview.php");
    exit();
}

$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        $product_id = (int) $product_id;
        $quantity = (int) $quantity;

        // Update the quantity in the cart_items table
        $stmt = $conn->prepare("UPDATE `cart_items` SET `quantity` = ? WHERE `user_id` = ? AND `product_id` = ?");
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Redirect back to the cart
header("Location: cart.php");
exit();
?>