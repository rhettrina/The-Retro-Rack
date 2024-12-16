<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page
    header('Location: loginview.php');
    exit();
}

// Fetch the user ID from the session
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
} else {
    // Handle the case where user_id is not set
    $_SESSION['message'] = "User ID not found. Please log in again.";
    header("Location: loginview.php");
    exit();
}

if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id'];

    // Get the quantity from GET, default to 1 if not set
    if (isset($_GET['quantity']) && is_numeric($_GET['quantity']) && $_GET['quantity'] > 0) {
        $quantity = (int)$_GET['quantity'];
    } else {
        $quantity = 1;
    }

    // Check if the product exists and has enough stock
    $stmt = $conn->prepare("SELECT `stock` FROM `products` WHERE `id` = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($stock);
    $stmt->fetch();
    $stmt->close();

    if ($stock >= $quantity) {
        // Check if the item is already in the user's cart
        $stmt = $conn->prepare("SELECT `quantity` FROM `cart_items` WHERE `user_id` = ? AND `product_id` = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Item exists in cart, update quantity
            $stmt->bind_result($existing_quantity);
            $stmt->fetch();
            $new_quantity = $existing_quantity + $quantity;

            $update_stmt = $conn->prepare("UPDATE `cart_items` SET `quantity` = ? WHERE `user_id` = ? AND `product_id` = ?");
            $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            // Item not in cart, insert new record
            $insert_stmt = $conn->prepare("INSERT INTO `cart_items` (`user_id`, `product_id`, `quantity`, `added_at`) VALUES (?, ?, ?, NOW())");
            $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
        $stmt->close();

        $_SESSION['success_message'] = 'Product added to cart successfully!';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        // Not enough stock
        $_SESSION['error_message'] = 'Sorry, not enough stock available.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
} else {
    // Invalid product ID
    echo "Invalid request.";
}
?>