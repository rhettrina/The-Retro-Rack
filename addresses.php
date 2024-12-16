<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.html");
    exit;
}

// Include database connection file
require_once 'config.php';

// Retrieve user's addresses from the database
$user_id = $_SESSION['id']; // Assuming you store user ID in session
$sql = "SELECT * FROM addresses WHERE user_id = ?";
$addresses = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $addresses = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    } else {
        echo "Error retrieving addresses.";
        exit;
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing statement.";
    exit;
}

// Handle form submission to add a new address
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process form data
    $address = $_POST['address'];

    // Insert the new address into the database
    $insert_sql = "INSERT INTO addresses (user_id, address) VALUES (?, ?)";
    if ($insert_stmt = mysqli_prepare($conn, $insert_sql)) {
        mysqli_stmt_bind_param($insert_stmt, "is", $user_id, $address);
        if (mysqli_stmt_execute($insert_stmt)) {
            // Insertion successful
            $_SESSION['success_message'] = "Address added successfully.";
            header("location: addresses.php");
            exit;
        } else {
            echo "Error adding address.";
            exit;
        }
        mysqli_stmt_close($insert_stmt);
    } else {
        echo "Error preparing insert statement.";
        exit;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head content -->
    <meta charset="UTF-8">
    <title>Manage Addresses</title>
    <!-- Include your CSS files -->
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <!-- Include your header -->
    <?php include 'visitorheader.php'; ?>

    <div class="addresses-container">
        <h1>My Addresses</h1>
        <?php
        // Display success message if set
        if (isset($_SESSION['success_message'])) {
            echo '<div class="success-message">'.$_SESSION['success_message'].'</div>';
            unset($_SESSION['success_message']);
        }
        ?>

        <!-- List of addresses -->
        <ul class="address-list">
            <?php foreach ($addresses as $addr): ?>
                <li>
                    <?php echo htmlspecialchars($addr['address']); ?>
                    <!-- You can add options to edit or delete the address -->
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Form to add a new address -->
        <h2>Add New Address</h2>
        <form action="addresses.php" method="post">
            <label for="address">Address:</label>
            <textarea name="address" id="address" rows="4" required></textarea>
            <button type="submit" class="btn">Add Address</button>
        </form>

        <!-- Link back to profile -->
        <a href="profile.php" class="btn">Back to Profile</a>
    </div>

    <!-- Include your footer -->
    <?php include 'footer.php'; ?>
</body>
</html>