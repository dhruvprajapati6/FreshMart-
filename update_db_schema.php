<?php
require_once 'db_connect.php';

try {
    $sql = "ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255) DEFAULT 'default.png'";
    $pdo->exec($sql);
    echo "Column 'profile_photo' added successfully.";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
