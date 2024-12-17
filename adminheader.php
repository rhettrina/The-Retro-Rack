<!-- Header -->

<head>
    <link rel="icon" href="./images/logoretro.png" type="image/x-icon">
</head>

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
        <a href="admin_dashboard.php" class="logo">Retro Rack Shop Admin</a>

        <?php
        // Get the current script name
        $current_page = basename($_SERVER['PHP_SELF']);
        ?>

        <ul class="nav-list d-flex">
            <li class="nav-item">
                <a href="admin_dashboard.php" class="nav-link <?php if ($current_page == 'admin_dashboard.php') {
                    echo 'active';
                } ?>">Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="admin_products.php" class="nav-link <?php if ($current_page == 'admin_products.php') {
                    echo 'active';
                } ?>">Products</a>
            </li>
            <li class="nav-item">
                <a href="admin_orders.php" class="nav-link <?php if ($current_page == 'admin_orders.php') {
                    echo 'active';
                } ?>">Orders</a>
            </li>
            <li class="nav-item">
                <a href="admin_users.php" class="nav-link <?php if ($current_page == 'admin_users.php') {
                    echo 'active';
                } ?>">Users</a>
            </li>
            <li class="nav-item">
                <a href="admin_report.php" class="nav-link <?php if ($current_page == 'admin_report.php') {
                    echo 'active';
                } ?>">Reports</a>
            </li>
            <li class="nav-item">
                <a href="admin_settings.php" class="nav-link <?php if ($current_page == 'admin_settings.php') {
                    echo 'active';
                } ?>">Settings</a>
            </li>
        </ul>
        <!-- Hamburger Menu for Mobile -->
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</nav>