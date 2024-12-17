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
$user_id = $_SESSION['id'];

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
      <form method="post" action="update_cart.php" id="cart-form">
        <div class="cart-table">
          <table>
            <tr>
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
                  <div class="quantity-controls">
                    <button type="button" class="quantity-minus"
                      data-product-id="<?php echo $item['product_id']; ?>">-</button>
                    <input type="number" name="quantities[<?php echo $item['product_id']; ?>]"
                      value="<?php echo $item['quantity']; ?>" min="1" readonly />
                    <button type="button" class="quantity-plus"
                      data-product-id="<?php echo $item['product_id']; ?>">+</button>
                  </div>
                </td>
                <td class="subtotal" data-price="<?php echo $item['price']; ?>">
                  $<?php echo number_format($subtotal, 2); ?>
                </td>
                <td><a href="remove_from_cart.php?id=<?php echo $item['product_id']; ?>" class="remove-btn">Remove</a></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </div>
      </form>
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
          <button type="button" onclick="updateCart()" class="update-cart btn">Update Cart</button>
          <a href="checkout.php" class="checkout btn">Proceed To Checkout</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php include 'visitorfooter.php'; ?>

  <!-- Custom Script -->
  <script src="./js/index.js"></script>
  <script>
    function updateCart() {
      document.getElementById('cart-form').submit();
    }

    document.querySelectorAll('.quantity-minus').forEach(function (button) {
      button.addEventListener('click', function () {
        var productId = this.getAttribute('data-product-id');
        var input = document.querySelector('input[name="quantities[' + productId + ']"]');
        var quantity = parseInt(input.value);
        if (quantity > 1) {
          quantity--;
          input.value = quantity;
          updateSubtotal(productId, quantity);
          updateTotal();
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
        updateSubtotal(productId, quantity);
        updateTotal();
      });
    });

    function updateSubtotal(productId, quantity) {
      var priceElement = document.querySelector('.subtotal[data-product-id="' + productId + '"]');
      var pricePerUnit = parseFloat(priceElement.getAttribute('data-price'));
      var newSubtotal = pricePerUnit * quantity;
      priceElement.textContent = '$' + newSubtotal.toFixed(2);
    }

    function updateTotal() {
      var total = 0;
      document.querySelectorAll('.subtotal').forEach(function (element) {
        var subtotal = parseFloat(element.textContent.replace('$', ''));
        total += subtotal;
      });
      document.getElementById('total-price').textContent = total.toFixed(2);
    }
  </script>
</body>

</html>