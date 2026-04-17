<?php
require_once 'auth_check.php';
require_once '../classes/Order.php';

$orderId = $_GET['id'] ?? 0;
$orderObj = new Order();
$order = $orderObj->getById($orderId);

if (!$order) {
    header('Location: /E-Commers-Website/admin/orders.php');
    exit;
}

$pageTitle = 'Order #' . $order['order_number'];
require_once 'header.php'; // Admin header
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Order #<?= htmlspecialchars($order['order_number']) ?></h2>
    <a href="/E-Commers-Website/admin/orders.php" class="btn-admin-outline text-decoration-none">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>

<div class="row g-4">
    <!-- Order Items -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <i class="fas fa-box me-2"></i>Order Items
            </div>
            <div class="admin-card-body p-0">
                <table class="admin-table">
                    <thead>
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
                                <td class="fw-semibold"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>$<?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($order['items'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No items found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="fw-bold">$<?= number_format($order['total_amount'], 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <!-- Customer Information -->
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <i class="fas fa-user me-2"></i>Customer Information
            </div>
            <div class="admin-card-body">
                <p class="fw-semibold mb-2"><?= htmlspecialchars($order['shipping_name']) ?></p>
                <p class="mb-1 text-muted">
                    <i class="fas fa-envelope me-2 fa-fw"></i><?= htmlspecialchars($order['shipping_email']) ?>
                </p>
                <p class="mb-1 text-muted">
                    <i class="fas fa-phone me-2 fa-fw"></i><?= htmlspecialchars($order['shipping_phone']) ?>
                </p>
                <hr class="my-3">
                <p class="mb-0 fw-semibold">Shipping Address</p>
                <p class="text-muted">
                    <?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br>
                    <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_country']) ?> <?= htmlspecialchars($order['shipping_postal']) ?>
                </p>
            </div>
        </div>

        <!-- Order Information -->
        <div class="admin-card">
            <div class="admin-card-header">
                <i class="fas fa-info-circle me-2"></i>Order Information
            </div>
            <div class="admin-card-body">
                <p class="mb-2">
                    <i class="fas fa-calendar me-2 fa-fw text-muted"></i>
                    <strong>Date:</strong> <?= date('F j, Y g:i A', strtotime($order['created_at'])) ?>
                </p>
                <p class="mb-2">
                    <i class="fas fa-tag me-2 fa-fw text-muted"></i>
                    <strong>Status:</strong>
                    <?php
                    $status = $order['status'];
                    $badgeClass = match ($status) {
                        'completed' => 'green',
                        'cancelled' => 'red',
                        'processing' => 'orange',
                        default => 'orange'
                    };
                    ?>
                    <span class="badge-admin <?= $badgeClass ?> ms-2"><?= ucfirst($status) ?></span>
                </p>
                <?php if (!empty($order['notes'])): ?>
                    <hr class="my-3">
                    <p class="mb-0 fw-semibold">Order Notes</p>
                    <p class="text-muted fst-italic">"<?= nl2br(htmlspecialchars($order['notes'])) ?>"</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>