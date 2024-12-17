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
  <link rel="stylesheet" href="./css/styles.css" />
  <!-- Inline CSS for Specific Styles -->
  <style>
    /* Center the container */
    .product-detail .container {
      max-width: 1200px;
      margin: 0 auto;
    }

    /* Container for the product details */
    .product-detail .details {
      display: flex;
      flex-wrap: wrap;
      margin-top: 2rem;
      justify-content: center;
    }

    /* Left column styles (Product Image) */
    .product-detail .details .left {
      flex: 1;
      max-width: 45%;
      margin-right: 2rem;
      position: relative;
      /* Add relative positioning */
      z-index: 1;
      /* Ensure image is above other elements */
    }

    /* Right column styles (Product Info and Buttons) */
    .product-detail .details .right {
      flex: 1;
      max-width: 45%;
      position: relative;
      /* Add relative positioning */
      z-index: 0;
      /* Set lower z-index */
    }

    /* For small screens, stack the columns */
    @media (max-width: 768px) {
      .product-detail .details {
        flex-direction: column;
        align-items: center;
      }

      .product-detail .details .left,
      .product-detail .details .right {
        max-width: 100%;
        margin-right: 0;
      }
    }

    /* Align buttons next to each other */
    .product-detail .button-group {
      display: flex;
      align-items: center;
      margin-bottom: 2rem;
      position: relative;
      z-index: 0;
      /* Ensure buttons stay below the zoomed image */
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

    /* Image styling */
    .image-container {
      text-align: center;
    }

    .image-container .main {
      position: relative;
      z-index: 2;
      /* Ensure the zoomed image stays above other elements */
      display: inline-block;
    }

    .image-container .main img {
      width: 100%;
      height: auto;
      max-width: 400px;
      display: block;
      cursor: zoom-in;
    }

    /* Adjust the zoomed image container */
    .zoomImg {
      z-index: 9999 !important;
      /* Bring the zoomed image above all elements */
    }
  </style>
</head>

<body>
  <!-- Navigation -->
  <?php include 'visitorheader.php'; ?>

  <!-- Product Details -->
  <section class="section product-detail">
    <div class="container">
      <div class="details">
        <!-- Left Column (Product Image) -->
        <div class="left image-container">
          <div class="main">
            <img src="<?php echo $product['image_path']; ?>" id="zoom"
              alt="<?php echo htmlspecialchars($product['name']); ?>" />
          </div>
        </div>

        <!-- Right Column (Product Info and Buttons) -->
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
    </div>
  </section>

  <!-- Footer -->
  <?php include 'visitorfooter.php'; ?>

  <!-- Scripts -->
  <!-- Include jQuery -->
  <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
  <!-- Include the image zoom plugin -->
  <script src="./js/zoomsl.min.js"></script>
  <!-- Initialize the image zoom plugin -->
  <script>
    $(function () {
      $("#zoom").imagezoomsl({
        zoomrange: [3, 3],
        magnifierborder: "none",
        innerzoom: true,
      });
    });
  </script>
  <!-- Quantity Synchronization Script -->
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