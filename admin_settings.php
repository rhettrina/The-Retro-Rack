<?php
// Start session or include authentication checks if necessary

// Include your database connection code
require_once 'config.php'; // This file contains your database connection code

// Fetch existing admins from the database
$query = "SELECT id, username, role, active FROM admins";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Settings - Clothing Store</title>

    <!-- Link to existing styles.css -->
    <link rel="stylesheet" href="./css/styles.css">
    <!-- Link to new admin_settings.css -->
    <link rel="stylesheet" href="./css/admin_settings.css">
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body>
    <?php include 'adminheader.php'; ?>

    <!-- Main Content -->
    <section class="section main-admin">
        <div class="container">
            <div class="title">
                <h1>Settings</h1>
            </div>
            <div class="settings-section">
                <!-- Discount Coupon Creator and Table -->
                <div class="settings-pair">
                    <!-- Coupon Creator Form -->
                    <div class="creator-form">
                        <h2>Create Discount Coupon</h2>
                        <form action="create_coupon.php" method="post" class="coupon-form">
                            <label for="code">Coupon Code:</label>
                            <input type="text" id="code" name="code" required>

                            <label for="discount">Discount Percentage:</label>
                            <input type="number" id="discount" name="discount" min="1" max="100" required>

                            <label for="expiry">Expiry Date:</label>
                            <input type="date" id="expiry" name="expiry" required>

                            <button type="submit" class="btn">Create Coupon</button>
                        </form>
                    </div>
                    <!-- Coupons Table -->
                    <div class="table-container">
                        <h2>Existing Coupons</h2>
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Coupon Code</th>
                                        <th>Discount (%)</th>
                                        <th>Expiry Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Sample Data -->
                                    <tr>
                                        <td>SAVE10</td>
                                        <td>10</td>
                                        <td>2024-12-31</td>
                                        <td>
                                            <a href="#" class="btn-action edit">Edit</a>
                                            <a href="#" class="btn-action delete">Delete</a>
                                        </td>
                                    </tr>
                                    <!-- Add more rows dynamically from your database -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Admin Creator and Table -->
                <div class="settings-pair">
                    <!-- Admin Creator Form -->
                    <div class="creator-form">
                        <h2>Create New Admin</h2>
                        <form action="create_admin.php" method="post" class="admin-form">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" required>

                            <label for="role">Role:</label>
                            <select id="role" name="role" required>
                                <option value="" disabled selected>Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="seller">Seller</option>
                            </select>

                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required>

                            <button type="submit" class="btn">Create Admin</button>
                        </form>
                    </div>
                    <!-- Admins Table -->
                    <div class="table-container">
                        <h2>Existing Admins</h2>
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Password</th>
                                        <th>Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Check if there are any admins
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($admin = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
                                            echo "<td>" . ucfirst(htmlspecialchars($admin['role'])) . "</td>";
                                            echo "<td>••••••••</td>"; // Hide actual password
                                            echo "<td>" . ($admin['active'] ? 'Active' : 'Inactive') . "</td>";
                                            echo "<td>
                                                <a href='edit_admin.php?id=" . $admin['id'] . "' class='btn-action edit'>Edit</a>
                                                <a href='delete_admin.php?id=" . $admin['id'] . "' class='btn-action delete' onclick=\"return confirm('Are you sure you want to delete this admin?');\">Delete</a>
                                            </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No admins found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Scripts -->
    <script>
        // Add JavaScript here if needed (e.g., form validation)
    </script>
</body>

</html>