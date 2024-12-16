<?php
session_start();
include 'config.php';



// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Set a message to display on the login page
    $_SESSION['message'] = "You need to log in to access this page.";

    // Redirect to loginview.php
    header("Location: loginview.php");
    exit();
}


// Get the user's ID from the session
$user_id = $_SESSION['id'];

// Initialize variables
$fullname = $username = $email = $phone = $gender = $dob = '';
$fullname_err = $username_err = $email_err = $phone_err = $gender_err = $dob_err = '';

// Process form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    // Validate and sanitize input data
    // Full Name
    if (empty(trim($_POST['fullname']))) {
        $fullname_err = 'Please enter your full name.';
    } else {
        $fullname = trim($_POST['fullname']);
    }

    // Username (you may want to make this read-only if not editable)
    if (empty(trim($_POST['username']))) {
        $username_err = 'Please enter a username.';
    } else {
        $username = trim($_POST['username']);
    }

    // Email
    if (empty(trim($_POST['email']))) {
        $email_err = 'Please enter your email.';
    } elseif (!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $email_err = 'Invalid email format.';
    } else {
        $email = trim($_POST['email']);
    }

    // Phone
    if (empty(trim($_POST['phone']))) {
        $phone_err = 'Please enter your phone number.';
    } else {
        $phone = trim($_POST['phone']);
    }

    // Gender
    if (empty($_POST['gender'])) {
        $gender_err = 'Please select your gender.';
    } else {
        $gender = $_POST['gender'];
    }

    // Date of Birth
    if (empty($_POST['dob'])) {
        $dob_err = 'Please enter your date of birth.';
    } else {
        $dob = $_POST['dob'];
    }

    // Check for errors before updating
    if (empty($fullname_err) && empty($username_err) && empty($email_err) && empty($phone_err) && empty($gender_err) && empty($dob_err)) {
        // Update the user's information in the database
        $sql = "UPDATE users SET fullname = ?, username = ?, email = ?, phone = ?, gender = ?, dob = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssssi", $fullname, $username, $email, $phone, $gender, $dob, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                // Update successful
                $_SESSION['success_message'] = 'Profile updated successfully.';
                // Update session variables if needed
                $_SESSION['username'] = $username;
                // Redirect to refresh the page
                header("location: profile.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Retrieve user information from the database
$sql = "SELECT fullname, username, email, phone, gender, dob FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $fullname, $username, $email, $phone, $gender, $dob);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo "Something went wrong. Please try again later.";
        exit;
    }
}

// Process form submission for adding a new address
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_address'])) {
    $address = trim($_POST['address']);
    if (empty($address)) {
        $address_err = 'Please enter an address.';
    } else {
        // Insert the new address into the database
        $sql = "INSERT INTO addresses (user_id, address) VALUES (?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "is", $user_id, $address);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = 'Address added successfully.';
                header("location: profile.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Retrieve user's addresses from the database
$sql = "SELECT id, address FROM addresses WHERE user_id = ?";
$addresses = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $addresses = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Head content -->
    <meta charset="UTF-8">
    <title>User Profile</title>
    <!-- Include your CSS files -->
    <link rel="stylesheet" href="./css/styles.css">
    <style>
        /* Additional styles for profile page */
        body {
            background-color: #f5f5f5;
        }

        .profile-container {
            max-width: 1200px;
            margin: 2rem auto;
            background-color: #fff;
            display: flex;
            border: 1px solid #ddd;
        }

        .sidebar {
            width: 250px;
            border-right: 1px solid #ddd;
            padding: 2rem;
        }

        .sidebar h2 {
            margin-bottom: 2rem;
            font-size: 1.5rem;
            color: #333;
        }

        .sidebar a {
            display: block;
            margin-bottom: 1.5rem;
            color: #555;
            text-decoration: none;
        }

        .sidebar a:hover {
            color: #ff5722;
        }

        .content {
            flex: 1;
            padding: 2rem;
        }

        .content h1 {
            margin-bottom: 2rem;
            font-size: 2rem;
            color: #333;
        }

        .profile-form label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
        }

        .profile-form input,
        .profile-form select {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .profile-form button {
            background-color: #ff5722;
            color: #fff;
            padding: 1rem 2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .profile-form button:hover {
            background-color: #e64a19;
        }

        .addresses h2 {
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            color: #333;
        }

        .address-list {
            margin-bottom: 2rem;
        }

        .address-item {
            background-color: #fafafa;
            padding: 1rem;
            border: 1px solid #ddd;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #d6e9c6;
            border-radius: 4px;
        }

        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <!-- Include your header -->
    <?php include 'visitorheader.php'; ?>

    <div class="profile-container">
        <div class="sidebar">
            <h2>My Account</h2>
            <a href="profile.php">Profile</a>
            <a href="#addresses">Addresses</a>
            <a href="orders.php">Orders</a>
            <!-- Add more links as needed -->
        </div>
        <div class="content">
            <h1>My Profile</h1>

            <?php
            // Display success message if set
            if (isset($_SESSION['success_message'])) {
                echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            ?>

            <form class="profile-form" action="profile.php" method="post">
                <label for="fullname">Full Name:</label>
                <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($fullname); ?>"
                    required>
                <span class="error-message"><?php echo $fullname_err; ?></span>

                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>"
                    required>
                <span class="error-message"><?php echo $username_err; ?></span>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <span class="error-message"><?php echo $email_err; ?></span>

                <label for="phone">Phone Number:</label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>">
                <span class="error-message"><?php echo $phone_err; ?></span>

                <label for="gender">Gender:</label>
                <select name="gender" id="gender">
                    <option value="">Select Gender</option>
                    <option value="Male" <?php if ($gender == 'Male')
                        echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($gender == 'Female')
                        echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($gender == 'Other')
                        echo 'selected'; ?>>Other</option>
                </select>
                <span class="error-message"><?php echo $gender_err; ?></span>

                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($dob); ?>">
                <span class="error-message"><?php echo $dob_err; ?></span>

                <!-- Hidden input to identify form submission -->
                <input type="hidden" name="update_profile" value="1">

                <button type="submit">Update Profile</button>
            </form>

            <div class="addresses" id="addresses">
                <h2>My Addresses</h2>

                <!-- List of addresses -->
                <div class="address-list">
                    <?php if (!empty($addresses)): ?>
                        <?php foreach ($addresses as $addr): ?>
                            <div class="address-item">
                                <?php echo nl2br(htmlspecialchars($addr['address'])); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No addresses found.</p>
                    <?php endif; ?>
                </div>

                <!-- Form to add a new address -->
                <h2>Add New Address</h2>
                <form action="profile.php#addresses" method="post">
                    <label for="address">Address:</label>
                    <textarea name="address" id="address" rows="4" required></textarea>
                    <span class="error-message"><?php echo isset($address_err) ? $address_err : ''; ?></span>

                    <!-- Hidden input to identify form submission -->
                    <input type="hidden" name="add_address" value="1">

                    <button type="submit">Add Address</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Include your footer -->
    <?php include 'visitorfooter.php'; ?>

</body>

</html>