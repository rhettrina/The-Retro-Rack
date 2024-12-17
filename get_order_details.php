<?php
session_start();
include 'config.php';

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    echo "Invalid order ID.";
    exit();
}

$order_id = (int) $_GET['order_id'];
$user_id = $_SESSION['id'];

// Verify that the order belongs to the logged-in user
$stmt = $conn->prepare("
    SELECT o.id
    FROM orders o
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    echo "Order not found.";
    exit();
}
$stmt->close();

// Fetch order items
$stmt = $conn->prepare("
    SELECT oi.product_id, oi.quantity, oi.price, p.name, p.image_path
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

echo '<table class="modal-table">';
echo '<tr><th>Product</th><th>Quantity</th><th>Price</th></tr>';
while ($item = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($item['name']) . '</td>';
    echo '<td>' . $item['quantity'] . '</td>';
    echo '<td>$' . number_format($item['price'] * $item['quantity'], 2) . '</td>';
    echo '</tr>';
}
echo '</table>';

$stmt->close();
?>