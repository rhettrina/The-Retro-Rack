<?php
session_start();

// Include the database connection
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Set a message to display on the login page
    $_SESSION['message'] = "You need to log in to access this page.";

    // Redirect to loginview.php
    header("Location: loginview.php");
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

// Fetch cart items for the user
$stmt = $conn->prepare("
    SELECT ci.product_id, ci.quantity, p.name, p.price, p.image_path
    FROM cart_items ci
    INNER JOIN products p ON ci.product_id = p.id
    WHERE ci.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize an array to store cart items
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}
$stmt->close();

// Initialize the total variable
$total = 0;
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
  <title>Your Cart</title>
</head>

<body>
  <?php include 'visitorheader.php'; ?>

  <div class="container cart">
    <?php if (!empty($cart_items)): ?>
      <div class="cart-header">
        <h1>Your Shopping Cart</h1>
      </div>
      <div class="cart-table">
        <table>
          <tr>
            <th><input type="checkbox" id="select-all" /></th>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Action</th>
          </tr>
          <?php
          foreach ($cart_items as $item):
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
            ?>
            <tr>
              <td><input type="checkbox" class="select-item" /></td>
              <td>
                <div class="cart-info">
                  <img src="<?php echo $item['image_path']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" />
                  <div>
                    <p><?php echo htmlspecialchars($item['name']); ?></p>
                  </div>
                </div>
              </td>
              <td>$<?php echo number_format($item['price'], 2); ?></td>
              <td>
                <form method="post" action="update_cart.php" class="quantity-form">
                  <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>" />
                  <div class="quantity-controls">
                    <button type="button" class="quantity-minus">-</button>
                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" readonly />
                    <button type="button" class="quantity-plus">+</button>
                  </div>
                </form>
              </td>
              <td>$<?php echo number_format($subtotal, 2); ?></td>
              <td><a href="remove_from_cart.php?id=<?php echo $item['product_id']; ?>" class="remove-btn">Remove</a></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php else: ?>
      <p>Your cart is empty.</p>
    <?php endif; ?>

    <!-- Cart Summary -->
    <div class="cart-summary">
      <div class="cart-total">
        <p>Total (<span id="total-items"><?php echo count($cart_items); ?></span> items): <strong>$<span
              id="total-price"><?php echo number_format($total, 2); ?></span></strong></p>
      </div>
      <?php if (!empty($cart_items)): ?>
        <div class="cart-actions">
          <a href="checkout.php" class="checkout btn">Proceed To Checkout</a>
        </div>
      <?php endif; ?>
    </div>
  </div>



  <?php include 'visitorfooter.php'; ?>

  <!-- Custom Script -->
  <script src="./js/index.js"></script>
  <!-- Custom Script -->
  <script src="./js/index.js"></script>
  <!-- Include this script at the end of your `cart.php` -->
  <script>
    document.querySelectorAll('.quantity-minus').forEach(function (button) {
      button.addEventListener('click', function () {
        var productId = this.getAttribute('data-product-id');
        var input = document.querySelector('input[name="quantities[' + productId + ']"]');
        var quantity = parseInt(input.value);
        if (quantity > 1) {
          quantity--;
          input.value = quantity;
        }
      });
    });

    document.querySelectorAll('.quantity-plus').forEach(function (button) {
      button.addEventListener('click', function () {
        var productId = this.getAttribute('data-product-id');
        var input = document.querySelector('input[name="quantities[' + productId + ']"]');
        var quantity = parseInt(input.value);
        quantity++;
        input.value = quantity;
      });
    });
  </script>
</body>

</html>