<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Box icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
    <!-- Custom StyleSheet -->
    <link rel="stylesheet" href="./css/styles.css" />
    <title>Sign Up</title>
    <style>
        /* Styles for error messages */
        .error {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }

        /* Highlight input fields with errors */
        .input-error {
            border-color: red;
        }

        /* Modal Styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1000;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: hidden;
            /* Disable scroll */
            background-color: rgba(0, 0, 0, 0.5);
            /* Black with opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            /* Could be more or less, depending on screen size */
            position: relative;
        }

        /* Close Button */
        .close {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 25px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Loading Line */
        .loading-bar {
            width: 100%;
            height: 4px;
            background-color: #3498db;
            position: absolute;
            top: 0;
            left: 0;
            animation: loading 2s linear infinite;
        }

        @keyframes loading {
            0% {
                width: 0%;
            }

            50% {
                width: 50%;
            }

            100% {
                width: 100%;
            }
        }

        /* Success Message Styling */
        .success-message {
            display: none;
            color: green;
            font-size: 1.2em;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="top-nav">
        <div class="container d-flex">
            <p>Order Online Or Call Us:(+63) 9073434119</p>
            <ul class="d-flex">
                <li><a href="about.html">About Us</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="admin_dashboard.php" id="adminLink">Admin</a></li>
            </ul>
        </div>
    </div>

    <!-- Sign Up Form -->
    <div class="container">
        <div class="login-form">
            <form id="signupForm" action="signup.php" method="post" novalidate>
                <h1>Sign Up</h1>
                <p>
                    Please fill in this form to create an account, or
                    <a href="loginview.php">Login</a>
                </p>

                <!-- Full Name -->

                <label for="fullname">Full Name</label>
                <div class="error" id="fullname_error"></div>
                <input type="text" placeholder="Enter Full Name" name="fullname" id="fullname" required />


                <!-- Username -->
                <label for="username">Username</label>
                <div class="error" id="username_error"></div>
                <input type="text" placeholder="Enter Username" name="username" id="username" required />


                <!-- Email -->
                <label for="email">Email</label>
                <div class="error" id="email_error"></div>
                <input type="email" placeholder="Enter Email" name="email" id="email" required />


                <!-- Phone Number -->
                <label for="phone">Phone Number</label>
                <div class="error" id="phone_error"></div>
                <input type="tel" placeholder="Enter Phone Number" name="phone" id="phone" required />


                <!-- Gender -->
                <label for="gender">Gender</label>
                <div class="error" id="gender_error"></div>
                <div class="gender-options" style="padding: 10px;">
                    <label>
                        <input type="radio" name="gender" value="male" required />
                        Male
                    </label>
                    <label>
                        <input type="radio" name="gender" value="female" required />
                        Female
                    </label>
                    <label>
                        <input type="radio" name="gender" value="other" required />
                        Other
                    </label>
                </div>


                <!-- Date of Birth -->
                <label for="dob">Date of Birth</label>
                <div class="error" id="dob_error"></div>
                <input type="date" name="dob" id="dob" required />


                <!-- Password -->
                <label for="psw">Password</label>
                <div class="error" id="password_error"></div>
                <input type="password" placeholder="Enter Password" name="password" id="password" required />


                <!-- Confirm Password -->
                <label for="psw-repeat">Repeat Password</label>
                <div class="error" id="confirm_password_error"></div>
                <input type="password" placeholder="Repeat Password" name="confirm_password" id="confirm_password"
                    required />


                <!-- Remember Me -->
                <label>
                    <input type="checkbox" checked="checked" name="remember" style="margin-bottom: 15px" />
                    Remember me
                </label>

                <p>
                    By creating an account you agree to our
                    <a href="terms.xml">Terms & Privacy</a>.
                </p>

                <!-- Buttons -->
                <div class="buttons">
                    <button type="button" class="cancelbtn">Cancel</button>
                    <button type="submit" class="signupbtn">Sign Up</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Unified Modal -->
    <div id="unifiedModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="loading-bar" id="loadingBar"></div>
            <p id="modalMessage">Processing your sign-up...</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="row">
            <div class="col d-flex">
                <h4>INFORMATION</h4>
                <a href="">About us</a>
                <a href="">Contact Us</a>
                <a href="">Term & Conditions</a>
                <a href="">Shipping Guide</a>
            </div>
            <div class="col d-flex">
                <h4>USEFUL LINK</h4>
                <a href="">Online Store</a>
                <a href="">Customer Services</a>
                <a href="">Promotion</a>
                <a href="">Top Brands</a>
            </div>
            <div class="col d-flex">
                <span><i class="bx bxl-facebook-square"></i></span>
                <span><i class="bx bxl-instagram-alt"></i></span>
                <span><i class="bx bxl-github"></i></span>
                <span><i class="bx bxl-twitter"></i></span>
                <span><i class="bx bxl-pinterest"></i></span>
            </div>
        </div>
    </footer>

    <!-- Custom Script -->
    <script src="./js/index.js"></script>
    <script>
        // Modal Handling
        const modal = document.getElementById("unifiedModal");
        const span = document.getElementsByClassName("close")[0];
        const modalMessage = document.getElementById("modalMessage");
        const loadingBar = document.getElementById("loadingBar");

        // Close modal when 'x' is clicked
        span.onclick = function () {
            modal.style.display = "none";
        };

        // Close modal when clicking outside the modal content
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };

        // Form Submission via AJAX
        const signupForm = document.getElementById("signupForm");

        signupForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent default form submission

            // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.textContent = '');
            document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));

            // Show modal with loading
            modalMessage.textContent = "Processing your sign-up...";
            loadingBar.style.display = "block";
            modal.style.display = "block";

            // Collect form data
            const formData = new FormData(signupForm);

            // Send AJAX request
            fetch('signup.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    // Hide loading bar
                    loadingBar.style.display = "none";

                    if (data.success) {
                        // Show success message
                        modalMessage.textContent = "Sign-up successful! Redirecting to login page...";
                        // Redirect after 2 seconds
                        setTimeout(() => {
                            window.location.href = 'loginview.php';
                        }, 2000);
                    } else {
                        // Display validation errors
                        for (let field in data.errors) {
                            const errorDiv = document.getElementById(`${field}_error`);
                            const inputField = document.getElementById(field);
                            if (errorDiv && data.errors[field]) {
                                errorDiv.textContent = data.errors[field];
                                if (inputField) {
                                    inputField.classList.add('input-error');
                                }
                            }
                        }
                        // Optionally, update modal message
                        modalMessage.textContent = "Please correct the errors highlighted below.";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingBar.style.display = "none";
                    modalMessage.textContent = "An unexpected error occurred. Please try again later.";
                });
        });

        // Cancel Button Functionality
        document.querySelector('.cancelbtn').addEventListener('click', function () {
            signupForm.reset();
            document.querySelectorAll('.error').forEach(el => el.textContent = '');
            document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        });
    </script>
</body>

</html>