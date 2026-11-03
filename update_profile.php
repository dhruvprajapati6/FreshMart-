<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $filetype = $_FILES['photo']['type'];
        $filesize = $_FILES['photo']['size'];
        
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Error: Only JPG, JPEG, PNG, and GIF files are allowed.'); window.location.href='profile.php';</script>";
            exit;
        }

        if ($filesize > 5 * 1024 * 1024) {
            echo "<script>alert('Error: File size is larger than the allowed limit (5MB).'); window.location.href='profile.php';</script>";
            exit;
        }

        $newFilename = "user_" . $userId . "_" . time() . "." . $ext;
        $uploadPath = "uploads/" . $newFilename;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
            // Update Database
            $sql = "UPDATE users SET profile_photo = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$newFilename, $userId])) {
                $_SESSION['profile_photo'] = $newFilename;
                header("Location: profile.php");
                exit;
            } else {
                echo "<script>alert('Error updating database.'); window.location.href='profile.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Error uploading file.'); window.location.href='profile.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Please select a valid image file.'); window.location.href='profile.php';</script>";
        exit;
    }
} else {
    header("Location: profile.php");
    exit;
}
