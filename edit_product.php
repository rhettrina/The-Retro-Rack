<?php
include './config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['productName']);
    $price = floatval($_POST['productPrice']);
    $stock = intval($_POST['productStock']);
    $category = mysqli_real_escape_string($conn, $_POST['productCategory']);
    $description = mysqli_real_escape_string($conn, $_POST['productDescription']);

    // Assume a user ID is available from the session (e.g., for admins)
    session_start();
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

    // Handle the image upload
    $imagePath = '';
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $fileInfo = pathinfo($_FILES['productImage']['name']);
        $fileExtension = strtolower($fileInfo['extension']);
        
        // Validate file extension (only allow jpg, png, gif, etc.)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode(["status" => "error", "message" => "Invalid image file type."]);
            exit;
        }

        // Generate unique name: userID_productID_timestamp.extension
        $timestamp = time();
        $uniqueName = "user{$userId}_prodID_TEMP_{$timestamp}.{$fileExtension}";

        // Temporarily set product ID to TEMP until record is inserted
        $imagePath = $targetDir . $uniqueName;

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES['productImage']['tmp_name'], $imagePath)) {
            echo json_encode(["status" => "error", "message" => "Failed to upload image."]);
            exit;
        }
    }

    // Insert the product into the database
    $sql = "INSERT INTO products (name, price, stock, category, description, image_path, created_at) 
            VALUES ('$name', $price, $stock, '$category', '$description', '', NOW())";

    if (mysqli_query($conn, $sql)) {
        $productId = mysqli_insert_id($conn);

        // Rename the image to include the actual product ID
        if ($imagePath) {
            $finalImageName = "user{$userId}_prodID_{$productId}_{$timestamp}.{$fileExtension}";
            $finalImagePath = $targetDir . $finalImageName;
            rename($imagePath, $finalImagePath);

            // Update the image_path in the database
            $updateSql = "UPDATE products SET image_path = '$finalImagePath' WHERE id = $productId";
            mysqli_query($conn, $updateSql);
        }

        echo json_encode(["status" => "success", "message" => "Product added successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . mysqli_error($conn)]);
    }
    exit;
}
?>
