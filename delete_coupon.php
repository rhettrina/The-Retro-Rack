<?php
session_start();
// Include database connection
require_once 'config.php';

// Get the coupon ID from the URL
if (isset($_GET['id'])) {
    $coupon_id = intval($_GET['id']);

    // Delete the coupon
    $delete_query = "DELETE FROM coupons WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, 'i', $coupon_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Coupon deleted successfully.";
    } else {
        $_SESSION['errors'] = ["Failed to delete coupon."];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header("Location: admin_settings.php");
    exit();
} else {
    $_SESSION['errors'] = ["Invalid coupon ID."];
    header("Location: admin_settings.php");
    exit();
}
?>