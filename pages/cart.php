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
$isEmpty = $cart->isEmpty();
?>

<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="display-5 fw-bold mb-0">
            <i class="fas fa-shopping-bag me-3 text-primary"></i>Shopping Cart
        </h1>
        <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill fs-6">
            <?= count($cartItems) ?> item<?= count($cartItems) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <?php if ($isEmpty): ?>
        <div class="glass-card rounded-4 p-5 text-center">
            <div class="py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4 opacity-50"></i>
                <h3 class="mb-3">Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added anything yet.</p>
                <a href="products.php" class="btn btn-primary btn-lg px-5 rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i>Start Shopping
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Cart Items Section -->
            <div class="col-lg-8">
                <div class="glass-card rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="border-bottom">
                                <tr>
                                    <th class="ps-4 py-3 text-muted fw-semibold">Product</th>
                                    <th class="py-3 text-muted fw-semibold">Price</th>
                                    <th class="py-3 text-muted fw-semibold text-center">Quantity</th>
                                    <th class="py-3 text-muted fw-semibold">Total</th>
                                    <th class="pe-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr class="border-bottom" data-product-id="<?= $item['product_id'] ?>">
                                        <td class="ps-4 py-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="cart-item-image rounded-3 overflow-hidden" style="width: 80px; height: 80px;">
                                                    <img src="<?= htmlspecialchars($item['image']) ?>"
                                                        alt="<?= htmlspecialchars($item['name']) ?>"
                                                        class="w-100 h-100 object-fit-cover">
                                                </div>
                                                <div>
                                                    <a href="product-detail.php?slug=<?= urlencode($item['slug']) ?>"
                                                        class="fw-semibold text-decoration-none fs-5">
                                                        <?= htmlspecialchars($item['name']) ?>
                                                    </a>
                                                    <div class="text-muted small mt-1">
                                                        SKU: <?= htmlspecialchars($item['product_id']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 fw-semibold">$<?= number_format($item['price'], 2) ?></td>
                                        <td class="py-4">
                                            <div class="quantity-control">
                                                <button class="cart-qty-btn" data-action="decrease">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number"
                                                    class="cart-qty-input"
                                                    value="<?= $item['quantity'] ?>"
                                                    min="1"
                                                    max="<?= $item['stock'] ?>"
                                                    data-product-id="<?= $item['product_id'] ?>"
                                                    inputmode="numeric"
                                                    pattern="[0-9]*">
                                                <button class="cart-qty-btn" data-action="increase">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="py-4 fw-bold fs-5 item-subtotal">$<?= number_format($item['subtotal'], 2) ?></td>
                                        <td class="pe-4 py-4">
                                            <button class="btn btn-outline-danger btn-sm rounded-circle cart-remove"
                                                style="width: 36px; height: 36px;">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 d-flex">
                    <a href="products.php" class="btn btn-outline-primary rounded-pill px-4 py-2">
                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="glass-card rounded-4 p-4 sticky-top" style="top: 90px;">
                    <h5 class="fw-bold mb-4 pb-2 border-bottom">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-semibold" id="cart-subtotal">$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Shipping</span>
                        <span class="text-success">Free</span>
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
                    <a href="checkout.php" class="btn btn-primary w-100 py-3 rounded-pill fw-semibold">
                        <i class="fas fa-lock me-2"></i>Proceed to Checkout
                    </a>
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>Secure checkout
                        </small>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        async function updateCartItem(productId, quantity) {
            try {
                const response = await fetch('/E-Commers-Website/api/cart/update.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                });
                const result = await response.json();
                if (result.success) {
                    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                    const price = parseFloat(row.querySelector('td:nth-child(2)').textContent.replace('$', ''));
                    row.querySelector('.item-subtotal').textContent = '$' + (price * quantity).toFixed(2);
                    updateCartTotals();
                    updateHeaderCartCount();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Update failed');
            }
        }

        async function updateCartTotals() {
            const response = await fetch('/E-Commers-Website/api/cart/get.php');
            const data = await response.json();
            document.getElementById('cart-subtotal').textContent = '$' + data.subtotal.toFixed(2);
            document.getElementById('cart-total').textContent = '$' + data.total.toFixed(2);
        }

        function updateHeaderCartCount() {
            fetch('/E-Commers-Website/api/cart/get.php')
                .then(res => res.json())
                .then(data => {
                    const badge = document.querySelector('.cart-count');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                    }
                });
        }

        // Quantity controls
        document.querySelectorAll('.cart-qty-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const row = this.closest('tr');
                const input = row.querySelector('.cart-qty-input');
                let newQty = parseInt(input.value);
                if (this.dataset.action === 'increase') {
                    newQty = Math.min(newQty + 1, parseInt(input.max));
                } else {
                    newQty = Math.max(newQty - 1, 1);
                }
                input.value = newQty;
                await updateCartItem(row.dataset.productId, newQty);
            });
        });

        document.querySelectorAll('.cart-qty-input').forEach(input => {
            // Handle manual typing (blur event)
            input.addEventListener('blur', async function() {
                const row = this.closest('tr');
                let qty = parseInt(this.value);
                const max = parseInt(this.max);

                // Validate
                if (isNaN(qty) || qty < 1) qty = 1;
                if (qty > max) qty = max;

                this.value = qty;
                await updateCartItem(row.dataset.productId, qty);
            });

            // Handle Enter key
            input.addEventListener('keypress', async function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.blur();
                }
            });
        });

        // Remove item
        document.querySelectorAll('.cart-remove').forEach(btn => {
            btn.addEventListener('click', async function() {
                const row = this.closest('tr');
                const productId = row.dataset.productId;
                if (!confirm('Remove this item from your cart?')) return;
                try {
                    const response = await fetch('/E-Commers-Website/api/cart/remove.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId
                        })
                    });
                    const result = await response.json();
                    if (result.success) {
                        row.remove();
                        updateCartTotals();
                        updateHeaderCartCount();
                        if (document.querySelectorAll('tbody tr').length === 0) location.reload();
                    }
                } catch (error) {
                    alert('Error removing item');
                }
            });
        });

        // Initial header cart count sync
        updateHeaderCartCount();
    });
</script>

<?php require_once '../includes/footer.php'; ?>