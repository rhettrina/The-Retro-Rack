<?php
session_start();
include 'config.php';
include 'functions.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: loginview.php');
  exit();
}

$user_id = $_SESSION['id'];
$user = getUserInfo($conn, $user_id);
$address = getDefaultAddress($conn, $user_id);

// Fetch cart items or product if coming directly from "Buy Now"
$cart_items = [];
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
  // Coming from "Buy Now"
  $product_id = (int) $_POST['product_id'];
  $quantity = (int) $_POST['quantity'];

  // Fetch product details
  $stmt = $conn->prepare("SELECT * FROM `products` WHERE `id` = ?");
  $stmt->bind_param("i", $product_id);
  $stmt->execute();
  $product_result = $stmt->get_result();
  if ($product = $product_result->fetch_assoc()) {
    $cart_items[] = [
      'product_id' => $product['id'],
      'name' => $product['name'],
      'price' => $product['price'],
      'quantity' => $quantity,
      'image_path' => $product['image_path'],
    ];
  }
  $stmt->close();
} else {
  // Coming from cart
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
  while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
  }
  $stmt->close();
}

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
  $subtotal = $item['price'] * $item['quantity'];
  $total += $subtotal;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_order') {
  // Get payment method
  $payment_method = $_POST['payment_method'];

  // Calculate total amount
  $total_amount = $total;

  // Insert order into `orders` table
  $stmt = $conn->prepare("INSERT INTO `orders` (`user_id`, `total_amount`, `payment_method`) VALUES (?, ?, ?)");
  $stmt->bind_param("ids", $user_id, $total_amount, $payment_method);
  $stmt->execute();
  $order_id = $stmt->insert_id;
  $stmt->close();

  // Insert order items into `order_items` table
  foreach ($cart_items as $item) {
    $stmt = $conn->prepare("INSERT INTO `order_items` (`order_id`, `product_id`, `quantity`, `price`) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
    $stmt->execute();
    $stmt->close();
  }

  // Remove purchased items from cart (if they exist)
  if (!isset($_POST['product_id'])) {
    // Coming from cart, remove items from cart_items table
    $stmt = $conn->prepare("DELETE FROM `cart_items` WHERE `user_id` = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
  }

  // Set order confirmation flag
  $orderConfirmed = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <link rel="stylesheet" href="./css/styles.css">
  <!-- Inline CSS for specific styles -->
  <style>
    /* Checkout page styles inspired by Lazada */
    .checkout-container {
      display: flex;
      max-width: 1200px;
      margin: 20px auto;
    }

    .checkout-left,
    .checkout-right {
      padding: 20px;
    }

    .checkout-left {
      flex: 2;
      background-color: #fff;
    }

    .checkout-right {
      flex: 1;
      background-color: #f5f5f5;
    }

    /* Address Section */
    .address {
      border: 1px solid #e7e7e7;
      padding: 15px;
      margin-bottom: 20px;
    }

    /* Payment Methods */
    .payment-methods {
      border: 1px solid #e7e7e7;
      padding: 15px;
      margin-bottom: 20px;
    }

    .payment-methods label {
      display: block;
      margin-bottom: 10px;
    }

    /* Order Summary */
    .order-summary {
      background-color: #fff;
      padding: 15px;
      border: 1px solid #e7e7e7;
    }

    .order-item {
      display: flex;
      margin-bottom: 15px;
    }

    .order-item img {
      width: 60px;
      margin-right: 15px;
    }

    .confirm-btn {
      background-color: #f57224;
      color: #fff;
      border: none;
      padding: 12px 20px;
      font-size: 16px;
      cursor: pointer;
      width: 100%;
      border-radius: 4px;
    }

    .confirm-btn:hover {
      background-color: #f45c14;
    }
  </style>
</head>

<body>
  <?php include 'visitorheader.php'; ?>

  <div class="checkout-container">
    <!-- Left Side -->
    <div class="checkout-left">
      <!-- Address Section -->
      <div class="address">
        <h3>Shipping Address</h3>
        <?php if ($address): ?>
          <p><?php echo htmlspecialchars($user['fullname']); ?></p>
          <p>
            <?php echo htmlspecialchars(
              $address['house_number'] . ', ' . $address['street'] . ', ' . $address['barangay']
            ); ?>
          </p>
          <p>
            <?php echo htmlspecialchars(
              $address['city'] . ', ' . $address['province'] . ', ' . $address['postal_code']
            ); ?>
          </p>
          <p><?php echo htmlspecialchars($address['country']); ?></p>
          <p>Phone: <?php echo htmlspecialchars($user['phone']); ?></p>
        <?php else: ?>
          <p>No default address found. Please add an address in your profile.</p>
        <?php endif; ?>
      </div>

      <!-- Payment Methods -->
      <div class="payment-methods">
        <h3>Payment Method</h3>
        <form method="post" action="checkout.php">
          <input type="hidden" name="action" value="place_order">
          <?php
          // Include hidden inputs for product_id and quantity if coming from "Buy Now"
          if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
            echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($_POST['product_id']) . '">';
            echo '<input type="hidden" name="quantity" value="' . htmlspecialchars($_POST['quantity']) . '">';
          }
          ?>
          <label>
            <input type="radio" name="payment_method" value="card" checked> Credit/Debit Card
          </label>
          <label>
            <input type="radio" name="payment_method" value="online_banking"> Online Banking
          </label>
          <label>
            <input type="radio" name="payment_method" value="cod"> Cash on Delivery
          </label>

          <!-- Confirm Button -->
          <button type="submit" class="confirm-btn">Confirm Order</button>
        </form>
      </div>
    </div>

    <!-- Right Side -->
    <div class="checkout-right">
      <div class="order-summary">
        <h3>Order Summary</h3>
        <?php foreach ($cart_items as $item): ?>
          <div class="order-item">
            <img src="<?php echo $item['image_path']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
            <div>
              <p><?php echo htmlspecialchars($item['name']); ?></p>
              <p>Quantity: <?php echo $item['quantity']; ?></p>
              <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
            </div>
          </div>
        <?php endforeach; ?>
        <hr>
        <p><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>
      </div>
    </div>
  </div>

  <!-- Order Confirmation Popup -->
  <?php if (isset($orderConfirmed) && $orderConfirmed): ?>
    <div id="order-confirmation-modal" class="modal">
      <div class="modal-content">
        <span class="close-button" onclick="closeModal()">&times;</span>
        <h2>Order Confirmation</h2>
        <p>Your order has been placed successfully!</p>
        <button onclick="closeModal()">Close</button>
      </div>
    </div>
    <script>
      function closeModal() {
        document.getElementById('order-confirmation-modal').style.display = 'none';
        // Redirect to order history or another page
        window.location.href = 'order_history.php';
      }

      // Show the modal
      document.getElementById('order-confirmation-modal').style.display = 'block';
    </script>
    <style>
      /* Modal styles */
      .modal {
        display: block;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100vh;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
      }

      .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        width: 30%;
        border-radius: 8px;
        text-align: center;
      }

      .close-button {
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
      }
    </style>
  <?php endif; ?>

</body>

</html>