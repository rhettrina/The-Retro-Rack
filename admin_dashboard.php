<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Not logged in, redirect to main page or login page
    header("Location: index.php");
    exit();
}

// ... rest of your admin dashboard code ...
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
                <!-- Card 1 -->
                <div class="product-item admin-card">
                    <div class="icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div class="admin-card-info">
                        <h3>Products</h3>
                        <span>150</span>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="product-item admin-card">
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="admin-card-info">
                        <h3>Orders</h3>
                        <span>120</span>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="product-item admin-card">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="admin-card-info">
                        <h3>Users</h3>
                        <span>300</span>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="product-item admin-card">
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="admin-card-info">
                        <h3>Revenue</h3>
                        <span>$12,000</span>
                    </div>
                </div>
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
                        <!-- Sample Data -->
                        <tr>
                            <td>#12345</td>
                            <td>John Doe</td>
                            <td><span class="status delivered">Delivered</span></td>
                            <td>$99.99</td>
                            <td>2024-12-10</td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
                <a href="#" class="btn">View All Orders</a>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script>
        // Add JavaScript here if needed
    </script>
</body>

</html>