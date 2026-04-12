<?php
require_once 'auth_check.php';
require_once '../classes/Order.php';
require_once '../includes/header.php';

$orderId = $_GET['id'] ?? 0;
$orderObj = new Order();
$order = $orderObj->getById($orderId);

if (!$order) {
    header('Location: /E-Commers-Website/admin/orders.php');
    exit;
}
?>
<!-- rest of file unchanged (no db call needed here) -->

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-md-block bg-light sidebar" style="min-height: 100vh;">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="/E-Commers-Website/admin/dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/E-Commers-Website/admin/products.php">
                            <i class="fas fa-box"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/E-Commers-Website/admin/orders.php">
                            <i class="fas fa-shopping-cart"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="/E-Commers-Website/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">Order #<?= htmlspecialchars($order['order_number']) ?></h1>
                <a href="/E-Commers-Website/admin/orders.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                                            <td>$<?= number_format($item['price'], 2) ?></td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td>$<?= number_format($item['subtotal'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th>$<?= number_format($order['total_amount'], 2) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong><?= htmlspecialchars($order['shipping_name']) ?></strong><br>
                                <?= htmlspecialchars($order['shipping_email']) ?><br>
                                <?= htmlspecialchars($order['shipping_phone']) ?></p>
                            <p><strong>Shipping Address:</strong><br>
                                <?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br>
                                <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_country']) ?> <?= htmlspecialchars($order['shipping_postal']) ?></p>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Order Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Date:</strong> <?= date('F j, Y g:i A', strtotime($order['created_at'])) ?></p>
                            <p><strong>Status:</strong>
                                <span class="badge bg-<?=
                                                        $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning')
                                                        ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </p>
                            <?php if (!empty($order['notes'])): ?>
                                <p><strong>Notes:</strong> <?= nl2br(htmlspecialchars($order['notes'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>