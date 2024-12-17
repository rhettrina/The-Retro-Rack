<?php
// delete_admin.php

require_once 'config.php';
session_start();

// Check if 'id' is set in the URL
if (isset($_GET['id'])) {
    $admin_id = intval($_GET['id']);

    if ($_SESSION['admin_id'] == $admin_id) {
        $_SESSION['errors'] = ["Cannot delete your own account."];
        header("Location: admin_settings.php");
        exit();
    }

    // Prepare the delete statement
    $sql = "DELETE FROM admins WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $admin_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Admin deleted successfully.";
            header("Location: admin_settings.php");
            exit();
        } else {
            $_SESSION['errors'] = ["Error deleting admin."];
            header("Location: admin_settings.php");
            exit();
        }

        mysqli_stmt_close($stmt);
    }
} else {
    $_SESSION['errors'] = ["Invalid request."];
    header("Location: admin_settings.php");
    exit();
}

mysqli_close($conn);
?>