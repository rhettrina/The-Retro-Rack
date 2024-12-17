<?php
session_start();

// Include your database connection code
require_once 'config.php'; // This file contains your database connection code

// Fetch existing admins from the database
$query = "SELECT id, username, role, active FROM admins";
$result = mysqli_query($conn, $query);

// Fetch existing coupons from the database
$coupon_query = "SELECT * FROM coupons ORDER BY expiry_date DESC";
$coupon_result = mysqli_query($conn, $coupon_query);

// Retrieve success or error messages from session
$success = '';
$errors = [];

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Settings - Clothing Store</title>
    <link rel="icon" href="./images/logoretro.png" type="image/x-icon">
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
                                    <?php
                                    // Check if there are any coupons
                                    if ($coupon_result && mysqli_num_rows($coupon_result) > 0) {
                                        while ($coupon = mysqli_fetch_assoc($coupon_result)) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($coupon['code']) . "</td>";
                                            echo "<td>" . htmlspecialchars($coupon['discount_percentage']) . "</td>";
                                            echo "<td>" . htmlspecialchars($coupon['expiry_date']) . "</td>";
                                            echo "<td>
        <a href='delete_coupon.php?id=" . $coupon['id'] . "' class='btn-action delete' onclick=\"openConfirmationModal('Are you sure you want to delete this coupon?', 'delete_coupon.php?id=" . $coupon['id'] . "'); return false;\">Delete</a>
    </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>No coupons found.</td></tr>";
                                    }
                                    ?>
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
    <a href='#' class='btn-action edit' onclick=\"openEditAdminModal(" . $admin['id'] . "); return false;\">Edit</a>
    <a href='delete_admin.php?id=" . $admin['id'] . "' class='btn-action delete' onclick=\"openConfirmationModal('Are you sure you want to delete this admin?', 'delete_admin.php?id=" . $admin['id'] . "'); return false;\">Delete</a>
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
    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeConfirmationModal()">&times;</span>
            <p id="confirmationMessage">Are you sure you want to proceed?</p>
            <div class="modal-actions">
                <button class="btn confirm-btn" onclick="confirmAction()">Confirm</button>
                <button class="btn cancel-btn" onclick="closeConfirmationModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div id="notificationModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeNotificationModal()">&times;</span>
            <p id="notificationMessage"></p>
            <button class="btn close-btn" onclick="closeNotificationModal()"></button>
        </div>
    </div>

    <!-- Edit Admin Modal -->
    <div id="editAdminModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditAdminModal()">&times;</span>
            <h2>Edit Admin</h2>
            <form id="editAdminForm" method="post" action="update_admin.php">
                <input type="hidden" id="editAdminId" name="id">
                <label for="editUsername">Username:</label>
                <input type="text" id="editUsername" name="username" required>

                <label for="editRole">Role:</label>
                <select id="editRole" name="role" required>
                    <option value="" disabled>Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="seller">Seller</option>
                </select>

                <label for="editPassword">Password (leave blank to keep current password):</label>
                <input type="password" id="editPassword" name="password">

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        // Global variables to hold the action and URL
        let actionToConfirm = '';
        let actionUrl = '';

        function openConfirmationModal(message, url) {
            document.getElementById('confirmationMessage').textContent = message;
            document.getElementById('confirmationModal').style.display = 'block';
            actionUrl = url;
        }

        function closeConfirmationModal() {
            document.getElementById('confirmationModal').style.display = 'none';
            actionToConfirm = '';
            actionUrl = '';
        }

        function confirmAction() {
            // Redirect to the action URL
            window.location.href = actionUrl;
        }

        function openNotificationModal(message) {
            document.getElementById('notificationMessage').textContent = message;
            document.getElementById('notificationModal').style.display = 'block';
        }

        function closeNotificationModal() {
            document.getElementById('notificationModal').style.display = 'none';
        }

        // Handle click outside modal to close
        window.onclick = function (event) {
            const confirmationModal = document.getElementById('confirmationModal');
            const notificationModal = document.getElementById('notificationModal');
            const editAdminModal = document.getElementById('editAdminModal');
            if (event.target == confirmationModal) {
                closeConfirmationModal();
            }
            if (event.target == notificationModal) {
                closeNotificationModal();
            }
            if (event.target == editAdminModal) {
                closeEditAdminModal();
            }
        }

        function openEditAdminModal(adminId) {
            // Use AJAX to get the admin details
            fetch('get_admin_details.php?id=' + adminId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate the form with data
                        document.getElementById('editAdminId').value = data.admin.id;
                        document.getElementById('editUsername').value = data.admin.username;
                        document.getElementById('editRole').value = data.admin.role;
                        // No need to populate the password field
                        document.getElementById('editPassword').value = '';

                        // Show the modal
                        document.getElementById('editAdminModal').style.display = 'block';
                    } else {
                        openNotificationModal('Failed to retrieve admin details.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    openNotificationModal('An error occurred while retrieving admin details.');
                });
        }

        function closeEditAdminModal() {
            document.getElementById('editAdminModal').style.display = 'none';
        }

        // Display success or error messages from PHP
        <?php
        if (!empty($success)) {
            echo "openNotificationModal('" . addslashes($success) . "');";
        }
        if (!empty($errors)) {
            $errorMsg = implode('\\n', $errors);
            echo "openNotificationModal('" . addslashes($errorMsg) . "');";
        }
        ?>
    </script>
</body>

</html>