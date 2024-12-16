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
  // Close the statement
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Box icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
  <!-- Custom StyleSheet -->
  <link rel="stylesheet" href="./css/styles.css" />
  <title>Boy’s T-Shirt - Codevo</title>
</head>

<body>
  <!-- Navigation -->
  <?php include 'visitorheader.php'; ?>

  <!-- Product Details -->
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

        <!-- If you have sizes or other options, you can display them here -->
        <!-- For example, if you have a 'size' field in your database -->
        <?php if (!empty($product['size'])): ?>
          <form>
            <div>
              <select>
                <option value="" selected disabled>Select Size</option>
                <?php
                // Assuming 'size' is stored as a comma-separated string
                $sizes = explode(',', $product['size']);
                foreach ($sizes as $size) {
                  echo '<option value="' . htmlspecialchars(trim($size)) . '">' . htmlspecialchars(trim($size)) . '</option>';
                }
                ?>
              </select>
              <span><i class="bx bx-chevron-down"></i></span>
            </div>
          </form>
        <?php endif; ?>

        <form class="form" method="get" action="add_to_cart.php">
          <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
          <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" />
          <button type="submit" class="addCart">Add To Cart</button>
        </form>
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
  <script src="https://code.jquery.com/jquery-3.4.0.min.js"
    integrity="sha384-JUMjoW8OzDJw4oFpWIB2Bu/c6768ObEthBMVSiIx4ruBIEdyNSUQAjJNFqT5pnJ6"
    crossorigin="anonymous"></script>
  <script src="./js/zoomsl.min.js"></script>
  <script>
    $(function () {
      console.log("hello");
      $("#zoom").imagezoomsl({
        zoomrange: [4, 4],
      });
    });
  </script>
</body>

</html>