<?php
header('Content-Type: application/json'); // Set response type to JSON
require_once "config.php"; // Include the database configuration file

// Initialize variables
$fullname = $username = $email = $phone = $gender = $dob = $password = $confirm_password = "";
$errors = [];

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Validate Full Name
    if (empty($_POST['fullname'])) {
        $errors['fullname'] = "Full name cannot be blank.";
    } else {
        $fullname = sanitize_input($_POST['fullname']);
    }

    // Validate Username
    if (empty($_POST['username'])) {
        $errors['username'] = "Username cannot be blank.";
    } else {
        $username = sanitize_input($_POST['username']);
        // Check if username exists
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $errors['username'] = "This username is already taken.";
                }
            } else {
                $errors['general'] = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate Email
    if (empty($_POST['email'])) {
        $errors['email'] = "Email cannot be blank.";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    } else {
        $email = sanitize_input($_POST['email']);
        // Check if email exists
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $errors['email'] = "This email is already registered.";
                }
            } else {
                $errors['general'] = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate Phone Number
    if (empty($_POST['phone'])) {
        $errors['phone'] = "Phone number cannot be blank.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $_POST['phone'])) {
        $errors['phone'] = "Phone number must be between 10 and 15 digits.";
    } else {
        $phone = sanitize_input($_POST['phone']);
        // Check if phone number exists
        $sql = "SELECT id FROM users WHERE phone = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $phone);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $errors['phone'] = "This phone number is already registered.";
                }
            } else {
                $errors['general'] = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate Gender
    if (empty($_POST['gender'])) {
        $errors['gender'] = "Please select your gender.";
    } else {
        $gender = sanitize_input($_POST['gender']);
        $allowed_genders = ['male', 'female', 'other'];
        if (!in_array(strtolower($gender), $allowed_genders)) {
            $errors['gender'] = "Invalid gender selection.";
        }
    }

    // Validate Date of Birth
    if (empty($_POST['dob'])) {
        $errors['dob'] = "Date of birth cannot be blank.";
    } else {
        $dob = sanitize_input($_POST['dob']);
        // Optional: Add logic to validate date of birth (e.g., age restriction)
    }

    // Validate Password
    if (empty($_POST['password'])) {
        $errors['password'] = "Password cannot be blank.";
    } elseif (strlen($_POST['password']) < 5) {
        $errors['password'] = "Password must be at least 5 characters long.";
    } else {
        $password = $_POST['password'];
    }

    // Validate Confirm Password
    if (empty($_POST['confirm_password'])) {
        $errors['confirm_password'] = "Please confirm your password.";
    } elseif ($_POST['password'] !== $_POST['confirm_password']) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    // If no errors, proceed to insert the user
    if (empty($errors)) {
        $sql = "INSERT INTO users (fullname, username, email, phone, gender, dob, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "sssssss", $fullname, $username, $email, $phone, $gender, $dob, $hashed_password);
            if (mysqli_stmt_execute($stmt)) {
                // Success
                echo json_encode([
                    'success' => true
                ]);
                exit();
            } else {
                // Execution failed
                $errors['general'] = "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Return errors if any
    echo json_encode([
        'success' => false,
        'errors' => $errors
    ]);

    // Close the database connection
    mysqli_close($conn);
} else {
    // If not a POST request
    echo json_encode([
        'success' => false,
        'errors' => ['general' => 'Invalid request method.']
    ]);
}
?>