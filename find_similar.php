<?php
include 'config.php';

// Validate and get the product ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = (int) $_GET['id'];

    // Fetch the current product details
    $stmt = $conn->prepare("SELECT * FROM `products` WHERE `id` = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $current_product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($current_product) {
        // Get the category or any other criteria
        $category = $current_product['category'];

        // Fetch similar products (excluding the current product)
        $stmt_similar = $conn->prepare("SELECT * FROM `products` WHERE `category` = ? AND `id` != ? LIMIT 8");
        $stmt_similar->bind_param("si", $category, $product_id);
        $stmt_similar->execute();
        $similar_products = $stmt_similar->get_result();
        $stmt_similar->close();
    } else {
        // Product not found
        echo "Product not found.";
        exit();
    }
} else {
    // Invalid product ID
    echo "Invalid product ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include your head content -->
    <link rel="stylesheet" href="./css/styles.css" />
    <title>Find Similar Products</title>
</head>
<body>
    <!-- Include your navigation -->

    <!-- Similar Products Section -->
    <section class="section all-products" id="products">
        <div class="top container">
            <h1>Products Similar to "<?php echo htmlspecialchars($current_product['name']); ?>"</h1>
        </div>
        <div class="product-center container">
            <?php
            if ($similar_products->num_rows > 0) {
                while ($product = $similar_products->fetch_assoc()) {
                    // Display each similar product
            ?>
            <div class="product-item">
                <div class="overlay">
                    <a href="productDetails.php?id=<?php echo $product['id']; ?>" class="product-thumb">
                        <img src="<?php echo $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                    </a>
                </div>
                <div class="product-info">
                    <span><?php echo htmlspecialchars($product['category']); ?></span>
                    <a href="productDetails.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                    <h4>$<?php echo number_format($product['price'], 2); ?></h4>
                </div>
                <ul class="icons">
                    <!-- Your other icons (e.g., add to cart) -->
                </ul>
            </div>
            <?php
                }
            } else {
                echo "<p>No similar products found.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Include your footer -->

    <!-- Custom Script -->
    <script src="./js/index.js"></script>
</body>
</html>