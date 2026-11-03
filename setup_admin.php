<?php
require_once 'db_connect.php';

$email = 'admin@gmail.com';
$password = 'admin';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if admin already exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->rowCount() > 0) {
    // Update existing
    $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin' WHERE email = ?");
    $stmt->execute([$hashed_password, $email]);
    echo "Admin password updated!\n";
} else {
    // Insert new
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['Admin', 'Owner', $email, $hashed_password, 'admin']);
    echo "Admin user created!\n";
}
?>
