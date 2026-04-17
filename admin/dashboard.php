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

// ===== CHART DATA =====
// Sales trend (last 7 days)
$salesTrend = $db->query("
    SELECT DATE(created_at) as date, SUM(total_amount) as total
    FROM orders
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date ASC
")->fetchAll(PDO::FETCH_ASSOC);

$chartDates = [];
$chartTotals = [];
foreach ($salesTrend as $row) {
    $chartDates[] = date('M d', strtotime($row['date']));
    $chartTotals[] = (float)$row['total'];
}

// Top selling products
$topProducts = $db->query("
    SELECT p.name, SUM(oi.quantity) as sold
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY p.id
    ORDER BY sold DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$topProductNames = [];
$topProductSales = [];
foreach ($topProducts as $p) {
    $topProductNames[] = $p['name'];
    $topProductSales[] = (int)$p['sold'];
}

// Order status distribution
$statusStats = $db->query("
    SELECT status, COUNT(*) as count FROM orders GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);
$statusLabels = [];
$statusCounts = [];
foreach ($statusStats as $s) {
    $statusLabels[] = ucfirst($s['status']);
    $statusCounts[] = (int)$s['count'];
}
?>

<div class="d-flex justify-content-end align-items-center mb-4">

    <div>
        <a href="/E-Commers-Website/admin/products.php" class="btn-admin me-2 text-decoration-none">
            <i class="fas fa-plus"></i> Add Product
        </a>
        <a href="/E-Commers-Website/admin/categories.php" class="btn-admin-outline text-decoration-none">
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
                    <a href="/E-Commers-Website/admin/product-form.php?id=<?= $item['id'] ?>" class="btn-admin-outline btn-sm ms-2 py-1 px-2 text-decoration-none">Update Stock</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Sales Trend Chart -->
    <div class="col-lg-8">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <i class="fas fa-chart-line me-2"></i>Sales Trend (Last 7 Days)
            </div>
            <div class="admin-card-body">
                <canvas id="salesChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
    <!-- Order Status Distribution -->
    <div class="col-lg-4">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <i class="fas fa-chart-pie me-2"></i>Order Status
            </div>
            <div class="admin-card-body">
                <canvas id="statusChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Products & Recent Orders -->
<div class="row g-4">
    <!-- Top Selling Products -->
    <div class="col-lg-5">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <i class="fas fa-trophy me-2" style="color: #fbbf24;"></i>Top Selling Products
            </div>
            <div class="admin-card-body">
                <canvas id="topProductsChart" style="height: 220px;"></canvas>
            </div>
        </div>
    </div>
    <!-- Recent Orders -->
    <div class="col-lg-7">
        <div class="admin-card h-100">
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
                            <th></th>
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
                                    <a href="/E-Commers-Website/admin/order-detail.php?id=<?= $order['id'] ?>" class="btn-admin-outline btn-sm text-decoration-none">
                                        <i class="fas fa-eye"></i>
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
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chartDates) ?>,
                datasets: [{
                    label: 'Revenue ($)',
                    data: <?= json_encode($chartTotals) ?>,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.05)',
                    borderWidth: 3,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#f9fafb',
                        bodyColor: '#d1d5db',
                        borderColor: '#f97316',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            callback: (v) => '$' + v
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Order Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($statusLabels) ?>,
                datasets: [{
                    data: <?= json_encode($statusCounts) ?>,
                    backgroundColor: ['#f97316', '#3b82f6', '#10b981', '#dc2626', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#1f2937',
                            font: {
                                weight: '500'
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Top Products Chart
        const topCtx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(topCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($topProductNames) ?>,
                datasets: [{
                    label: 'Units Sold',
                    data: <?= json_encode($topProductSales) ?>,
                    backgroundColor: '#f97316',
                    borderRadius: 8,
                    barPercentage: 0.6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>

<?php require_once 'footer.php'; ?>