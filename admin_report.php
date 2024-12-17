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
                <!-- Card 1 -->
                <div class="report-card">
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="report-card-info">
                        <h3>Total Sales</h3>
                        <span>$25,000</span>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="report-card">
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="report-card-info">
                        <h3>Total Orders</h3>
                        <span>350</span>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="report-card">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="report-card-info">
                        <h3>Total Customers</h3>
                        <span>200</span>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="report-card">
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="report-card-info">
                        <h3>Sales Growth</h3>
                        <span>15% ↑</span>
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
                        <!-- Sample Data -->
                        <tr>
                            <td>#S001</td>
                            <td>John Doe</td>
                            <td>$120.00</td>
                            <td>2024-12-10</td>
                        </tr>
                        <tr>
                            <td>#S002</td>
                            <td>Jane Smith</td>
                            <td>$85.50</td>
                            <td>2024-12-09</td>
                        </tr>
                        <!-- Add more rows dynamically from your database -->
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

        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line', // You can change this to 'bar', 'pie', etc.
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June'], // Replace with your data labels
                datasets: [{
                    label: 'Sales ($)',
                    data: [5000, 7000, 8000, 5500, 9000, 10000], // Replace with your data
                    backgroundColor: 'rgba(165, 66, 0, 0.2)', // var(--orange) with 0.2 opacity
                    borderColor: 'rgba(165, 66, 0, 1)', // var(--orange)
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
            },
        });
    </script>
</body>

</html>