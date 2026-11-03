<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    // Verify Admin Credentials
    if ($role === 'admin') {
        if ($email !== 'dhruv@gmail.com' || $password !== 'd023') {
            die("<script>alert('Error: Invalid Master Email or Password for Admin registration!'); window.location.href='register.html';</script>");
        }
    }

    // Basic validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        die("Please fill all required fields.");
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        die("Error: An account with this email already exists. <a href='login.html'>Login here</a>");
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $sql = "INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$first_name, $last_name, $email, $hashed_password, $role])) {
        // Registration successful, redirect to login page
        echo "<script>alert('Registration successful! Please login.'); window.location.href='login.html';</script>";
        exit;
    } else {
        die("Error: Something went wrong. Please try again later.");
    }
} else {
    // If someone tries to access this file directly without POSTing
    header("Location: register.html");
    exit;
}
