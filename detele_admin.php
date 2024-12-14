<?php
// delete_admin.php

require_once 'config.php';

// Check if 'id' is set in the URL
if (isset($_GET['id'])) {
    $admin_id = intval($_GET['id']);


    session_start();
    if ($_SESSION['admin_id'] == $admin_id) {
        header("Location: admin_settings.php?error=Cannot delete your own account.");
        exit();
    }

    // Prepare the delete statement
    $sql = "DELETE FROM admins WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $admin_id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: admin_settings.php?message=Admin deleted successfully.");
            exit();
        } else {
            header("Location: admin_settings.php?error=Error deleting admin.");
            exit();
        }

        mysqli_stmt_close($stmt);
    }
} else {
    header("Location: admin_settings.php?error=Invalid request.");
    exit();
}

mysqli_close($conn);
?>