<?php
require_once 'includes/header.php';
require_once 'classes/Order.php';
require_once 'classes/User.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: /E-Commers-Website/login.php');
    exit;
}

$currentUser = $user->getCurrentUser();
$orderObj = new Order();
$orders = $orderObj->getByUser($currentUser['id']);
?>

<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <h1 class="display-5 fw-bold mb-0">
            <i class="fas fa-box me-3 text-primary"></i>My Orders
        </h1>
        <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill ms-3">
            <?= count($orders) ?> order<?= count($orders) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <?php if (empty($orders)): ?>
        <div class="glass-card rounded-4 p-5 text-center">
            <div class="py-5">
                <div class="icon-circle bg-primary bg-opacity-10 text-primary mx-auto mb-4" style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 class="mb-3">No orders yet</h3>
                <p class="text-muted mb-4">Looks like you haven't placed any orders.</p>
                <a href="/E-Commers-Website/pages/products.php" class="btn btn-primary btn-lg px-5 rounded-pill">
                    <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="glass-card rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="border-bottom">
                        <tr>
                            <th class="ps-4 py-3 text-muted fw-semibold">Order #</th>
                            <th class="py-3 text-muted fw-semibold">Date</th>
                            <th class="py-3 text-muted fw-semibold">Total</th>
                            <th class="py-3 text-muted fw-semibold">Status</th>
                            <th class="pe-4 py-3 text-muted fw-semibold text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr class="border-bottom">
                                <td class="ps-4 py-3">
                                    <span class="fw-semibold"><?= htmlspecialchars($order['order_number']) ?></span>
                                </td>
                                <td class="py-3 text-muted">
                                    <?= date('M j, Y', strtotime($order['created_at'])) ?>
                                </td>
                                <td class="py-3 fw-semibold">
                                    $<?= number_format($order['total_amount'], 2) ?>
                                </td>
                                <td class="py-3">
                                    <?php
                                    $status = $order['status'];
                                    $badgeClass = match ($status) {
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'processing' => 'info',
                                        default => 'warning'
                                    };
                                    $icon = match ($status) {
                                        'completed' => 'check-circle',
                                        'cancelled' => 'times-circle',
                                        'processing' => 'spinner',
                                        default => 'clock'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?> bg-opacity-10 text-<?= $badgeClass ?> px-3 py-2 rounded-pill">
                                        <i class="fas fa-<?= $icon ?> me-1"></i>
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <a href="/E-Commers-Website/pages/order-detail.php?id=<?= $order['id'] ?>"
                                        class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    /* Icon circle for empty state */
    .icon-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<?php require_once 'includes/footer.php'; ?>