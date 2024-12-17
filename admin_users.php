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

    <?php include 'adminheader.php'; ?>

    <section class="section main-admin">
        <div class="container">
            <div class="title">
                <h1>Users Management</h1>
            </div>

            <div class="users-table">

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