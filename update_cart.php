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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['product_id'], $_POST['quantity'])) {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        if ($quantity > 0) {
            // Update the quantity
            $stmt = $conn->prepare("UPDATE `cart_items` SET `quantity` = ? WHERE `user_id` = ? AND `product_id` = ?");
            $stmt->bind_param("iii", $quantity, $user_id, $product_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Remove the item if quantity is zero
            $stmt = $conn->prepare("DELETE FROM `cart_items` WHERE `user_id` = ? AND `product_id` = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

header('Location: cart.php');
exit();
?>