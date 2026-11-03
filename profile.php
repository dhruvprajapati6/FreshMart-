<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

$firstName = htmlspecialchars($_SESSION['first_name'] ?? '');
$lastName = htmlspecialchars($_SESSION['last_name'] ?? '');
$email = htmlspecialchars($_SESSION['email'] ?? '');
$mobile = htmlspecialchars($_SESSION['mobile'] ?? '');
$fullName = trim($firstName . ' ' . $lastName);
$initials = strtoupper(substr($firstName, 0, 1));
if(empty($initials)) $initials = 'U';

// Fetch the latest profile photo from DB directly in case it changed
$stmt = $pdo->prepare("SELECT profile_photo, mobile FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$profilePhoto = $user['profile_photo'] ?? 'default.png';
$mobile = $user['mobile'] ?? '';

// Update session just in case
$_SESSION['profile_photo'] = $profilePhoto;
$_SESSION['mobile'] = $mobile;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FreshMart</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: var(--bg-light);
            transition: background-color 0.4s ease;
        }
        .profile-card {
            max-width: 500px;
            margin: 50px auto;
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow-lg);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.4s ease;
            animation: fadeInUp 0.6s ease-out;
        }
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-premium);
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .profile-photo-container {
            width: 120px;
            height: 120px;
            margin: 0 auto;
            position: relative;
        }
        .profile-photo {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid var(--primary-color);
            object-fit: cover;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        .camera-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--primary-color);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid var(--white);
            transition: all 0.2s;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 179, 126, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(0, 179, 126, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 179, 126, 0); }
        }
        .camera-icon:hover {
            transform: scale(1.2);
            animation: none;
        }
        .profile-section-title {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin: 25px 0 20px;
            color: var(--text-dark);
        }
        .profile-section-title i {
            color: var(--primary-color);
            font-size: 1.4rem;
        }
        .detail-row {
            margin-bottom: 20px;
            border-bottom: 1px solid var(--text-muted);
            padding-bottom: 10px;
        }
        .detail-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 5px;
        }
        .detail-value {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 1rem;
        }
        .btn-back {
            background: var(--bg-light);
            border: 1px solid var(--text-muted);
            color: var(--text-dark);
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: var(--text-dark);
            color: var(--bg-light);
        }
        .btn-logout {
            background: #dc3545;
            color: white;
            font-weight: 600;
            border: none;
        }
        .btn-logout:hover {
            background: #c82333;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="fixed-top shadow-sm">
        <div class="header-top text-center d-none d-lg-block">
            <div class="container">
                <span class="small translate-text"><i class="bi bi-truck me-2"></i> Free delivery on orders over $50 | 100% Organic Products</span>
            </div>
        </div>
        
        <nav class="navbar navbar-expand-lg py-3 navbar-glass">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="index.html">
                    <i class="bi bi-basket2-fill text-primary-custom me-2"></i>Fresh<span>Mart</span>
                </a>
                
                <!-- Mobile Toggle -->
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation & Actions -->
                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="index.html">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="shop.html">Shop</a>
                        </li>
                        
                        
                        <li class="nav-item">
                            <a class="nav-link" href="about.html">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.html">Contact</a>
                        </li>
                    </ul>
                    
                    <!-- Right Actions -->
                    <div class="d-flex align-items-center mt-3 mt-lg-0">
                        <div class="search-container d-none d-lg-block me-3">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" class="search-input" placeholder="Search products...">
                        </div>
                        
                        <a href="login.html" class="header-action-icon" title="Login/Register">
                            <i class="bi bi-person"></i>
                        </a>
                        <a href="wishlist.html" class="header-action-icon" title="Wishlist">
                            <i class="bi bi-heart"></i>
                            <span class="badge-count">0</span>
                        </a>
                        <a href="cart.html" class="header-action-icon" title="Cart">
                            <i class="bi bi-cart3"></i>
                            <span class="badge-count">0</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container" style="margin-top: 140px;">
        <div class="profile-card">
            
            <div class="text-center mb-3">
                <div class="profile-photo-container">
                    <?php if ($profilePhoto !== 'default.png'): ?>
                        <img src="uploads/<?= htmlspecialchars($profilePhoto) ?>" class="profile-photo" alt="Profile">
                    <?php else: ?>
                        <div class="profile-photo"><?= $initials ?></div>
                    <?php endif; ?>
                    
                    <div class="camera-icon" onclick="document.getElementById('photoInput').click()">
                        <i class="bi bi-camera-fill"></i>
                    </div>
                </div>
                
                <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="d-none">
                    <input type="file" name="photo" id="photoInput" accept="image/*" onchange="this.form.submit()">
                </form>
                
                <a href="#" class="d-inline-block mt-2 text-decoration-none fw-bold" style="color: #00b37e; font-size: 0.9rem;" onclick="document.getElementById('photoInput').click(); return false;">
                    <i class="bi bi-pencil-square me-1"></i> Change Photo
                </a>
            </div>

            <div class="profile-section-title">
                <i class="bi bi-person-lines-fill me-2"></i> Profile Details
            </div>

            <div class="detail-row">
                <div class="detail-label">Full Name</div>
                <div class="detail-value"><?= $fullName ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Mobile Number</div>
                <div class="detail-value">
                    <?php if(!empty($mobile)): ?>
                        <?= $mobile ?>
                    <?php else: ?>
                        <span class="text-muted fst-italic">Not added yet</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Email Address</div>
                <div class="detail-value"><?= $email ?></div>
            </div>

            <div class="d-flex justify-content-between mt-5">
                <a href="index.html" class="btn btn-back px-4 rounded-3"><i class="bi bi-arrow-left me-2"></i> Back</a>
                <a href="logout.php" class="btn btn-logout px-4 rounded-3"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
