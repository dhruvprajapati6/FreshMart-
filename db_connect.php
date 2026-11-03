<?php
// db_connect.php
$host = 'localhost';
$dbname = 'freshmart';
$username = 'root'; // WAMP default
$password = '';     // WAMP default

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect to database. " . $e->getMessage());
}
