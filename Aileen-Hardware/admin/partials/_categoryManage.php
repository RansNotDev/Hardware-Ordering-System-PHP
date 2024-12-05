<?php
include '_dbconnect.php';  // Include your database connection file

// Function to handle image upload and return the binary data
function handleImageUpload($imageFile)
{
    // Check if there was an error with the file upload
    if (isset($imageFile) && $imageFile['error'] === UPLOAD_ERR_OK) {
        // Read the image file as binary data and return it
        return file_get_contents($imageFile['tmp_name']);
    } else {
        return null;  // No image or error uploading
    }
}

// Create Category
if (isset($_POST['createCategory'])) {
    $name = $_POST['name'];
    $description = $_POST['desc'];

    // Handle image upload
    $image = handleImageUpload($_FILES['image']);
    if ($image !== null) {
        // Insert category into the database, including the image as binary data
        $sql = "INSERT INTO `categories` (`categorieName`, `categorieDesc`, `image`) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Use a dummy variable for 'b' parameter
        $dummy = null;
        $stmt->bind_param("ssb", $name, $description, $dummy);

        // Send binary data
        $stmt->send_long_data(2, $image);

        if ($stmt->execute()) {
            echo "<script>alert('Category created successfully.'); window.location=document.referrer;</script>";
        } else {
            echo "<script>alert('Failed to create category.'); window.location=document.referrer;</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Image file is required.'); window.location=document.referrer;</script>";
    }
}

// Update Category
if (isset($_POST['updateCategory']) && isset($_POST['categoryId'])) {
    $catId = $_POST['categoryId'];
    $name = $_POST['name'];
    $description = $_POST['desc'];

    // Handle image upload if a new image is provided
    $image = handleImageUpload($_FILES['image']);

    if ($image !== null) {
        // Update category with the new image
        $sql = "UPDATE `categories` SET `categorieName`=?, `categorieDesc`=?, `image`=? WHERE `categorieId`=?";
        $stmt = $conn->prepare($sql);

        // Use a dummy variable for 'b' parameter
        $dummy = null;
        $stmt->bind_param("ssbi", $name, $description, $dummy, $catId);

        // Send binary data
        $stmt->send_long_data(2, $image);

        if ($stmt->execute()) {
            echo "<script>alert('Category updated successfully.'); window.location=document.referrer;</script>";
        } else {
            echo "<script>alert('Failed to update category.'); window.location=document.referrer;</script>";
        }
        $stmt->close();
    } else {
        // Update without changing the image
        $sql = "UPDATE `categories` SET `categorieName`=?, `categorieDesc`=? WHERE `categorieId`=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $description, $catId);

        if ($stmt->execute()) {
            echo "<script>alert('Category updated successfully.'); window.location=document.referrer;</script>";
        } else {
            echo "<script>alert('Failed to update category.'); window.location=document.referrer;</script>";
        }
        $stmt->close();
    }
}

// Remove Category
if (isset($_POST['removeCategory']) && isset($_POST['catId'])) {
    $catId = $_POST['catId'];

    // Prepare the DELETE query to remove the category
    $sql = "DELETE FROM categories WHERE categorieId=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $catId); // Ensure that catId is an integer

    if ($stmt->execute()) {
        echo "<script>alert('Category removed successfully.'); window.location=document.referrer;</script>";
    } else {
        echo "<script>alert('Failed to remove category.'); window.location=document.referrer;</script>";
    }
    $stmt->close();
}
