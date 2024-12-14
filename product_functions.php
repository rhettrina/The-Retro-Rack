<?php
// product_functions.php

include './config.php';

// Function to get all products
function getAllProducts() {
    global $conn;
    $sql = "SELECT id, name, price, stock, category, description, image_path, created_at FROM products";
    return mysqli_query($conn, $sql);
}




