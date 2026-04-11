<?php
require_once '../includes/header.php';
require_once '../classes/Order.php';

$orderId = $_GET['order_id'] ?? 0;
$orderObj = new Order();
$order = $orderObj->getById($orderId);

if (!$order) {
    header('Location: /E-Commers-Website/index.php');
    exit;
}
?>

<div class="container py-5 text-center">
    <div class="mb-4">
        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
    </div>
    <h1 class="mb-3">Thank You for Your Order!</h1>
    <p class="lead">Your demo order has been placed successfully.</p>

    <div class="card shadow-sm mt-4 mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h5>Order Details</h5>
            <p><strong>Order Number:</strong> <?= htmlspecialchars($order['order_number']) ?></p>
            <p><strong>Date:</strong> <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
            <p><strong>Total:</strong> $<?= number_format($order['total_amount'], 2) ?></p>
            <p><strong>Status:</strong> <span class="badge bg-warning">Pending</span></p>
            <hr>
            <h6>Shipping To:</h6>
            <p>
                <?= htmlspecialchars($order['shipping_name']) ?><br>
                <?= htmlspecialchars($order['shipping_address']) ?><br>
                <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_country']) ?> <?= htmlspecialchars($order['shipping_postal']) ?>
            </p>
        </div>
    </div>

    <div class="mt-4">
        <a href="/E-Commers-Website/pages/products.php" class="btn btn-primary">Continue Shopping</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/E-Commers-Website/orders.php" class="btn btn-outline-secondary">View My Orders</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>