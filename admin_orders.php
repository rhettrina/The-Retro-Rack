<?php
// Start the session
session_start();

// Include the database configuration file
require_once 'config.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to admin login page
    header('Location: index.php');
    exit();
}

// Fetch orders from the database
$sql = "SELECT 
            o.id AS order_id,
            u.fullname AS user_name,
            GROUP_CONCAT(CONCAT(p.name, ' x', oi.quantity) SEPARATOR ', ') AS products,
            o.status,
            o.total_amount,
            DATE_FORMAT(o.order_date, '%Y-%m-%d') AS order_date
        FROM orders o
        INNER JOIN users u ON o.user_id = u.id
        INNER JOIN order_items oi ON o.id = oi.order_id
        INNER JOIN products p ON oi.product_id = p.id
        GROUP BY o.id
        ORDER BY o.order_date DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die('Error executing query: ' . mysqli_error($conn));
}

// Define the statuses
$statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled', 'Returned'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Orders - Clothing Store</title>
    <!-- Link to existing stylesheets -->
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/admin_order.css">
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <!-- Add any additional CSS styles for status badges -->
    <style>
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status.Pending {
            background-color: orange;
        }

        .status.Delivered {
            background-color: green;
        }

        .status.Cancelled {
            background-color: red;
        }

        /* Additional status styles can be added here */
    </style>
</head>

<body>
    <?php include 'adminheader.php'; ?>
    <?php
    // Display Success or Error Messages
    if (isset($_SESSION['success_message'])) {
        echo "<div class='success-message'>" . htmlspecialchars($_SESSION['success_message']) . "</div>";
        unset($_SESSION['success_message']);
    }

    if (isset($_SESSION['error_message'])) {
        echo "<div class='error-message'>" . htmlspecialchars($_SESSION['error_message']) . "</div>";
        unset($_SESSION['error_message']);
    }
    ?>

    <!-- Orders Table -->
    <!-- Main Content -->
    <section class="section main-admin">
        <div class="container">
            <!-- Display Success or Error Messages -->
            <?php
            if (isset($_SESSION['success_message'])) {
                echo "<div class='success-message'>" . htmlspecialchars($_SESSION['success_message']) . "</div>";
                unset($_SESSION['success_message']);
            }

            if (isset($_SESSION['error_message'])) {
                echo "<div class='error-message'>" . htmlspecialchars($_SESSION['error_message']) . "</div>";
                unset($_SESSION['error_message']);
            }
            ?>

            <div class="title">
                <h1>Orders</h1>
            </div>

            <!-- Orders Table -->
            <div class="orders-table">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Product</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td data-label="Order ID">#<?php echo htmlspecialchars($row['order_id']); ?></td>
                                    <td data-label="User"><?php echo htmlspecialchars($row['user_name']); ?></td>
                                    <td data-label="Product"><?php echo htmlspecialchars($row['products']); ?></td>
                                    <td data-label="Status">
                                        <form action="update_order_status.php" method="POST">
                                            <input type="hidden" name="order_id"
                                                value="<?php echo htmlspecialchars($row['order_id']); ?>">
                                            <select name="status" onchange="this.form.submit()">
                                                <?php
                                                foreach ($statuses as $status) {
                                                    $selected = ($status == $row['status']) ? 'selected' : '';
                                                    echo "<option value='" . htmlspecialchars($status) . "' $selected>" . htmlspecialchars($status) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td data-label="Total">$<?php echo number_format($row['total_amount'], 2); ?></td>
                                    <td data-label="Date"><?php echo htmlspecialchars($row['order_date']); ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6'>No orders found.</td></tr>";
                        }
                        // Free result set and close database connection
                        mysqli_free_result($result);
                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script>
        // Add JavaScript here if needed
    </script>
</body>

</html>