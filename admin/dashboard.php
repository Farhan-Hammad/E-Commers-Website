<?php
require_once 'auth_check.php';
require_once '../includes/header.php';

$db = db();

// Stats
$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pendingOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

// Low stock products (less than 5)
$lowStock = $db->query("
    SELECT id, name, stock_quantity 
    FROM products 
    WHERE stock_quantity < 5 AND status = 'active'
    ORDER BY stock_quantity ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Recent orders
$recentOrders = $db->query("
    SELECT o.*, u.first_name, u.last_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar (same as before, add Categories link) -->
        <nav class="col-md-2 d-md-block bg-light sidebar" style="min-height: 100vh;">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link active" href="/E-Commers-Website/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-Commers-Website/admin/products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-Commers-Website/admin/categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-Commers-Website/admin/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="/E-Commers-Website/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">Dashboard</h1>
                <div>
                    <a href="/E-Commers-Website/admin/products.php" class="btn btn-primary me-2"><i class="fas fa-plus"></i> Add Product</a>
                    <a href="/E-Commers-Website/admin/categories.php" class="btn btn-outline-primary"><i class="fas fa-tags"></i> Manage Categories</a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Orders</h5>
                            <p class="card-text display-6"><?= $totalOrders ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Products</h5>
                            <p class="card-text display-6"><?= $totalProducts ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Users</h5>
                            <p class="card-text display-6"><?= $totalUsers ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Pending Orders</h5>
                            <p class="card-text display-6"><?= $pendingOrders ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <?php if (!empty($lowStock)): ?>
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h5>
                    <div class="row">
                        <?php foreach ($lowStock as $item): ?>
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-danger"><?= $item['stock_quantity'] ?> left</span>
                                <?= htmlspecialchars($item['name']) ?>
                                <a href="/E-Commers-Website/admin/product-form.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-warning ms-2">Update Stock</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Recent Orders Table (same as before) -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Orders</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['order_number']) ?></td>
                                    <td><?= htmlspecialchars($order['shipping_name']) ?></td>
                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                    <td><span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?>"><?= ucfirst($order['status']) ?></span></td>
                                    <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                    <td><a href="/E-Commers-Website/admin/order-detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>