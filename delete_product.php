<?php
include './config.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the product ID
    if (!isset($data['id']) || !is_numeric($data['id'])) {
        echo json_encode(["status" => "error", "message" => "Invalid product ID."]);
        exit;
    }

    $id = intval($data['id']);

    // Retrieve the product's image path before deleting
    $query = "SELECT image_path FROM products WHERE id = $id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        $imagePath = $product['image_path'];

        // Delete the product record
        $sql = "DELETE FROM products WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            // Remove the image file if it exists
            if ($imagePath && file_exists($imagePath)) {
                unlink($imagePath);
            }
            echo json_encode(["status" => "success", "message" => "Product deleted successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Product not found."]);
    }
    exit;
}
?>
