<?php
// admin/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/auth_check.php'; // Already handles admin check

$user = new User();
$currentUser = $user->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - MyStore</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Admin CSS (Orange/Red theme) -->
    <link rel="stylesheet" href="/E-Commers-Website/assets/css/admin.css">
</head>

<body class="admin-panel">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block admin-sidebar p-0" style="min-height: 100vh;">
                <div class="position-sticky pt-4">
                    <!-- Brand -->
                    <div class="px-4 mb-4">
                        <h4 class="text-white fw-bold">
                            <i class="fas fa-store-alt me-2" style="color: var(--admin-primary);"></i>
                            MyStore<span style="color: var(--admin-primary);">Admin</span>
                        </h4>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"
                                href="/E-Commers-Website/admin/dashboard.php">
                                <i class="fas fa-tachometer-alt fa-fw"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>"
                                href="/E-Commers-Website/admin/products.php">
                                <i class="fas fa-box fa-fw"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>"
                                href="/E-Commers-Website/admin/categories.php">
                                <i class="fas fa-tags fa-fw"></i> Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>"
                                href="/E-Commers-Website/admin/orders.php">
                                <i class="fas fa-shopping-cart fa-fw"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="nav-link" href="/E-Commers-Website/index.php" target="_blank">
                                <i class="fas fa-external-link-alt fa-fw"></i> View Store
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="/E-Commers-Website/logout.php">
                                <i class="fas fa-sign-out-alt fa-fw"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <!-- Top Bar -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-0" style="color: var(--admin-text);">
                            <?= $pageTitle ?? 'Dashboard' ?>
                        </h2>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted">
                            <i class="fas fa-user-circle me-1"></i>
                            <?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?>
                        </span>
                    </div>
                </div>