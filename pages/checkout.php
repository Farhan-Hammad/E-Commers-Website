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
$shipping = [
    'name' => $currentUser['name'] ?? '',
    'email' => $currentUser['email'] ?? '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'country' => '',
    'postal' => '',
    'notes' => ''
];
?>

<div class="container py-5">
    <h1 class="mb-4">Checkout</h1>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <form id="checkoutForm" method="POST" action="/E-Commers-Website/api/orders/create.php">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?= htmlspecialchars($shipping['name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($shipping['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone *</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    value="<?= htmlspecialchars($shipping['phone']) ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label">Address *</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="<?= htmlspecialchars($shipping['address']) ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="city" name="city"
                                    value="<?= htmlspecialchars($shipping['city']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="country" class="form-label">Country *</label>
                                <select class="form-select" id="country" name="country" required>
                                    <option value="">Select...</option>
                                    <option value="Pakistan" <?= $shipping['country'] == 'Pakistan' ? 'selected' : '' ?>>Pakistan</option>
                                    <option value="USA">USA</option>
                                    <option value="UK">UK</option>
                                    <option value="Canada">Canada</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="postal" class="form-label">Postal Code *</label>
                                <input type="text" class="form-control" id="postal" name="postal"
                                    value="<?= htmlspecialchars($shipping['postal']) ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Order Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="total" value="<?= $subtotal ?>">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                            <span>$<?= number_format($item['subtotal'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold mb-2">
                        <span>Subtotal</span>
                        <span>$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-muted mb-2">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                        <span>Total</span>
                        <span>$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Method</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" checked disabled>
                            <label class="form-check-label" for="cod">
                                <i class="fas fa-money-bill-wave text-success"></i> Cash on Delivery (Demo)
                            </label>
                        </div>
                        <small class="text-muted">This is a demo project – no actual payment.</small>
                    </div>
                    <button type="submit" form="checkoutForm" class="btn btn-primary w-100 btn-lg">
                        <i class="fas fa-lock"></i> Place Order (Demo)
                    </button>
                    <a href="cart.php" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="fas fa-arrow-left"></i> Back to Cart
                    </a>
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