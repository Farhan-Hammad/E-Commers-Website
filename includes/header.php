<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load required classes
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Cart.php';

$user = new User();
$cart = new Cart();
$cartCount = $cart->count();

// Get current page for active nav highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyStore - Your Online Shopping Destination</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        /* Cart badge styling */
        .cart-badge {
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            min-width: 18px;
            text-align: center;
        }

        /* User dropdown avatar */
        .user-avatar {
            width: 32px;
            height: 32px;
            background-color: #0d6efd;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand fw-bold text-primary" href="/E-Commers-Website/index.php">
                <i class="fas fa-store"></i> MyStore
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
                aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <!-- Main Navigation Links -->
                <!-- Main Navigation Links -->
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage == 'index.php' ? 'active' : '' ?>" href="/E-Commers-Website/index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage == 'products.php' ? 'active' : '' ?>" href="/E-Commers-Website/pages/products.php">
                            <i class="fas fa-tag"></i> Products
                        </a>
                    </li>
                    <!-- ... -->
                    <li class="nav-item">
                        <a class="nav-link" href="/E-Commers-Website/pages/cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                        </a>
                    </li>
                </ul>

                <!-- Search Form -->
                <form class="d-flex me-3" action="/E-Commers-Website/pages/products.php" method="GET" role="search">
                    <div class="input-group">
                        <input class="form-control" type="search" name="search" placeholder="Search products..."
                            aria-label="Search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Right Side Icons -->
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <!-- Cart Icon with Badge -->
                    <li class="nav-item me-2">
                        <a class="nav-link position-relative" href="../pages/cart.php">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <?php if ($cartCount > 0): ?>
                                <span class="cart-count"><?= $cartCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <!-- User Dropdown -->
                    <?php if ($user->isLoggedIn()): ?>
                        <?php $currentUser = $user->getCurrentUser(); ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar me-2">
                                    <?= strtoupper(substr($currentUser['first_name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <span><?= htmlspecialchars($currentUser['first_name'] ?? 'User') ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="../profile.php">
                                        <i class="fas fa-user"></i> My Profile
                                    </a></li>
                                <li><a class="dropdown-item" href="../orders.php">
                                        <i class="fas fa-box"></i> My Orders
                                    </a></li>
                                <li><a class="dropdown-item" href="../wishlist.php">
                                        <i class="fas fa-heart"></i> Wishlist
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <?php if ($user->isAdmin()): ?>
                                    <li><a class="dropdown-item" href="../admin/dashboard.php">
                                            <i class="fas fa-cog"></i> Admin Dashboard
                                        </a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php endif; ?>
                                <li><a class="dropdown-item text-danger" href="../logout.php">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../login.php">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm ms-2" href="../register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Container (closing tag in footer.php) -->
    <main>