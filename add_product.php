<?php
include './config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['productName']);
    $price = floatval($_POST['productPrice']);
    $stock = intval($_POST['productStock']);
    $category = mysqli_real_escape_string($conn, $_POST['productCategory']);
    $description = mysqli_real_escape_string($conn, $_POST['productDescription']);

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

        // Generate unique name: productName_timestamp.extension
        $timestamp = time();
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name); // Replace unsafe characters
        $uniqueName = "{$safeName}_{$timestamp}.{$fileExtension}";

        $imagePath = $targetDir . $uniqueName;

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES['productImage']['tmp_name'], $imagePath)) {
            echo json_encode(["status" => "error", "message" => "Failed to upload image."]);
            exit;
        }
    }

    // Insert the product into the database
    $sql = "INSERT INTO products (name, price, stock, category, description, image_path, created_at) 
            VALUES ('$name', $price, $stock, '$category', '$description', '$imagePath', NOW())";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success", "message" => "Product added successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . mysqli_error($conn)]);
    }
    exit;
}
?>
