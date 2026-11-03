<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // We need to format it slightly so it matches the JS format if needed
    // The DB column names match JS properties exactly because we designed it that way
    
    echo json_encode($products);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
