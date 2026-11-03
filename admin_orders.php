<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

// Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    header("Location: admin_orders.php?msg=status_updated");
    exit;
}

// Fetch all orders with user details
$sql = "SELECT o.*, u.first_name, u.last_name, u.email, u.mobile 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch items for each order
$order_items = [];
if (count($orders) > 0) {
    $stmt = $pdo->query("SELECT * FROM order_items");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($items as $item) {
        $order_items[$item['order_id']][] = $item;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - FreshMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary-color: #00b37e; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background-color: #fff; box-shadow: 2px 0 10px rgba(0,0,0,0.05); }
        .nav-link { color: #555; font-weight: 500; padding: 12px 20px; border-radius: 8px; margin: 5px 15px; }
        .nav-link:hover, .nav-link.active { background-color: rgba(0,179,126,0.1); color: var(--primary-color); }
        .nav-link i { margin-right: 10px; font-size: 1.2rem; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar py-4" style="width: 250px; flex-shrink: 0;">
            <div class="text-center mb-5">
                <a href="index.html" class="text-decoration-none text-dark fs-3 fw-bold">
                    <i class="bi bi-basket2-fill text-success me-2"></i>Fresh<span style="color:var(--primary-color)">Mart</span>
                </a>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="admin_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_products.php"><i class="bi bi-box-seam"></i> Product Mgt</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="admin_orders.php"><i class="bi bi-cart-check"></i> Order Mgt</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 p-5 overflow-auto" style="height: 100vh;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Order Management</h2>
            </div>

            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Order status updated successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Order ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Update Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#<?php echo $order['order_number']; ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($order['email'] ?? ''); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($order['mobile'] ?? ''); ?></div>
                                </td>
                                <td>
                                    <?php 
                                    if(isset($order_items[$order['id']])) {
                                        foreach($order_items[$order['id']] as $item) {
                                            echo "<div class='small'>" . htmlspecialchars($item['product_name']) . " (x" . $item['quantity'] . ")</div>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="fw-bold">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <?php 
                                        $badge = 'bg-secondary';
                                        if($order['status'] == 'Processing') $badge = 'bg-warning text-dark';
                                        if($order['status'] == 'Shipped') $badge = 'bg-info text-dark';
                                        if($order['status'] == 'Delivered') $badge = 'bg-success';
                                        if($order['status'] == 'Cancelled') $badge = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $badge; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <form method="POST" action="admin_orders.php" class="d-flex align-items-center justify-content-end">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" class="form-select form-select-sm me-2" style="width: 130px;">
                                            <option value="Processing" <?php if($order['status']=='Processing') echo 'selected'; ?>>Processing</option>
                                            <option value="Shipped" <?php if($order['status']=='Shipped') echo 'selected'; ?>>Shipped</option>
                                            <option value="Delivered" <?php if($order['status']=='Delivered') echo 'selected'; ?>>Delivered</option>
                                            <option value="Cancelled" <?php if($order['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-success">Save</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(count($orders) === 0): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No orders found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
