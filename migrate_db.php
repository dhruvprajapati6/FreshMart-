<?php
require 'db_connect.php';

// 1. Alter users table
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER email");
    echo "Added role column.\n";
} catch(Exception $e) {
    echo "Role column might exist: " . $e->getMessage() . "\n";
}

// 2. Drop and Recreate Products table
try {
    $pdo->exec("DROP TABLE IF EXISTS products");
    $sql = "CREATE TABLE products (
        id VARCHAR(50) PRIMARY KEY,
        en_name VARCHAR(255) NOT NULL,
        gu_name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        en_category VARCHAR(100) NOT NULL,
        gu_category VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        category_id VARCHAR(50) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        weight VARCHAR(50) NOT NULL,
        img VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql);
    echo "Created products table.\n";
} catch(Exception $e) {
    die("Error creating products table: " . $e->getMessage());
}

// 3. Read products.js and extract JSON
$js = file_get_contents('js/products.js');
preg_match('/const productsDB = (\[.*?\]);/s', $js, $matches);
if(isset($matches[1])) {
    $products = json_decode($matches[1], true);
    
    if(is_array($products)) {
        $stmt = $pdo->prepare("INSERT INTO products (id, en_name, gu_name, en_category, gu_category, category_id, price, weight, img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $count = 0;
        foreach($products as $p) {
            $stmt->execute([
                $p['id'], $p['en_name'], $p['gu_name'], $p['en_category'], $p['gu_category'], $p['category_id'], $p['price'], $p['weight'], $p['img']
            ]);
            $count++;
        }
        echo "Inserted $count products successfully!\n";
    } else {
        echo "JSON decode failed.\n";
    }
} else {
    echo "Could not parse products.js\n";
}
?>
