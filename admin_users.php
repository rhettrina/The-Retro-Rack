<?php
// admin_users.php

// Include the functions file
include('user_function.php');

// Fetch all users
$users = getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Clothing Store Admin</title>
    <!-- Link to existing styles.css -->
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/admin_users.css">
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
                <li><a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </header>
    
    <!-- Navigation Bar -->
    <nav class="navigation">
        <div class="nav-center container d-flex">
            <a href="admin_dashboard.html" class="logo">Clothing Store Admin</a>
            <ul class="nav-list d-flex">
                <li class="nav-item">
                    <a href="admin_dashboard.html" class="nav-link">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="admin_products.php" class="nav-link">Products</a>
                </li>
                <li class="nav-item">
                    <a href="admin_orders.html" class="nav-link">Orders</a>
                </li>
                <li class="nav-item">
                    <a href="admin_users.php" class="nav-link active">Users</a>
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

    <section class="section main-admin">
        <div class="container">
            <div class="title">
                <h1>Users Management</h1>
            </div>
            
            <div class="users-table">
                <a href="#" class="btn add-user-btn"><i class="fas fa-user-plus"></i> Add New User</a>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Date of Birth</th>
                            <th>Date Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>#U<?php echo str_pad($user['id'], 3, "0", STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($user['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($user['dob']); ?></td>
                                    <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                    <td>
                                        <a href="#" class="btn-action edit"><i class="fas fa-edit"></i></a>
                                        <a href="#" class="btn-action delete"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
                
        </div>
    </section>

    <!-- Scripts -->
    <script>
        // JavaScript for mobile menu toggle
        const hamburger = document.querySelector('.hamburger');
        const navList = document.querySelector('.nav-list');

        hamburger.addEventListener('click', () => {
            navList.classList.toggle('open');
        });
    </script>
</body>
</html>