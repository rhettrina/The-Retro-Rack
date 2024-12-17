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

    <!-- Header -->
    <header class="top-nav" id="admin-header">
        <div class="container">
            <div class="welcome">
                <span id="currentDateTime"></span>
            </div>

            <script>
                function updateDateTime() {
                    const now = new Date();
                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    };
                    document.getElementById('currentDateTime').textContent = now.toLocaleString('en-US', options);
                }

                updateDateTime();
                setInterval(updateDateTime, 1000); // Update every second
            </script>

            <ul class="nav-links">
                <li><a href="#"><i class="fas fa-bell"></i></a></li>
                <li><a href="#" id="logoutLink"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>


    </header>


    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to log out?</p>
            <div class="modal-buttons">
                <button id="cancelLogout" class="btn cancel-btn">Cancel</button>
                <a href="admin_logout.php" id="confirmLogout" class="btn logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <script>
        // Get the modal element
        var logoutModal = document.getElementById('logoutModal');

        // Get the link that opens the modal
        var logoutLink = document.getElementById('logoutLink');

        // Get the <span> element that closes the modal
        var closeBtn = document.querySelector('#logoutModal .close-button');

        // Get the cancel button
        var cancelBtn = document.getElementById('cancelLogout');

        // When the user clicks the "Logout" link, open the modal
        logoutLink.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default link behavior
            logoutModal.style.display = 'block';
        });

        // When the user clicks on <span> (x), close the modal
        closeBtn.addEventListener('click', function () {
            logoutModal.style.display = 'none';
        });

        // When the user clicks on the "Cancel" button, close the modal
        cancelBtn.addEventListener('click', function () {
            logoutModal.style.display = 'none';
        });

        // When the user clicks anywhere outside of the modal, close it
        window.addEventListener('click', function (event) {
            if (event.target == logoutModal) {
                logoutModal.style.display = 'none';
            }
        });
    </script>


    <!-- Navigation Bar -->
    <nav class="navigation">
        <div class="nav-center container d-flex">
            <a href="admin_dashboard.html" class="logo">Clothing Store Admin</a>
            <ul class="nav-list d-flex">
                <li class="nav-item">
                    <a href="admin_dashboard.html" class="nav-link active">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="admin_products.php" class="nav-link">Products</a>
                </li>
                <li class="nav-item">
                    <a href="admin_orders.php" class="nav-link">Orders</a>
                </li>
                <li class="nav-item">
                    <a href="admin_users.php" class="nav-link">Users</a>
                </li>
                <li class="nav-item">
                    <a href="admin_report.html" class="nav-link">Reports</a>
                </li>
                <li class="nav-item">
                    <a href="admin_settings.php" class="nav-link">Settings</a>
                </li>
            </ul>
            <!-- Hamburger Menu for Mobile -->
            <div class="hamburger">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

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