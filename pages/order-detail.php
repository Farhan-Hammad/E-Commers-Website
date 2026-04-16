<?php
require_once '../includes/header.php';
require_once '../classes/Order.php';
require_once '../classes/User.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: /E-Commers-Website/login.php');
    exit;
}

$orderId = $_GET['id'] ?? 0;
$orderObj = new Order();
$order = $orderObj->getById($orderId);

// Security: Ensure order belongs to current user
if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    header('Location: /E-Commers-Website/orders.php');
    exit;
}

$status = $order['status'];
$badgeClass = match ($status) {
    'completed' => 'success',
    'cancelled' => 'danger',
    'processing' => 'info',
    default => 'warning'
};
$statusIcon = match ($status) {
    'completed' => 'check-circle',
    'cancelled' => 'times-circle',
    'processing' => 'spinner',
    default => 'clock'
};
?>

<div class="container py-5">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <a href="/E-Commers-Website/orders.php" class="text-muted text-decoration-none mb-2 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i>Back to Orders
            </a>
            <h1 class="display-6 fw-bold mb-0">
                Order #<?= htmlspecialchars($order['order_number']) ?>
            </h1>
        </div>
        <span class="badge bg-<?= $badgeClass ?> bg-opacity-10 text-<?= $badgeClass ?> px-4 py-2 rounded-pill fs-6 mt-3 mt-sm-0">
            <i class="fas fa-<?= $statusIcon ?> me-2"></i><?= ucfirst($status) ?>
        </span>
    </div>

    <div class="row g-4">
        <!-- Order Items -->
        <div class="col-lg-8">
            <div class="glass-card rounded-4 p-4">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">
                    <i class="fas fa-box me-2"></i>Order Items
                </h5>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="border-bottom">
                            <tr>
                                <th class="text-muted fw-semibold">Product</th>
                                <th class="text-muted fw-semibold text-center">Qty</th>
                                <th class="text-muted fw-semibold text-end">Price</th>
                                <th class="text-muted fw-semibold text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr class="border-bottom">
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 overflow-hidden bg-light" style="width: 50px; height: 50px;">
                                                <img src="/E-Commers-Website/assets/images/placeholder.jpg"
                                                    alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                    class="w-100 h-100 object-fit-cover">
                                            </div>
                                            <span class="fw-semibold"><?= htmlspecialchars($item['product_name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center"><?= $item['quantity'] ?></td>
                                    <td class="py-3 text-end">$<?= number_format($item['price'], 2) ?></td>
                                    <td class="py-3 text-end fw-semibold">$<?= number_format($item['subtotal'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="3" class="text-end fw-bold pt-3">Total:</td>
                                <td class="text-end fw-bold fs-5 pt-3">$<?= number_format($order['total_amount'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Shipping Information -->
            <div class="glass-card rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">
                    <i class="fas fa-map-marker-alt me-2"></i>Shipping Information
                </h5>
                <div class="mb-3">
                    <p class="fw-semibold mb-1"><?= htmlspecialchars($order['shipping_name']) ?></p>
                    <p class="text-muted mb-1"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                    <p class="text-muted mb-0">
                        <?= htmlspecialchars($order['shipping_city']) ?>,
                        <?= htmlspecialchars($order['shipping_country']) ?>
                        <?= htmlspecialchars($order['shipping_postal']) ?>
                    </p>
                </div>
                <div class="d-flex border-top pt-3 mt-2">
                    <div class="me-4">
                        <small class="text-muted d-block">Email</small>
                        <span><?= htmlspecialchars($order['shipping_email']) ?></span>
                    </div>
                    <div>
                        <small class="text-muted d-block">Phone</small>
                        <span><?= htmlspecialchars($order['shipping_phone']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="glass-card rounded-4 p-4">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">
                    <i class="fas fa-receipt me-2"></i>Order Summary
                </h5>
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Order Date</small>
                        <span class="fw-semibold"><?= date('F j, Y', strtotime($order['created_at'])) ?></span>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-circle bg-info bg-opacity-10 text-info me-3">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Total Amount</small>
                        <span class="fw-bold fs-5">$<?= number_format($order['total_amount'], 2) ?></span>
                    </div>
                </div>
                <?php if (!empty($order['notes'])): ?>
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted d-block mb-1">Order Notes</small>
                        <p class="mb-0 fst-italic">"<?= nl2br(htmlspecialchars($order['notes'])) ?>"</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    /* Icon circle for summary items */
    .icon-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
</style>

<?php require_once '../includes/footer.php'; ?>