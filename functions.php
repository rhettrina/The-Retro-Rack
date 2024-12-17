<?php
function getUserInfo($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE `id` = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

function getDefaultAddress($conn, $user_id)
{
    $stmt = $conn->prepare(
        "SELECT * FROM `addresses` WHERE `user_id` = ? AND `is_default` = 1"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $address = $result->fetch_assoc();
    $stmt->close();
    return $address;
}

// Add other common functions here...
?>