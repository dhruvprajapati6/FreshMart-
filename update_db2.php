<?php
require_once 'db_connect.php';

try {
    $sql = "ALTER TABLE users ADD COLUMN mobile VARCHAR(20) DEFAULT NULL";
    $pdo->exec($sql);
    echo "Column 'mobile' added successfully.";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
