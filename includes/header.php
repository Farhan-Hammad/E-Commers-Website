<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load required classes using absolute paths
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
    <title><?= $pageTitle ?? 'MyStore' ?> - MyStore</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/E-Commers-Website/assets/css/style.css">
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
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item mx-1">
                        <a class="nav-link <?= $currentPage == 'index.php' ? 'active' : '' ?>" href="/E-Commers-Website/index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link <?= $currentPage == 'products.php' ? 'active' : '' ?>" href="/E-Commers-Website/pages/products.php">
                            <i class="fas fa-tag"></i> Products
                        </a>
                    </li>
                    <li class="nav-item mx-1">
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
                <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
                    <!-- Theme Toggle Button -->
                    <li class="nav-item me-2">
                        <button class="btn theme-toggle" id="themeToggle" title="Toggle dark/light mode">
                            <i class="fas fa-moon"></i>
                        </button>
                    </li>

                    <!-- Cart Icon with Badge -->
                    <li class="nav-item me-2">
                        <a class="nav-link position-relative" href="/E-Commers-Website/pages/cart.php">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <span class="cart-count" style="display: <?= $cartCount > 0 ? 'inline-block' : 'none' ?>;">
                                <?= $cartCount ?>
                            </span>
                        </a>
                    </li>

                    <!-- User Dropdown -->
                    <!-- User Dropdown -->
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
                                <li><a class="dropdown-item" href="/E-Commers-Website/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                                <li><a class="dropdown-item" href="/E-Commers-Website/orders.php"><i class="fas fa-box"></i> My Orders</a></li>
                                <li><a class="dropdown-item" href="/E-Commers-Website/wishlist.php"><i class="fas fa-heart"></i> Wishlist</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <?php if ($user->isAdmin()): ?>
                                    <li><a class="dropdown-item" href="/E-Commers-Website/admin/dashboard.php"><i class="fas fa-cog"></i> Admin Dashboard</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php endif; ?>
                                <li><a class="dropdown-item text-danger" href="/E-Commers-Website/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/E-Commers-Website/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm ms-2" href="/E-Commers-Website/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main>