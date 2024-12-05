<?php
include '_dbconnect.php';

// Create Item
if (isset($_POST['createItem'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $categoryId = $_POST['categoryId'];

    // Check if file was uploaded without errors
    if (isset($_FILES["image"])) {
        if ($_FILES["image"]["error"] === UPLOAD_ERR_OK) {
            $imageData = file_get_contents($_FILES["image"]["tmp_name"]);
            $sql = "INSERT INTO `products` (`productName`, `productDesc`, `productPrice`, `productCategorieId`, `image`) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdis", $name, $description, $price, $categoryId, $imageData);

            if ($stmt->execute()) {
                echo "<script>alert('Item created successfully.'); window.location=document.referrer;</script>";
            } else {
                echo "<script>alert('Failed to create item.'); window.location=document.referrer;</script>";
            }
            $stmt->close();
        } else {
            // Handle upload errors
            handleUploadError($_FILES["image"]["error"]);
        }
    }
}

// Update Item
if (isset($_POST['productId'])) {
    $productId = $_POST['productId'];
    $name = $_POST['name'];
    $description = $_POST['desc'];
    $price = $_POST['price'];
    $categoryId = $_POST['categoryId']; // Ensure this matches your form field name

    // Update product details
    $updateSql = "UPDATE `products` SET `productName`=?, `productDesc`=?, `productPrice`=?, `productCategorieId`=? WHERE `productId`=?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssdii", $name, $description, $price, $categoryId, $productId);

    if ($updateStmt->execute()) {
        // Handle Image Update
        if (isset($_FILES["image"])) { // Change "itemimage" to "image"
            if ($_FILES["image"]["error"] === UPLOAD_ERR_OK) {
                $imageData = file_get_contents($_FILES["image"]["tmp_name"]);

                if ($imageData !== false) {
                    // Prepare the SQL statement to update the image
                    $imageSql = "UPDATE `products` SET `image`=? WHERE `productId`=?";
                    $imageStmt = $conn->prepare($imageSql);
                    $imageStmt->bind_param("bi", $imageData, $productId);

                    // Send long data
                    if (!$imageStmt->send_long_data(0, $imageData)) {
                        echo "<script>alert('Error sending long data: " . $imageStmt->error . "'); window.location=document.referrer;</script>";
                    }

                    // Execute the image update
                    if ($imageStmt->execute()) {
                        echo "<script>alert('Item and image updated successfully.'); window.location=document.referrer;</script>";
                    } else {
                        echo "<script>alert('Failed to update image: " . $imageStmt->error . "'); window.location=document.referrer;</script>";
                    }
                    $imageStmt->close();
                } else {
                    echo "<script>alert('Failed to read image data.'); window.location=document.referrer;</script>";
                }
            } else {
                // Handle upload errors
                handleUploadError($_FILES["image"]["error"]);
            }
        } else {
            echo "<script>alert('Item updated successfully without new image.'); window.location=document.referrer;</script>";
        }
    } else {
        echo "<script>alert('Failed to update item: " . $updateStmt->error . "'); window.location=document.referrer;</script>";
    }
    $updateStmt->close();
}

// Remove Item
if (isset($_POST['removeItem'])) {
    $productId = $_POST['productId'];
    $removeSql = "DELETE FROM `products` WHERE `productId`=?";
    $removeStmt = $conn->prepare($removeSql);
    $removeStmt->bind_param("i", $productId);

    if ($removeStmt->execute()) {
        echo "<script>alert('Item deleted successfully.'); window.location=document.referrer;</script>";
    } else {
        echo "<script>alert('Failed to delete item.'); window.location=document.referrer;</script>";
    }
    $removeStmt->close();
}

// Function to handle upload errors
function handleUploadError($error) {
    switch ($error) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            echo "<script>alert('Error: File exceeds the maximum size limit.'); window.location=document.referrer;</script>";
            break;
        case UPLOAD_ERR_NO_FILE:
            echo "<script>alert('Error: No file was uploaded.'); window.location=document.referrer;</script>";
            break;
        default:
            echo "<script>alert('Failed to upload image.'); window.location=document.referrer;</script>";
            break;
    }
}
?>
