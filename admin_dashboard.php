<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Not logged in, redirect to main page or login page
    header("Location: index.php");
    exit();
}

// Include the database configuration file
require_once 'config.php';

// Fetch data from the database

// Fetch total number of products
$product_query = "SELECT COUNT(*) AS total_products FROM products";
$product_result = mysqli_query($conn, $product_query);
$product_data = mysqli_fetch_assoc($product_result);
$total_products = $product_data['total_products'];

// Fetch total number of orders
$order_query = "SELECT COUNT(*) AS total_orders FROM orders";
$order_result = mysqli_query($conn, $order_query);
$order_data = mysqli_fetch_assoc($order_result);
$total_orders = $order_data['total_orders'];

// Fetch total number of users
$user_query = "SELECT COUNT(*) AS total_users FROM users";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);
$total_users = $user_data['total_users'];

// Fetch total revenue
$revenue_query = "SELECT SUM(total_amount) AS total_revenue FROM orders WHERE status = 'Delivered'";
$revenue_result = mysqli_query($conn, $revenue_query);
$revenue_data = mysqli_fetch_assoc($revenue_result);
$total_revenue = $revenue_data['total_revenue'];
$total_revenue = $total_revenue ? $total_revenue : 0; // Handle NULL values

// Fetch recent orders
$recent_orders_query = "
    SELECT o.id AS order_id, u.fullname AS user_name, o.status, o.total_amount, o.order_date
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
    LIMIT 5
";
$recent_orders_result = mysqli_query($conn, $recent_orders_query);

/* 
// Removed the COGS query and profit calculation since we're not using cost_price

$cogs_query = "
    SELECT SUM(oi.quantity * p.cost_price) AS total_cogs
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.id
    INNER JOIN products p ON oi.product_id = p.id
    WHERE o.status = 'Delivered'
";
$cogs_result = mysqli_query($conn, $cogs_query);
$cogs_data = mysqli_fetch_assoc($cogs_result);
$total_cogs = $cogs_data['total_cogs'] ?: 0;

// Calculate Profit
$profit = $total_revenue - $total_cogs;
*/

// Fetch top-selling products
$top_products_query = "
    SELECT p.name, SUM(oi.quantity) AS total_quantity
    FROM order_items oi
    INNER JOIN products p ON oi.product_id = p.id
    INNER JOIN orders o ON oi.order_id = o.id
    WHERE o.status = 'Delivered'
    GROUP BY oi.product_id
    ORDER BY total_quantity DESC
    LIMIT 5
";
$top_products_result = mysqli_query($conn, $top_products_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Clothing Store</title>
    <!-- Link to existing styles.css -->
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/admin_dashboard.css">
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body>

    <?php include 'adminheader.php'; ?>

    <!-- Main Content -->
    <section class="section main-admin">
        <div class="container">
            <div class="title">
                <h1>Dashboard</h1>
            </div>

            <!-- Cards -->
            <div class="product-center admin-cards">
                <!-- Card 1: Products -->
                <div class="product-item admin-card">
                    <div class="icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div class="admin-card-info">
                        <h3>Products</h3>
                        <span><?php echo $total_products; ?></span>
                    </div>
                </div>
                <!-- Card 2: Orders -->
                <div class="product-item admin-card">
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="admin-card-info">
                        <h3>Orders</h3>
                        <span><?php echo $total_orders; ?></span>
                    </div>
                </div>
                <!-- Card 3: Users -->
                <div class="product-item admin-card">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="admin-card-info">
                        <h3>Users</h3>
                        <span><?php echo $total_users; ?></span>
                    </div>
                </div>
                <!-- Card 4: Revenue -->
                <div class="product-item admin-card">
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="admin-card-info">
                        <h3>Revenue</h3>
                        <span>$<?php echo number_format($total_revenue, 2); ?></span>
                    </div>
                </div>
                <!-- Note: Removed the Profit card since we're not calculating profit anymore -->
            </div>

            <!-- Recent Orders Table -->
            <div class="recent-orders">
                <h2>Recent Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = mysqli_fetch_assoc($recent_orders_result)) { ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td>
                                    <span class="status <?php echo strtolower($order['status']); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <a href="orders.php" class="btn">View All Orders</a>
            </div>
        </div>

        <div class="top-products">
            <h2>Top-Selling Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Units Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = mysqli_fetch_assoc($top_products_result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo $product['total_quantity']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Scripts -->
    <script>
        // Add JavaScript here if needed
    </script>
</body>

</html>