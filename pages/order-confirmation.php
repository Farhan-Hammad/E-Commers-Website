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

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Animation -->
            <div class="text-center mb-4">
                <div class="success-animation mb-4">
                    <div class="checkmark-circle">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                </div>
                <h1 class="display-4 fw-bold mb-3">Thank You!</h1>
                <p class="lead text-muted">Your order has been placed successfully.</p>
                <div class="d-flex justify-content-center gap-3 mb-4">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill">
                        <i class="fas fa-hashtag me-1"></i> <?= htmlspecialchars($order['order_number']) ?>
                    </span>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-4 py-2 rounded-pill">
                        <i class="fas fa-clock me-1"></i> Pending
                    </span>
                </div>
            </div>

            <!-- Order Details Card -->
            <div class="glass-card rounded-4 p-4 p-md-5 mb-4">
                <h4 class="fw-bold mb-4 pb-2 border-bottom">
                    <i class="fas fa-receipt me-2"></i>Order Details
                </h4>

                <div class="row g-4">
                    <div class="col-md-6">
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
                            <div class="icon-circle bg-success bg-opacity-10 text-success me-3">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Amount</small>
                                <span class="fw-bold fs-5">$<?= number_format($order['total_amount'], 2) ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-info bg-opacity-10 text-info me-3">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Estimated Delivery</small>
                                <span class="fw-semibold">3-5 Business Days</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>Shipping Address
                        </h6>
                        <div class="glass-card rounded-3 p-3">
                            <p class="mb-1 fw-semibold"><?= htmlspecialchars($order['shipping_name']) ?></p>
                            <p class="mb-1 text-muted"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                            <p class="mb-0 text-muted">
                                <?= htmlspecialchars($order['shipping_city']) ?>,
                                <?= htmlspecialchars($order['shipping_country']) ?>
                                <?= htmlspecialchars($order['shipping_postal']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items Summary -->
            <div class="glass-card rounded-4 p-4 p-md-5 mb-4">
                <h5 class="fw-bold mb-4">
                    <i class="fas fa-box me-2"></i>Items Ordered
                </h5>
                <div class="table-responsive">
                    <table class="table align-middle">
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
                                            <div class="rounded-3 overflow-hidden" style="width: 50px; height: 50px;">
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

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="/E-Commers-Website/pages/products.php" class="btn btn-primary rounded-pill px-5 py-3">
                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/E-Commers-Website/orders.php" class="btn btn-outline-primary rounded-pill px-5 py-3">
                        <i class="fas fa-list me-2"></i>View My Orders
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    /* Success animation */
    .success-animation {
        display: flex;
        justify-content: center;
    }

    .checkmark-circle {
        width: 100px;
        height: 100px;
        background: var(--success);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        animation: scaleIn 0.5s ease-out;
    }

    .checkmark-circle i {
        font-size: 60px;
        color: white;
    }

    @keyframes scaleIn {
        0% {
            transform: scale(0);
            opacity: 0;
        }

        80% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Icon circle */
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