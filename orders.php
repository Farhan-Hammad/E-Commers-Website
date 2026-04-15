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
    <h1 class="mb-4">My Orders</h1>
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            You haven't placed any orders yet.
            <a href="/E-Commers-Website/pages/products.php" class="alert-link">Start shopping</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_number']) ?></td>
                            <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                            <td>$<?= number_format($order['total_amount'], 2) ?></td>
                            <td><span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?>"><?= ucfirst($order['status']) ?></span></td>
                            <td><a href="/E-Commers-Website/pages/order-detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>