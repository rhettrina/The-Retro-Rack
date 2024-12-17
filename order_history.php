<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['message'] = "You need to log in to access this page.";
    header("Location: loginview.php");
    exit();
}

$user_id = $_SESSION['id'];

// Fetch orders for the logged-in user
$stmt = $conn->prepare("
    SELECT o.id, o.order_date, o.total_amount, o.payment_method, o.status
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$orders = [];
while ($order = $orders_result->fetch_assoc()) {
    $orders[] = $order;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Order History</title>
    <!-- Include your stylesheet -->
    <link rel="stylesheet" href="./css/styles.css">
    <!-- Inline CSS for specific styles -->
    <style>
        .order-history-container {
            max-width: 1200px;
            margin: 5rem auto;
            padding: 2rem;
            background-color: var(--white);
            border: 1px solid var(--orange);
            border-radius: 8px;
        }

        .order-history-container h1 {
            margin-bottom: 2rem;
            color: var(--black);
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th,
        .orders-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eaeaea;
        }

        .orders-table th {
            background-color: var(--orange);
            color: var(--white);
        }

        .orders-table td {
            background-color: var(--beige);
            color: var(--black);
        }

        .view-details {
            background-color: var(--orange);
            color: var(--white);
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            text-decoration: none;
        }

        .view-details:hover {
            background-color: var(--black);
            color: var(--white);
        }

        /* Order Details Modal */
        .modal {
            display: none;
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
            background-color: var(--white);
            margin: 5% auto;
            padding: 20px;
            width: 60%;
            border-radius: 8px;
            position: relative;
        }

        .close-button {
            position: absolute;
            right: 1rem;
            top: 1rem;
            font-size: 2rem;
            cursor: pointer;
            color: var(--black);
        }

        .modal-table th,
        .modal-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eaeaea;
        }

        .modal-table th {
            background-color: var(--orange);
            color: var(--white);
        }
    </style>
</head>

<body>
    <?php include 'visitorheader.php'; ?>

    <div class="order-history-container">
        <h1>Your Order History</h1>
        <?php if (count($orders) > 0): ?>
            <table class="orders-table">
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo date('F d, Y h:i A', strtotime($order['order_date'])); ?></td>
                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td>
                            <button class="view-details" data-order-id="<?php echo $order['id']; ?>">View Details</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>You have no orders yet.</p>
        <?php endif; ?>
    </div>

    <!-- Order Details Modal -->
    <div id="order-details-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Order Details</h2>
            <div id="order-details-content">
                <!-- Order items will be loaded here via AJAX -->
            </div>
        </div>
    </div>

    <!-- Include jQuery (You can also use vanilla JS or other libraries) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.view-details').click(function () {
                var orderId = $(this).data('order-id');
                // Fetch order items via AJAX
                $.ajax({
                    url: 'get_order_details.php',
                    type: 'GET',
                    data: { order_id: orderId },
                    success: function (response) {
                        $('#order-details-content').html(response);
                        $('#order-details-modal').css('display', 'block');
                    },
                    error: function () {
                        alert('Error fetching order details.');
                    }
                });
            });

            $('.close-button').click(function () {
                $('#order-details-modal').css('display', 'none');
            });

            // Close modal when clicking outside content
            window.onclick = function (event) {
                if (event.target == document.getElementById('order-details-modal')) {
                    $('#order-details-modal').css('display', 'none');
                }
            };
        });
    </script>
</body>

</html>