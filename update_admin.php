<?php
// update_admin.php
session_start();
require_once 'config.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_id = intval($_POST['id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Initialize an array to store errors
    $errors = [];

    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    if (empty($role)) {
        $errors[] = "Role is required.";
    }

    if (empty($errors)) {
        if (!empty($password)) {
            // Hash the new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update username, role, and password
            $sql = "UPDATE admins SET username = ?, role = ?, password = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, 'sssi', $username, $role, $hashed_password, $admin_id);
            }
        } else {
            // Update username and role only
            $sql = "UPDATE admins SET username = ?, role = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, 'ssi', $username, $role, $admin_id);
            }
        }

        if ($stmt) {
            if (mysqli_stmt_execute($stmt)) {
                // Success
                $_SESSION['success'] = 'Admin updated successfully.';
                header("Location: admin_settings.php");
                exit();
            } else {
                $errors[] = 'Error updating admin.';
            }
            mysqli_stmt_close($stmt);
        } else {
            $errors[] = 'Error preparing statement.';
        }
    }

    mysqli_close($conn);
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: admin_settings.php");
        exit();
    }

} else {
    // Invalid request
    header("Location: admin_settings.php");
    exit();
}
?>