<?php
// Start session on every page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current user if logged in
$currentUser = null;
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    $currentUser = [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'is_admin' => $_SESSION['is_admin']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'E-Commerce Store'; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        /* Header Styles */
        .header {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3B82F6;
            text-decoration: none;
        }

        .nav-menu {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-menu a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-menu a:hover {
            color: #3B82F6;
        }

        /* User Menu */
        .user-menu {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn {
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #3B82F6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563EB;
        }

        .btn-outline {
            background: transparent;
            color: #3B82F6;
            border: 2px solid #3B82F6;
        }

        .btn-outline:hover {
            background: #3B82F6;
            color: white;
        }

        .user-dropdown {
            position: relative;
        }

        .user-name {
            cursor: pointer;
            padding: 8px 15px;
            background: #f0f0f0;
            border-radius: 5px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            min-width: 150px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            margin-top: 5px;
        }

        .dropdown-content a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
        }

        .dropdown-content a:hover {
            background: #f5f5f5;
        }

        .user-dropdown:hover .dropdown-content {
            display: block;
        }

        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            min-height: 500px;
        }

        /* Footer */
        .footer {
            background: #1F2937;
            color: white;
            padding: 40px 20px;
            margin-top: 50px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .footer-section h3 {
            margin-bottom: 15px;
            color: #3B82F6;
        }

        .footer-section a {
            color: #ccc;
            text-decoration: none;
            display: block;
            margin: 8px 0;
        }

        .footer-section a:hover {
            color: white;
        }

        /* Alert Messages */
        .alert {
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">🛒 MyStore</a>

            <nav class="nav-menu">
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <a href="cart.php">Cart (0)</a>
            </nav>

            <div class="user-menu">
                <?php if ($currentUser): ?>
                    <div class="user-dropdown">
                        <span class="user-name">👤 <?php echo htmlspecialchars($currentUser['name']); ?></span>
                        <div class="dropdown-content">
                            <a href="profile.php">My Profile</a>
                            <a href="orders.php">My Orders</a>
                            <?php if ($currentUser['is_admin']): ?>
                                <a href="admin/dashboard.php">Admin Panel</a>
                            <?php endif; ?>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline">Login</a>
                    <a href="register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="main-content">