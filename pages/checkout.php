<?php
require_once '../classes/User.php';
$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: /E-Commers-Website/login.php');
    exit;
}

require_once '../includes/header.php';
require_once '../classes/Cart.php';

$cart = new Cart();
$cartItems = $cart->getItems();
$subtotal = $cart->subtotal();

if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

$currentUser = $user->getCurrentUser();
$userData = $user->getById($currentUser['id']);
$shipping = [
    'name'    => $currentUser['name'] ?? '',
    'email'   => $currentUser['email'] ?? '',
    'phone'   => $userData['phone'] ?? '',
    'address' => $userData['address'] ?? '',
    'city'    => $userData['city'] ?? '',
    'country' => $userData['country'] ?? '',
    'postal'  => $userData['postal_code'] ?? '',
    'notes'   => ''
];
?>

<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <h1 class="display-5 fw-bold mb-0">
            <i class="fas fa-credit-card me-3 text-primary"></i>Checkout
        </h1>
        <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill ms-3">
            <?= count($cartItems) ?> item<?= count($cartItems) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <div class="row g-4">
        <!-- Shipping Form -->
        <div class="col-lg-8">
            <div class="glass-card rounded-4 p-4">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">
                    <i class="fas fa-truck me-2"></i>Shipping Information
                </h5>
                <form id="checkoutForm" method="POST" action="/E-Commers-Website/api/orders/create.php">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="name" class="form-label fw-semibold">Full Name *</label>
                            <input type="text" class="form-control form-control-lg rounded-pill" id="name" name="name"
                                value="<?= htmlspecialchars($shipping['name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email *</label>
                            <input type="email" class="form-control form-control-lg rounded-pill" id="email" name="email"
                                value="<?= htmlspecialchars($shipping['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Phone *</label>
                            <input type="tel" class="form-control form-control-lg rounded-pill" id="phone" name="phone"
                                value="<?= htmlspecialchars($shipping['phone']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label fw-semibold">Address *</label>
                            <input type="text" class="form-control form-control-lg rounded-pill" id="address" name="address"
                                value="<?= htmlspecialchars($shipping['address']) ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label for="city" class="form-label fw-semibold">City *</label>
                            <input type="text" class="form-control form-control-lg rounded-pill" id="city" name="city"
                                value="<?= htmlspecialchars($shipping['city']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="country" class="form-label fw-semibold">Country *</label>
                            <select class="form-select form-select-lg rounded-pill" id="country" name="country" required>
                                <option value="">Select...</option>
                                <option value="Pakistan" <?= $shipping['country'] == 'Pakistan' ? 'selected' : '' ?>>Pakistan</option>
                                <option value="USA">USA</option>
                                <option value="UK">UK</option>
                                <option value="Canada">Canada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="postal" class="form-label fw-semibold">Postal Code *</label>
                            <input type="text" class="form-control form-control-lg rounded-pill" id="postal" name="postal"
                                value="<?= htmlspecialchars($shipping['postal']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label fw-semibold">Order Notes (Optional)</label>
                            <textarea class="form-control rounded-4" id="notes" name="notes" rows="3"
                                placeholder="Special delivery instructions..."></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="total" value="<?= $subtotal ?>">
                </form>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="glass-card rounded-4 p-4 sticky-top" style="top: 90px;">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">
                    <i class="fas fa-receipt me-2"></i>Order Summary
                </h5>

                <!-- Cart Items Preview -->
                <div class="mb-3" style="max-height: 300px; overflow-y: auto;">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                            <div class="rounded-3 overflow-hidden" style="width: 50px; height: 50px;">
                                <img src="<?= htmlspecialchars($item['image']) ?>"
                                    alt="<?= htmlspecialchars($item['name']) ?>"
                                    class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="small text-muted">Qty: <?= $item['quantity'] ?></div>
                            </div>
                            <div class="fw-semibold">$<?= number_format($item['subtotal'], 2) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-semibold" id="cart-subtotal">$<?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span class="text-success fw-semibold">Free</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Tax</span>
                    <span class="text-muted">Calculated at checkout</span>
                </div>
                <hr class="my-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold fs-5">Total</span>
                    <span class="fw-bold fs-4" id="cart-total">$<?= number_format($subtotal, 2) ?></span>
                </div>

                <!-- Payment Method -->
                <div class="mb-4">
                    <label class="form-label fw-bold mb-3">Payment Method</label>
                    <div class="glass-card rounded-4 p-3 border border-success border-opacity-25">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" checked disabled>
                            <label class="form-check-label fw-semibold" for="cod">
                                <i class="fas fa-money-bill-wave text-success me-2"></i>Cash on Delivery
                            </label>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle me-1"></i>Demo project – no actual payment processed.
                        </small>
                    </div>
                </div>

                <button type="submit" form="checkoutForm" class="btn btn-primary w-100 py-3 rounded-pill fw-semibold btn-lg">
                    <i class="fas fa-lock me-2"></i>Place Order
                </button>
                <a href="cart.php" class="btn btn-outline-secondary w-100 mt-3 rounded-pill py-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Cart
                </a>
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>Secure checkout
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
        const formData = new FormData(this);
        fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/E-Commers-Website/pages/order-confirmation.php?order_id=' + data.order_id;
                } else {
                    alert('Error: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                alert('An error occurred.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    });
</script>

<?php require_once '../includes/footer.php'; ?>