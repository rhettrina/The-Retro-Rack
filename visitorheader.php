

<link rel="stylesheet" href="./css/index.css">

<div class="top-nav">
    <div class="container d-flex">
        <p>Order Online Or Call Us:(+63) 9073434119</p>
        <ul class="d-flex">
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="admin_dashboard.php" id="adminLink">Admin</a></li>
        </ul>
    </div>
</div>

<!-- Admin Login Modal -->
<div id="adminLoginModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Admin Login</h2>

        <!-- Error message container -->
        <div id="loginError" class="error-message"></div>

        <form id="adminLoginForm" method="post">
            <div class="form-group">
                <label for="admin-username">Username:</label>
                <input type="text" id="admin-username" name="username" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="admin-password">Password:</label>
                <input type="password" id="admin-password" name="password" class="form-input" required>
            </div>
            <button type="submit" class="btn logout-btn">Login</button>
        </form>
    </div>
</div>
<script>
    // Existing modal behavior code
    var modal = document.getElementById('adminLoginModal');
    var btn = document.getElementById('adminLink');
    var span = document.getElementsByClassName('close-button')[0];

    // Open modal when "Admin" link is clicked
    btn.onclick = function (event) {
        event.preventDefault(); // Prevent default action
        modal.style.display = 'block';
    }

    // Close modal when "x" is clicked
    span.onclick = function () {
        modal.style.display = 'none';
    }

    // Close modal when clicking outside of it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Handle form submission via AJAX
    var loginForm = document.getElementById('adminLoginForm');
    loginForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        // Get form data
        var formData = new FormData(loginForm);

        // Send AJAX request
        fetch('admin_login.php', {
            method: 'POST',
            body: formData,
        })
            .then(function (response) {
                return response.json(); // Parse JSON response
            })
            .then(function (data) {
                if (data.success) {
                    // Redirect to admin dashboard
                    window.location.href = 'admin_dashboard.php';
                } else {
                    // Display error message
                    var loginError = document.getElementById('loginError');
                    loginError.textContent = data.error;
                    loginError.style.display = 'block';
                }
            })
            .catch(function (error) {
                console.error('Error:', error);
            });
    });
</script>

<div class="navigation">
    <div class="nav-center container d-flex">
        <a href="index.php" class="logo">
            <h1>The Retro Rack</h1>
        </a>

        <!-- Navigation List -->
        <ul class="nav-list d-flex">
            <li class="nav-item">
                <a href="index.php" class="nav-link">Home</a>
            </li>
            <li class="nav-item">
                <a href="product.php" class="nav-link">Shop</a>
            </li>
            <li class="nav-item">
                <a href="terms.xml" class="nav-link">Terms</a>
            </li>
            <li class="nav-item">
                <a href="about.php" class="nav-link">About</a>
            </li>
            <li class="nav-item">
                <a href="contact.php" class="nav-link">Contact</a>
            </li>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <!-- Welcome User -->
                <li class="nav-item" id="welcomeUser">
                    <a href="#" class="nav-link"><?php echo "Welcome " . $_SESSION['username']; ?></a>
                </li>
            <?php endif; ?>
        </ul>

        <!-- Icons -->
        <div class="icons d-flex">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <a href="profile.php" class="icon">
                    <i class="bx bx-user"></i>
                </a>
            <?php else: ?>
                <a href="loginview.php" class="icon">
                    <i class="bx bx-user"></i>
                </a>
            <?php endif; ?>

            <!-- Search Icon and Popup -->
            <div class="search-container">
                <a href="#" class="icon" id="searchIcon">
                    <i class="bx bx-search"></i>
                </a>

                <!-- Search Popup -->
                <div class="search-popup" id="searchPopup">
                    <form action="product.php" method="get">
                        <input type="text" name="search_query" placeholder="Search..." required>
                        <button type="submit">Go</button>
                    </form>
                </div>
            </div>

            <style>
                /* Style for search container */
                .search-container {
                    position: relative;
                    display: inline-block;
                }

                /* Hide the search popup by default */
                .search-popup {
                    display: none;
                    position: absolute;
                    top: 100%;
                    /* Position below the icon */
                    right: 0;
                    /* Align to the right */
                    background-color: var(--white);
                    border: 1px solid var(--black);
                    padding: 10px;
                    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
                    z-index: 1000;
                    white-space: nowrap;
                }

                /* Show the search popup when active */
                .search-container.active .search-popup {
                    display: block;
                }

                /* Style the input field and button */
                .search-popup input[type="text"] {
                    padding: 5px;
                    width: 150px;
                    /* Adjust as needed */
                    border: 1px solid var(--black);
                    border-radius: 4px;
                }

                .search-popup button {
                    padding: 5px 10px;
                    margin-left: 5px;
                    background-color: var(--orange);
                    color: var(--white);
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }

                .search-popup button:hover {
                    background-color: var(--beige);
                }

                .search-popup input[type="text"],
                .search-popup button {
                    font-size: 14px;
                }

                /* Adjust alignment of icons */
                .icons .icon {
                    margin-left: 15px;
                }
            </style>

            <script>
                // Existing JavaScript code...

                // Search Popup Functionality
                // Get references to elements
                var searchIcon = document.getElementById('searchIcon');
                var searchContainer = document.querySelector('.search-container');
                var searchPopup = document.getElementById('searchPopup');

                // Toggle the popup when the search icon is clicked
                searchIcon.addEventListener('click', function (event) {
                    event.preventDefault(); // Prevent default action
                    searchContainer.classList.toggle('active');

                    // Focus the input field when the popup appears
                    if (searchContainer.classList.contains('active')) {
                        searchPopup.querySelector('input[name="search_query"]').focus();
                    }
                });

                // Close the popup when clicking outside
                document.addEventListener('click', function (event) {
                    // Check if the click is outside the search container
                    if (!searchContainer.contains(event.target) && searchContainer.classList.contains('active')) {
                        searchContainer.classList.remove('active');
                    }
                });
            </script>

            <a href="cart.php" class="icon">
                <i class="bx bx-cart"></i>
                <span class="d-flex">
                    <?php
                    // Initialize cart count
                    $cart_count = 0;

                    // Check if user is logged in
                    if (isset($_SESSION['id'])) {
                        $user_id = $_SESSION['id'];

                        // Prepare the SQL query to sum the quantities for the current user
                        $stmt = $conn->prepare("SELECT SUM(quantity) AS total_quantity FROM cart_items WHERE user_id = ?");
                        if ($stmt) {
                            // Bind the user ID parameter
                            $stmt->bind_param("i", $user_id);

                            // Execute the statement
                            $stmt->execute();

                            // Bind the result to a variable
                            $stmt->bind_result($total_quantity);

                            // Fetch the result
                            $stmt->fetch();

                            // Set cart count
                            $cart_count = $total_quantity ? $total_quantity : 0;

                            // Close the statement
                            $stmt->close();
                        } else {
                            // Handle query preparation error
                            // Uncomment the following line to debug
                            // echo "Error preparing statement: " . $conn->error;
                        }
                    } else {
                        // If the user is not logged in, cart count is 0
                        $cart_count = 0;
                    }

                    echo $cart_count;
                    ?>
                </span>
            </a>

            <!-- Logout Icon -->
            <a href="logout.php" class="icon" id="logoutIcon">
                <i class="bx bx-log-out"></i>
            </a>
        </div>



        <!-- Logout Confirmation Modal -->
        <div id="logoutModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeModal">&times;</span>
                <p>Are you sure you want to log out?</p>
                <div class="modal-buttons">
                    <button class="btn cancel-btn" id="cancelLogout">Cancel</button>
                    <a href="logout.php" class="btn logout-btn" id="confirmLogout">Log out</a>
                </div>
            </div>
        </div>

        <!-- JavaScript -->
        <script>
            // Get DOM elements
            var logoutIcon = document.getElementById('logoutIcon');
            var logoutModal = document.getElementById('logoutModal');
            var closeModal = document.getElementById('closeModal');
            var cancelLogout = document.getElementById('cancelLogout');

            // When the user clicks on the logout icon, open the modal
            logoutIcon.addEventListener('click', function (event) {
                event.preventDefault(); // Prevent default action
                logoutModal.style.display = "block";
            });

            // When the user clicks on the close button (X), close the modal
            closeModal.addEventListener('click', function () {
                logoutModal.style.display = "none";
            });

            // When the user clicks on the "Cancel" button, close the modal
            cancelLogout.addEventListener('click', function () {
                logoutModal.style.display = "none";
            });

            // When the user clicks anywhere outside of the modal content, close it
            window.addEventListener('click', function (event) {
                if (event.target == logoutModal) {
                    logoutModal.style.display = "none";
                }
            });
        </script>

        <div class="hamburger">
            <i class="bx bx-menu-alt-left"></i>
        </div>
    </div>
</div>