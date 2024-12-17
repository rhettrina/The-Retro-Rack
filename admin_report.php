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

// Fetch data for summary cards

// Fetch total sales
$total_sales_query = "SELECT SUM(total_amount) AS total_sales FROM orders WHERE status = 'Delivered'";
$total_sales_result = mysqli_query($conn, $total_sales_query);
$total_sales_data = mysqli_fetch_assoc($total_sales_result);
$total_sales = $total_sales_data['total_sales'] ?: 0;

// Fetch total orders
$total_orders_query = "SELECT COUNT(*) AS total_orders FROM orders";
$total_orders_result = mysqli_query($conn, $total_orders_query);
$total_orders_data = mysqli_fetch_assoc($total_orders_result);
$total_orders = $total_orders_data['total_orders'];

// Fetch total customers
$total_customers_query = "SELECT COUNT(DISTINCT user_id) AS total_customers FROM orders";
$total_customers_result = mysqli_query($conn, $total_customers_query);
$total_customers_data = mysqli_fetch_assoc($total_customers_result);
$total_customers = $total_customers_data['total_customers'];

// Calculate sales growth
$current_month_sales_query = "
    SELECT SUM(total_amount) AS current_month_sales
    FROM orders
    WHERE status = 'Delivered'
    AND YEAR(order_date) = YEAR(CURRENT_DATE())
    AND MONTH(order_date) = MONTH(CURRENT_DATE())
";
$current_month_result = mysqli_query($conn, $current_month_sales_query);
$current_month_data = mysqli_fetch_assoc($current_month_result);
$current_month_sales = $current_month_data['current_month_sales'] ?: 0;

$previous_month_sales_query = "
    SELECT SUM(total_amount) AS previous_month_sales
    FROM orders
    WHERE status = 'Delivered'
    AND YEAR(order_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
    AND MONTH(order_date) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)
";
$previous_month_result = mysqli_query($conn, $previous_month_sales_query);
$previous_month_data = mysqli_fetch_assoc($previous_month_result);
$previous_month_sales = $previous_month_data['previous_month_sales'] ?: 0;

// Calculate sales growth percentage
if ($previous_month_sales > 0) {
    $sales_growth = (($current_month_sales - $previous_month_sales) / $previous_month_sales) * 100;
} else {
    $sales_growth = 0;
}

// Fetch sales data for the last 6 months
$sales_chart_query = "
    SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, SUM(total_amount) AS monthly_sales
    FROM orders
    WHERE status = 'Delivered'
    AND order_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY YEAR(order_date), MONTH(order_date)
    ORDER BY YEAR(order_date), MONTH(order_date)
";
$sales_chart_result = mysqli_query($conn, $sales_chart_query);

// Initialize arrays
$months = [];
$sales = [];

// Prepare the last 6 months' labels
for ($i = 5; $i >= 0; $i--) {
    $months[] = date('Y-m', strtotime("-$i months"));
    $sales[] = 0; // Initialize with zero sales
}

// Update with actual sales data
while ($row = mysqli_fetch_assoc($sales_chart_result)) {
    $month = $row['month'];
    $index = array_search($month, $months);
    if ($index !== false) {
        $sales[$index] = $row['monthly_sales'];
    }
}

// Encode data for JavaScript
$months_js = json_encode($months);
$sales_js = json_encode($sales);

// Fetch recent sales
$recent_sales_query = "
    SELECT orders.id AS sale_id, users.fullname AS customer_name, orders.total_amount, orders.order_date
    FROM orders
    JOIN users ON orders.user_id = users.id
    WHERE orders.status = 'Delivered'
    ORDER BY orders.order_date DESC
    LIMIT 10
";
$recent_sales_result = mysqli_query($conn, $recent_sales_query);

// Fetch top customers
$top_customers_query = "
    SELECT u.fullname, SUM(o.total_amount) AS total_spent
    FROM users u
    INNER JOIN orders o ON u.id = o.user_id
    WHERE o.status = 'Delivered'
    GROUP BY u.id
    ORDER BY total_spent DESC
    LIMIT 5
";
$top_customers_result = mysqli_query($conn, $top_customers_query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reports - Clothing Store Admin</title>
    <!-- Link to existing stylesheets -->
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/admin_report.css">
    <!-- Include Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body>

    <?php include 'adminheader.php'; ?>
    <!-- Main Content -->
    <section class="section main-admin">
        <div class="container">
            <div class="title">
                <h1>Reports</h1>
            </div>

            <!-- Summary Cards -->
            <div class="report-cards">
                <!-- Card 1: Total Sales -->
                <div class="report-card">
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="report-card-info">
                        <h3>Total Sales</h3>
                        <span>$<?php echo number_format($total_sales, 2); ?></span>
                    </div>
                </div>
                <!-- Card 2: Total Orders -->
                <div class="report-card">
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="report-card-info">
                        <h3>Total Orders</h3>
                        <span><?php echo $total_orders; ?></span>
                    </div>
                </div>
                <!-- Card 3: Total Customers -->
                <div class="report-card">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="report-card-info">
                        <h3>Total Customers</h3>
                        <span><?php echo $total_customers; ?></span>
                    </div>
                </div>
                <!-- Card 4: Sales Growth -->
                <div class="report-card">
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="report-card-info">
                        <h3>Sales Growth</h3>
                        <span>
                            <?php if ($sales_growth > 0) { ?>
                                <?php echo number_format($sales_growth, 2); ?>% &#x2191;
                            <?php } elseif ($sales_growth < 0) { ?>
                                <?php echo number_format(abs($sales_growth), 2); ?>% &#x2193;
                            <?php } else { ?>
                                No Change
                            <?php } ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="sales-chart">
                <h2>Sales Overview</h2>
                <canvas id="salesChart"></canvas>
            </div>

            <!-- Recent Sales Table -->
            <div class="recent-sales">
                <h2>Recent Sales</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Sale ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($sale = mysqli_fetch_assoc($recent_sales_result)) { ?>
                            <tr>
                                <td>#S<?php echo $sale['sale_id']; ?></td>
                                <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                                <td>$<?php echo number_format($sale['total_amount'], 2); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($sale['order_date'])); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Top Customers Table -->
            <div class="top-customers">
                <h2>Top Customers</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Total Spent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($customer = mysqli_fetch_assoc($top_customers_result)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($customer['fullname']); ?></td>
                                <td>$<?php echo number_format($customer['total_spent'], 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

    <!-- Scripts -->
    <script>
        // Get data from PHP
        var months = <?php echo $months_js; ?>;
        var sales = <?php echo $sales_js; ?>;

        // Format months for display
        var formattedMonths = months.map(function (month) {
            var date = new Date(month + '-01');
            var options = {
                year: 'numeric',
                month: 'short'
            };
            return new Intl.DateTimeFormat('en-US', options).format(date);
        });

        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: formattedMonths,
                datasets: [{
                    label: 'Sales ($)',
                    data: sales,
                    backgroundColor: 'rgba(165, 66, 0, 0.2)',
                    borderColor: 'rgba(165, 66, 0, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            },
        });
    </script>
</body>

</html>