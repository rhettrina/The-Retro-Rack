<?php
session_start();
include 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Set a message for the login page
    $_SESSION['message'] = "You need to log in to access this page.";
    // Redirect to login page
    header("Location: loginview.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['id'];

// Determine the active tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

// Initialize variables
$fullname = $username = $email = $phone = $gender = $dob = '';
$fullname_err = $username_err = $email_err = $phone_err = $gender_err = $dob_err = '';
$current_password_err = $new_password_err = $confirm_password_err = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    // Validate inputs
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];

    // Handle password change
    if (!empty($_POST['current_password']) || !empty($_POST['new_password']) || !empty($_POST['confirm_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate current password
        if (empty($current_password)) {
            $current_password_err = "Please enter your current password.";
        } else {
            // Fetch the password from the database
            $sql_password_check = "SELECT password FROM users WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql_password_check)) {
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $hashed_password_db);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);

                // Verify the current password
                if (!password_verify($current_password, $hashed_password_db)) {
                    $current_password_err = "Current password is incorrect.";
                }
            }
        }

        // Validate new password
        if (empty($new_password)) {
            $new_password_err = "Please enter a new password.";
        } elseif (strlen($new_password) < 6) {
            $new_password_err = "Password must be at least 6 characters.";
        }

        // Validate confirm password
        if (empty($confirm_password)) {
            $confirm_password_err = "Please confirm your new password.";
        } elseif ($new_password != $confirm_password) {
            $confirm_password_err = "Passwords do not match.";
        }

        // If no errors, update the password
        if (empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)) {
            // Hash the new password
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            // Update the password in the database
            $sql_update_password = "UPDATE users SET password = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql_update_password)) {
                mysqli_stmt_bind_param($stmt, "si", $hashed_new_password, $user_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $_SESSION['success_message'] = 'Password updated successfully.';
            }
        }
    }

    // If there are no errors, proceed to update the profile information
    if (empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)) {
        $sql = "UPDATE users SET fullname = ?, username = ?, email = ?, phone = ?, gender = ?, dob = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssssi", $fullname, $username, $email, $phone, $gender, $dob, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = 'Profile updated successfully.';
                // Update session variables if needed
                $_SESSION['username'] = $username;
                header("Location: profile.php?tab=profile");
                exit();
            } else {
                echo "Error: Could not update profile.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Retrieve user information
$sql = "SELECT id, fullname, username, email, phone, gender, dob FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $fullname, $username, $email, $phone, $gender, $dob);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo "Something went wrong. Please try again later.";
        exit;
    }
}

// Handle adding a new address
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_address'])) {
    // Retrieve address fields
    $house_number = trim($_POST['house_number']);
    $street = trim($_POST['street']);
    $barangay = trim($_POST['barangay']);
    $city = trim($_POST['city']);
    $province = trim($_POST['province']);
    $postal_code = trim($_POST['postal_code']);
    $country = trim($_POST['country']);
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    // Validate inputs (add validation if needed)

    // If setting as default, unset other defaults
    if ($is_default) {
        $sql = "UPDATE addresses SET is_default = 0 WHERE user_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Insert new address
    $sql = "INSERT INTO addresses (user_id, house_number, street, barangay, city, province, postal_code, country, is_default)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "isssssssi", $user_id, $house_number, $street, $barangay, $city, $province, $postal_code, $country, $is_default);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = 'Address added successfully.';
            header("Location: profile.php?tab=addresses");
            exit();
        } else {
            echo "Error: Could not add address.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle setting default address
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['set_default'])) {
    $address_id = $_POST['address_id'];

    // Unset other default addresses
    $sql = "UPDATE addresses SET is_default = 0 WHERE user_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Set selected address as default
    $sql = "UPDATE addresses SET is_default = 1 WHERE id = ? AND user_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $address_id, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = 'Default address updated successfully.';
            header("Location: profile.php?tab=addresses");
            exit();
        } else {
            echo "Error: Could not set default address.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle deleting an address
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_address'])) {
    $address_id = $_POST['address_id'];

    // Delete the address
    $sql = "DELETE FROM addresses WHERE id = ? AND user_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $address_id, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = 'Address deleted successfully.';
            header("Location: profile.php?tab=addresses");
            exit();
        } else {
            echo "Error: Could not delete address.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Retrieve user's addresses
$sql = "SELECT * FROM addresses WHERE user_id = ?";
$addresses = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $addresses = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <!-- Include your CSS files -->
    <link rel="stylesheet" href="./css/styles.css">
    <!-- Additional styles -->
    <style>
        /* Modal Styles */
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
            border: 1px solid #888;
            width: 50%;
            border-radius: 8px;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Add Address Button Styles */
        .add-address-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 2rem;
        }

        .add-address-btn {
            padding: 1rem 2rem;
            background-color: var(--orange);
            color: var(--white);
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1.6rem;
            transition: background-color 0.3s ease;
        }

        .add-address-btn:hover {
            background-color: var(--black);
        }

        /* Additional styles */
        .disabled-input {
            background-color: #f5f5f5;
            pointer-events: none;
        }

        .edit-button {
            margin-bottom: 1rem;
            padding: 0.8rem 1.5rem;
            background-color: var(--orange);
            color: var(--white);
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1.6rem;
        }

        .edit-button:hover {
            background-color: var(--black);
        }

        .error-message {
            color: red;
            font-size: 1.4rem;
            margin-top: -1rem;
            margin-bottom: 1rem;
        }

        /* Other styles... */
    </style>
</head>

<body>
    <!-- Include your header -->
    <?php include 'visitorheader.php'; ?>

    <div class="profile-container">
        <div class="sidebar">
            <h2>My Account</h2>
            <a href="profile.php?tab=profile" class="<?php echo ($tab == 'profile') ? 'active' : ''; ?>">Profile</a>
            <a href="profile.php?tab=addresses"
                class="<?php echo ($tab == 'addresses') ? 'active' : ''; ?>">Addresses</a>
            <a href="orders.php">Orders</a>
        </div>
        <div class="content">
            <?php
            // Display success message
            if (isset($_SESSION['success_message'])) {
                echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            ?>
            <!-- Profile Tab -->
            <div class="tab-content <?php echo ($tab == 'profile') ? 'active' : ''; ?>">
                <h1>My Profile</h1>
                <button id="editButton" class="edit-button">Edit</button>
                <form class="profile-form" action="profile.php?tab=profile" method="post" id="profileForm">
                    <label for="fullname">Full Name:</label>
                    <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($fullname); ?>"
                        required disabled>
                    <span class="error-message"><?php echo $fullname_err; ?></span>

                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>"
                        required disabled>
                    <span class="error-message"><?php echo $username_err; ?></span>

                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required
                        disabled>
                    <span class="error-message"><?php echo $email_err; ?></span>

                    <label for="phone">Phone Number:</label>
                    <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>" disabled>
                    <span class="error-message"><?php echo $phone_err; ?></span>

                    <label for="gender">Gender:</label>
                    <select name="gender" id="gender" disabled>
                        <option value="">Select Gender</option>
                        <option value="Male" <?php if (strcasecmp($gender, 'Male') == 0)
                            echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if (strcasecmp($gender, 'Female') == 0)
                            echo 'selected'; ?>>Female
                        </option>
                        <option value="Other" <?php if (strcasecmp($gender, 'Other') == 0)
                            echo 'selected'; ?>>Other
                        </option>
                    </select>
                    <span class="error-message"><?php echo $gender_err; ?></span>

                    <label for="dob">Date of Birth:</label>
                    <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($dob); ?>" disabled>
                    <span class="error-message"><?php echo $dob_err; ?></span>

                    <!-- Password Change Section -->
                    <h2>Change Password</h2>
                    <label for="current_password">Current Password:</label>
                    <input type="password" name="current_password" id="current_password" disabled>
                    <span class="error-message"><?php echo $current_password_err; ?></span>

                    <label for="new_password">New Password:</label>
                    <input type="password" name="new_password" id="new_password" disabled>
                    <span class="error-message"><?php echo $new_password_err; ?></span>

                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password" disabled>
                    <span class="error-message"><?php echo $confirm_password_err; ?></span>

                    <!-- Hidden input to identify form submission -->
                    <input type="hidden" name="update_profile" value="1">
                    <button type="submit" id="saveButton" class="edit-button" style="display:none;">Save</button>
                </form>
            </div>

            <style>
                /* Modal Styles */
                .profile-modal {
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

                .profile-modal .modal-content {
                    background-color: var(--white);
                    margin: 5% auto;
                    padding: 20px;
                    border: 1px solid #888;
                    width: 50%;
                    border-radius: 8px;
                }

                .profile-modal .close-button {
                    color: #aaa;
                    float: right;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                }

                .profile-modal .close-button:hover,
                .profile-modal .close-button:focus {
                    color: black;
                    text-decoration: none;
                    cursor: pointer;
                }
            </style>
            <!-- Addresses Tab -->
            <div class="tab-content <?php echo ($tab == 'addresses') ? 'active' : ''; ?>">
                <h1>My Addresses</h1>

                <!-- Button to Add New Address -->
                <div class="add-address-container">
                    <button class="add-address-btn"
                        onclick="document.getElementById('addAddressModal').style.display='block'">Add New
                        Address</button>
                </div>

                <!-- Address List -->
                <div class="address-list">
                    <?php if (!empty($addresses)): ?>
                        <?php foreach ($addresses as $addr): ?>
                            <div class="address-item">
                                <p>
                                    <?php
                                    echo htmlspecialchars($addr['house_number']) . ' ' . htmlspecialchars($addr['street']) . ', ';
                                    echo htmlspecialchars($addr['barangay']) . ', ' . htmlspecialchars($addr['city']) . ', ';
                                    echo htmlspecialchars($addr['province']) . ', ' . htmlspecialchars($addr['postal_code']) . ', ';
                                    echo htmlspecialchars($addr['country']);
                                    ?>
                                </p>
                                <?php if ($addr['is_default']): ?>
                                    <p><strong>Default Address</strong></p>
                                <?php else: ?>
                                    <form action="profile.php?tab=addresses" method="post" style="display:inline;">
                                        <input type="hidden" name="address_id" value="<?php echo $addr['id']; ?>">
                                        <input type="hidden" name="set_default" value="1">
                                        <button type="submit">Set as Default</button>
                                    </form>
                                <?php endif; ?>
                                <!-- Delete Address -->
                                <form action="profile.php?tab=addresses" method="post" style="display:inline;">
                                    <input type="hidden" name="address_id" value="<?php echo $addr['id']; ?>">
                                    <input type="hidden" name="delete_address" value="1">
                                    <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this address?');">Delete</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No addresses found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Add Address Modal -->
            <div id="addAddressModal" class="profile-modal">
                <div class="modal-content">
                    <span onclick="document.getElementById('addAddressModal').style.display='none'"
                        class="close-button">&times;</span>
                    <h2>Add New Address</h2>
                    <form action="profile.php?tab=addresses" method="post">
                        <label for="house_number">House Number:</label>
                        <input type="text" name="house_number" id="house_number" required>

                        <label for="street">Street:</label>
                        <input type="text" name="street" id="street" required>

                        <label for="barangay">Barangay:</label>
                        <input type="text" name="barangay" id="barangay" required>

                        <label for="city">City:</label>
                        <input type="text" name="city" id="city" required>

                        <label for="province">Province:</label>
                        <input type="text" name="province" id="province" required>

                        <label for="postal_code">Postal Code:</label>
                        <input type="text" name="postal_code" id="postal_code">

                        <label for="country">Country:</label>
                        <input type="text" name="country" id="country" value="Philippines" required>

                        <label>
                            <input type="checkbox" name="is_default" value="1"> Set as Default Address
                        </label>

                        <!-- Hidden input to identify form submission -->
                        <input type="hidden" name="add_address" value="1">
                        <button type="submit">Add Address</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Include your footer -->
    <?php include 'visitorfooter.php'; ?>

    <!-- Modal Styles and Script -->
    <style>
        /* ... (Move any inline styles to your styles.css if preferred) */
    </style>

    <script>
        var modal = document.getElementById('addAddressModal');

        // Close modal when clicking outside
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };

        // Close modal when pressing Esc key
        document.onkeydown = function (evt) {
            evt = evt || window.event;
            if (evt.key === "Escape" || evt.key === "Esc") {
                modal.style.display = "none";
            }
        };

        const editButton = document.getElementById('editButton');
        const saveButton = document.getElementById('saveButton');
        const profileForm = document.getElementById('profileForm');
        const formElements = profileForm.elements;

        editButton.addEventListener('click', function () {
            // Enable form fields
            for (let i = 0; i < formElements.length; i++) {
                formElements[i].disabled = false;
            }
            // Hide the edit button and show the save button
            editButton.style.display = 'none';
            saveButton.style.display = 'inline-block';
        });
    </script>

</body>

</html>