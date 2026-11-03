<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        die("Please enter both email and password.");
    }

    // Prepare statement to find user
    $sql = "SELECT id, first_name, last_name, email, password, profile_photo, mobile, role FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a new session
            session_regenerate_id();
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['profile_photo'] = $user['profile_photo'] ?? 'default.png';
            $_SESSION['mobile'] = $user['mobile'] ?? '';
            $_SESSION['role'] = $user['role'] ?? 'user';
            
            // Redirect based on role
            if ($_SESSION['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.html");
            }
            exit;
        } else {
            // Incorrect password
            echo "<script>alert('Invalid email or password.'); window.location.href='login.html';</script>";
        }
    } else {
        // User not found
        echo "<script>alert('Invalid email or password.'); window.location.href='login.html';</script>";
    }
} else {
    header("Location: login.html");
    exit;
}
