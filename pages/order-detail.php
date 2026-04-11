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
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Order #<?= htmlspecialchars($order['order_number']) ?></h1>
        <a href="/E-Commers-Website/orders.php" class="btn btn-outline-secondary">
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
                    <h5 class="mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <p><strong><?= htmlspecialchars($order['shipping_name']) ?></strong><br>
                        <?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br>
                        <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_country']) ?> <?= htmlspecialchars($order['shipping_postal']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['shipping_email']) ?><br>
                        <strong>Phone:</strong> <?= htmlspecialchars($order['shipping_phone']) ?>
                    </p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <p><strong>Order Date:</strong> <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
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
</div>

<?php require_once '../includes/footer.php'; ?>