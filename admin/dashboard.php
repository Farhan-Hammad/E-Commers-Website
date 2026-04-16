<?php
require_once 'auth_check.php';
$pageTitle = 'Dashboard';
require_once 'header.php'; // Admin header

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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Dashboard</h2>
    <div>
        <a href="/E-Commers-Website/admin/products.php" class="btn-admin me-2">
            <i class="fas fa-plus"></i> Add Product
        </a>
        <a href="/E-Commers-Website/admin/categories.php" class="btn-admin-outline">
            <i class="fas fa-tags"></i> Manage Categories
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-title">Total Orders</div>
            <div class="stat-value"><?= $totalOrders ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(145deg, #3b82f6, #2563eb);">
            <div class="stat-title">Products</div>
            <div class="stat-value"><?= $totalProducts ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(145deg, #10b981, #059669);">
            <div class="stat-title">Users</div>
            <div class="stat-value"><?= $totalUsers ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card red">
            <div class="stat-title">Pending Orders</div>
            <div class="stat-value"><?= $pendingOrders ?></div>
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
<?php if (!empty($lowStock)): ?>
    <div class="alert alert-warning border-0 rounded-3 shadow-sm mb-4" style="background: rgba(249, 115, 22, 0.1); border-left: 4px solid var(--admin-primary);">
        <h5 class="fw-bold"><i class="fas fa-exclamation-triangle me-2" style="color: var(--admin-primary);"></i>Low Stock Alert</h5>
        <div class="row">
            <?php foreach ($lowStock as $item): ?>
                <div class="col-md-3 mb-2">
                    <span class="badge-admin red me-2"><?= $item['stock_quantity'] ?> left</span>
                    <?= htmlspecialchars($item['name']) ?>
                    <a href="/E-Commers-Website/admin/product-form.php?id=<?= $item['id'] ?>" class="btn-admin-outline btn-sm ms-2 py-1 px-2">Update Stock</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Recent Orders Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <i class="fas fa-clock me-2"></i>Recent Orders
    </div>
    <div class="admin-card-body p-0">
        <table class="admin-table">
            <thead>
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
                        <td><span class="fw-semibold"><?= htmlspecialchars($order['order_number']) ?></span></td>
                        <td><?= htmlspecialchars($order['shipping_name']) ?></td>
                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                        <td>
                            <?php
                            $status = $order['status'];
                            $badgeClass = match ($status) {
                                'completed' => 'green',
                                'cancelled' => 'red',
                                'processing' => 'orange',
                                default => 'orange'
                            };
                            ?>
                            <span class="badge-admin <?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                        </td>
                        <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                        <td>
                            <a href="/E-Commers-Website/admin/order-detail.php?id=<?= $order['id'] ?>" class="btn-admin-outline btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recentOrders)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No orders yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; // Admin footer 
?>