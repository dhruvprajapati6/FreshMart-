<?php
require_once 'db_connect.php';

// Downgrade everyone to user
$pdo->query("UPDATE users SET role='user'");

// Create or update Dhruv as the ONLY admin
$email = 'dhruv@gmail.com';
$password = 'd023';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->rowCount() > 0) {
    $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin' WHERE email = ?");
    $stmt->execute([$hash, $email]);
} else {
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['Dhruv', 'Prajapati', $email, $hash, 'admin']);
}

echo "Reset all other admins and made dhruv@gmail.com the only admin.\n";
?>
