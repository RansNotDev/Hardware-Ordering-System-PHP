<?php
include '_dbconnect.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {

    // Function to handle image upload and return the binary data
    function handleImageUpload($imageFile) {
        if (isset($imageFile) && $imageFile['error'] === UPLOAD_ERR_OK) {
            return file_get_contents($imageFile['tmp_name']);
        } else {
            return null;
        }
    }

    if (isset($_POST['removeUser'])) {
        $Id = $_POST["Id"];
        $sql = "DELETE FROM `users` WHERE `id`='$Id'";   
        $result = mysqli_query($conn, $sql);
        echo "<script>alert('Removed');
            window.location=document.referrer;
            </script>";
    }

    if (isset($_POST['createUser'])) {
        $username = $_POST["username"];
        $firstName = $_POST["firstName"];
        $lastName = $_POST["lastName"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $userType = $_POST["userType"];
        $password = $_POST["password"];
        $cpassword = $_POST["cpassword"];

        // Check whether this username exists
        $existSql = "SELECT * FROM `users` WHERE username = '$username'";
        $result = mysqli_query($conn, $existSql);
        $numExistRows = mysqli_num_rows($result);
        if ($numExistRows > 0) {
            echo "<script>alert('Username Already Exists');
                    window.location=document.referrer;
                </script>";
        } else {
            if (($password == $cpassword)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO `users` (`username`, `firstName`, `lastName`, `email`, `phone`, `userType`, `password`, `joinDate`) VALUES (?, ?, ?, ?, ?, ?, ?, current_timestamp())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssss", $username, $firstName, $lastName, $email, $phone, $userType, $hash);

                if ($stmt->execute()) {
                    echo "<script>alert('Success');
                            window.location=document.referrer;
                        </script>";
                } else {
                    echo "<script>alert('Failed');
                            window.location=document.referrer;
                        </script>";
                }
                $stmt->close();
            } else {
                echo "<script>alert('Passwords do not match');
                    window.location=document.referrer;
                </script>";
            }
        }
    }

    if (isset($_POST['editUser'])) {
        $id = $_POST["userId"];
        $firstName = $_POST["firstName"];
        $lastName = $_POST["lastName"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $userType = $_POST["userType"];

        $sql = "UPDATE `users` SET `firstName`=?, `lastName`=?, `email`=?, `phone`=?, `userType`=? WHERE `id`=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $firstName, $lastName, $email, $phone, $userType, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Update successful');
                window.location=document.referrer;
                </script>";
        } else {
            echo "<script>alert('Failed');
                window.location=document.referrer;
                </script>";
        }
        $stmt->close();
    }

    if (isset($_POST['updateProfilePhoto'])) {
        $id = $_POST["userId"];
        $image = handleImageUpload($_FILES['userimage']);

        if ($image !== null) {
            $sql = "UPDATE `users` SET `image`=? WHERE `id`=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("bi", $dummy, $id);
            $stmt->send_long_data(0, $image);

            if ($stmt->execute()) {
                echo "<script>alert('Profile photo updated successfully');
                        window.location=document.referrer;
                    </script>";
            } else {
                echo "<script>alert('Failed to update profile photo');
                        window.location=document.referrer;
                    </script>";
            }
            $stmt->close();
        } else {
            echo '<script>alert("Please select an image file to upload.");
            window.location=document.referrer;
                </script>';
        }
    }

    if (isset($_POST['removeProfilePhoto'])) {
        $id = $_POST["userId"];
        $sql = "UPDATE `users` SET `image`=NULL WHERE `id`=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<script>alert('Profile photo removed');
                window.location=document.referrer;
            </script>";
        } else {
            echo "<script>alert('Failed to remove photo');
                window.location=document.referrer;
            </script>";
        }
        $stmt->close();
    }
}
?>
