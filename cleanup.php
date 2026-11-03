<?php
require_once 'db_connect.php';
$pdo->query("DELETE FROM users WHERE email='dhruv@gmail.com'");
$pdo->query("DELETE FROM users WHERE email='admin@gmail.com'");
echo "Cleaned up old test accounts.\n";
?>
