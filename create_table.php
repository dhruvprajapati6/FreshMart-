<?php
require_once 'db_connect.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `first_name` varchar(50) NOT NULL,
      `last_name` varchar(50) NOT NULL,
      `email` varchar(100) NOT NULL,
      `password` varchar(255) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    $pdo->exec($sql);
    echo "<div style='font-family:sans-serif; text-align:center; padding:50px; background:#e8fbf4; color:#00b37e;'>";
    echo "<h1>✅ Success!</h1>";
    echo "<h3>The database table 'users' has been created successfully.</h3>";
    echo "<br><br>";
    echo "<a href='register.html' style='background:#00b37e; color:white; padding:15px 30px; text-decoration:none; border-radius:5px; font-weight:bold;'>Go back to Register Page</a>";
    echo "</div>";
} catch(PDOException $e) {
    echo "<h1>❌ Error creating table</h1>";
    echo $e->getMessage();
}
?>
