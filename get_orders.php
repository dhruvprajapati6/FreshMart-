<?php
session_start();
header('Content-Type: application/json');
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch orders for the logged-in user
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formatted_orders = [];

    foreach ($orders as $order) {
        // Format date
        $date = date("M d, Y", strtotime($order['created_at']));

        // Fetch order items
        $stmt_items = $pdo->prepare("SELECT product_id, product_name, price, quantity, img FROM order_items WHERE order_id = ?");
        $stmt_items->execute([$order['id']]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        $formatted_items = [];
        foreach ($items as $item) {
            $formatted_items[] = [
                'id' => $item['product_id'],
                'name' => $item['product_name'],
                'price' => (float)$item['price'],
                'quantity' => (int)$item['quantity'],
                'img' => $item['img']
            ];
        }

        $formatted_orders[] = [
            'id' => $order['order_number'],
            'date' => $date,
            'total' => (float)$order['total_amount'],
            'status' => $order['status'],
            'items' => $formatted_items
        ];
    }

    echo json_encode(['success' => true, 'orders' => $formatted_orders]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
