<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FreshMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00b37e;
        }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background-color: #fff; box-shadow: 2px 0 10px rgba(0,0,0,0.05); }
        .nav-link { color: #555; font-weight: 500; padding: 12px 20px; border-radius: 8px; margin: 5px 15px; }
        .nav-link:hover, .nav-link.active { background-color: rgba(0,179,126,0.1); color: var(--primary-color); }
        .nav-link i { margin-right: 10px; font-size: 1.2rem; }
        .dashboard-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: transform 0.3s; }
        .dashboard-card:hover { transform: translateY(-5px); }
        .card-icon { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar py-4" style="width: 250px;">
            <div class="text-center mb-5">
                <a href="index.html" class="text-decoration-none text-dark fs-3 fw-bold">
                    <i class="bi bi-basket2-fill text-success me-2"></i>Fresh<span style="color:var(--primary-color)">Mart</span>
                </a>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="admin_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_products.php"><i class="bi bi-box-seam"></i> Product Mgt</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_orders.php"><i class="bi bi-cart-check"></i> Order Mgt</a>
                </li>
                <li class="nav-item mt-5">
                    <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Admin Dashboard</h2>
                <div class="d-flex align-items-center">
                    <img src="uploads/<?php echo htmlspecialchars($_SESSION['profile_photo'] ?? 'default.png'); ?>" class="rounded-circle me-2" style="width:40px; height:40px; object-fit:cover;">
                    <span class="fw-semibold"><?php echo htmlspecialchars($_SESSION['first_name']); ?> (Admin)</span>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-md-6">
                    <a href="admin_products.php" class="text-decoration-none">
                        <div class="card dashboard-card p-4">
                            <div class="d-flex align-items-center">
                                <div class="card-icon bg-success bg-opacity-10 text-success me-4">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-1 text-dark">Product Management</h4>
                                    <p class="text-muted mb-0">Add, Update, or Delete products from the store.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="admin_orders.php" class="text-decoration-none">
                        <div class="card dashboard-card p-4">
                            <div class="d-flex align-items-center">
                                <div class="card-icon bg-primary bg-opacity-10 text-primary me-4">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-1 text-dark">Order Management</h4>
                                    <p class="text-muted mb-0">View customer orders and manage their status.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
