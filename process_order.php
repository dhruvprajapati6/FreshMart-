<?php
session_start();
header('Content-Type: application/json');
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id']; // Ensure session has user_id stored during login

// Read JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Empty cart']);
    exit;
}

$items = $data['items'];
$total_amount = $data['total'];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Generate unique order number
    $order_number = 'ORD-' . strtoupper(uniqid());

    // Insert into orders
    $stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, total_amount) VALUES (?, ?, ?)");
    $stmt->execute([$order_number, $user_id, $total_amount]);
    $order_id = $pdo->lastInsertId();

    // Insert into order_items
    $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, img) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmt_item->execute([
            $order_id,
            $item['id'],
            $item['name'],
            $item['price'],
            $item['quantity'],
            $item['img']
        ]);
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Order placed successfully',
        'order_number' => $order_number
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
