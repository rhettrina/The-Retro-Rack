<?php
// get_admin_details.php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $admin_id = intval($_GET['id']);

    $query = "SELECT id, username, role FROM admins WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $admin_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $username, $role);
        if (mysqli_stmt_fetch($stmt)) {
            echo json_encode([
                'success' => true,
                'admin' => [
                    'id' => $id,
                    'username' => $username,
                    'role' => $role
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Admin not found.']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
mysqli_close($conn);
?>