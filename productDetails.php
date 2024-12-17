<?php
session_start();
include 'config.php';

// Validate and get the product ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $product_id = (int) $_GET['id'];
} else {
  header('Location: 404.php');
  exit();
}

// Fetch the product from the database
$stmt = $conn->prepare("SELECT * FROM `products` WHERE `id` = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($product = $result->fetch_assoc()) {
  $stmt->close();
} else {
  header('Location: 404.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title><?php echo htmlspecialchars($product['name']); ?> - Product Details</title>
  <!-- Box icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
  <!-- General Stylesheet -->
  <link rel="stylesheet" href="styles.css" />
  <!-- Inline CSS for Specific Styles -->
  <style>
    /* Align buttons next to each other */
    .product-detail .button-group {
      display: flex;
      align-items: center;
      margin-bottom: 2rem;
    }

    .product-detail .button-group form {
      margin-right: 1rem;
    }

    .product-detail .button-group form:last-child {
      margin-right: 0;
    }

    .product-detail .button-group button {
      flex: 1;
      padding: 1rem 2rem;
      font-size: 1.6rem;
      border: none;
      cursor: pointer;
      border-radius: 4px;
    }

    .product-detail .button-group .add-cart {
      background-color: #ff9f1a;
      color: #fff;
    }

    .product-detail .button-group .add-cart:hover {
      background-color: #e68a00;
    }

    .product-detail .button-group .buy-now {
      background-color: #f57224;
      color: #fff;
    }

    .product-detail .button-group .buy-now:hover {
      background-color: #f45c14;
    }

    /* Quantity input styling */
    .product-detail .quantity-input {
      width: 5rem;
      padding: 0.5rem;
      margin-right: 2rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
  </style>
</head>

<body>
  <!-- Navigation -->
  <?php include 'visitorheader.php'; ?>

  <!-- Product Details -->
  <section class="section product-detail">
    <div class="details container">
      <div class="left image-container">
        <div class="main">
          <img src="<?php echo $product['image_path']; ?>" id="zoom"
            alt="<?php echo htmlspecialchars($product['name']); ?>" />
        </div>
      </div>
      <div class="right">
        <span>Home/<?php echo htmlspecialchars($product['category']); ?></span>
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="price">$<?php echo number_format($product['price'], 2); ?></div>

        <!-- Quantity Input -->
        <div class="form">
          <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>"
            class="quantity-input" />
        </div>

        <!-- Buttons -->
        <div class="button-group">
          <!-- Add to Cart Button -->
          <form method="post" action="add_to_cart.php">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
            <input type="hidden" name="quantity" id="add-to-cart-quantity" value="1" />
            <button type="submit" class="add-cart">Add to Cart</button>
          </form>
          <!-- Buy Now Button -->
          <form method="post" action="checkout.php">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
            <input type="hidden" name="quantity" id="buy-now-quantity" value="1" />
            <button type="submit" class="buy-now">Buy Now</button>
          </form>
        </div>

        <h3>Product Detail</h3>
        <p>
          <?php echo nl2br(htmlspecialchars($product['description'])); ?>
        </p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'visitorfooter.php'; ?>

  <!-- Custom Script -->
  <script src="./js/index.js"></script>
  <script>
    const quantityInput = document.getElementById('quantity');
    const addToCartQuantityInput = document.getElementById('add-to-cart-quantity');
    const buyNowQuantityInput = document.getElementById('buy-now-quantity');

    quantityInput.addEventListener('input', function () {
      addToCartQuantityInput.value = this.value;
      buyNowQuantityInput.value = this.value;
    });
  </script>
</body>

</html>