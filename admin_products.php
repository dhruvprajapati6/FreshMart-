<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    regenerateProductsJS($pdo);
    header("Location: admin_products.php?msg=deleted");
    exit;
}

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $en_name = $_POST['en_name'] ?? '';
    $gu_name = $_POST['gu_name'] ?? '';
    $en_category = $_POST['en_category'] ?? '';
    $gu_category = $_POST['gu_category'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $price = $_POST['price'] ?? 0;
    $weight = $_POST['weight'] ?? '';
    $img = $_POST['img'] ?? '';
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO products (id, en_name, gu_name, en_category, gu_category, category_id, price, weight, img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id, $en_name, $gu_name, $en_category, $gu_category, $category_id, $price, $weight, $img]);
        regenerateProductsJS($pdo);
        header("Location: admin_products.php?msg=added");
        exit;
    } elseif ($action === 'edit') {
        $stmt = $pdo->prepare("UPDATE products SET en_name=?, gu_name=?, en_category=?, gu_category=?, category_id=?, price=?, weight=?, img=? WHERE id=?");
        $stmt->execute([$en_name, $gu_name, $en_category, $gu_category, $category_id, $price, $weight, $img, $id]);
        regenerateProductsJS($pdo);
        header("Location: admin_products.php?msg=updated");
        exit;
    }
}

function regenerateProductsJS($pdo) {
    $stmt = $pdo->query("SELECT id, en_name, gu_name, en_category, gu_category, category_id, price, weight, img FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Convert numeric fields back to numbers so JS works perfectly
    foreach($products as &$p) {
        $p['price'] = (float)$p['price'];
    }
    $json = json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $js_content = "// Auto-generated Product Database with Isolated White Background Photos\nconst productsDB = " . $json . ";\n";
    file_put_contents('js/products.js', $js_content);

    // Update HTML files with cache buster
    $v = time();
    $html_files = glob("*.html");
    foreach($html_files as $file) {
        $content = file_get_contents($file);
        $content = preg_replace('/js\/products\.js(\?v=[0-9]+)?/', 'js/products.js?v=' . $v, $content);
        file_put_contents($file, $content);
    }
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - FreshMart</title>
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
                    <a class="nav-link active" href="admin_products.php"><i class="bi bi-box-seam"></i> Product Mgt</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_orders.php"><i class="bi bi-cart-check"></i> Order Mgt</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 p-5 overflow-auto" style="height: 100vh;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Product Management</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#productModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Add New Product</button>
            </div>

            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Action completed successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Image</th>
                                <th>ID</th>
                                <th>Name (EN/GU)</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Weight</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $p): ?>
                            <tr>
                                <td class="ps-4"><img src="<?php echo htmlspecialchars($p['img']); ?>" style="width:40px; height:40px; object-fit:cover; border-radius:8px;"></td>
                                <td><?php echo htmlspecialchars($p['id']); ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($p['en_name']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($p['gu_name']); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($p['en_category']); ?></td>
                                <td>₹<?php echo htmlspecialchars($p['price']); ?></td>
                                <td><?php echo htmlspecialchars($p['weight']); ?></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick='editProduct(<?php echo json_encode($p); ?>)'><i class="bi bi-pencil"></i> Edit</button>
                                    <a href="admin_products.php?delete_id=<?php echo urlencode($p['id']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?');"><i class="bi bi-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="admin_products.php">
                    <div class="modal-body row g-3">
                        <input type="hidden" name="action" id="formAction" value="add">
                        
                        <div class="col-md-6">
                            <label class="form-label">Product ID</label>
                            <input type="text" name="id" id="p_id" class="form-control" required placeholder="e.g. p46">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price (₹)</label>
                            <input type="number" step="0.01" name="price" id="p_price" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">English Name</label>
                            <input type="text" name="en_name" id="p_en_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gujarati Name</label>
                            <input type="text" name="gu_name" id="p_gu_name" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">English Category</label>
                            <input type="text" name="en_category" id="p_en_category" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gujarati Category</label>
                            <input type="text" name="gu_category" id="p_gu_category" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category ID</label>
                            <input type="text" name="category_id" id="p_category_id" class="form-control" required placeholder="e.g. vegetables">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Weight</label>
                            <input type="text" name="weight" id="p_weight" class="form-control" required placeholder="e.g. 1 kg">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image Path</label>
                            <input type="text" name="img" id="p_img" class="form-control" required placeholder="img/products/Apple.jpg">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            document.getElementById('modalTitle').textContent = 'Add Product';
            document.getElementById('formAction').value = 'add';
            document.getElementById('p_id').value = '';
            document.getElementById('p_id').readOnly = false;
            document.getElementById('p_en_name').value = '';
            document.getElementById('p_gu_name').value = '';
            document.getElementById('p_en_category').value = '';
            document.getElementById('p_gu_category').value = '';
            document.getElementById('p_category_id').value = '';
            document.getElementById('p_price').value = '';
            document.getElementById('p_weight').value = '';
            document.getElementById('p_img').value = '';
        }

        function editProduct(p) {
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('p_id').value = p.id;
            document.getElementById('p_id').readOnly = true; // Don't allow changing ID
            document.getElementById('p_en_name').value = p.en_name;
            document.getElementById('p_gu_name').value = p.gu_name;
            document.getElementById('p_en_category').value = p.en_category;
            document.getElementById('p_gu_category').value = p.gu_category;
            document.getElementById('p_category_id').value = p.category_id;
            document.getElementById('p_price').value = p.price;
            document.getElementById('p_weight').value = p.weight;
            document.getElementById('p_img').value = p.img;
            
            var modal = new bootstrap.Modal(document.getElementById('productModal'));
            modal.show();
        }
    </script>
</body>
</html>
