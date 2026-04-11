<?php
require_once '../includes/header.php';
require_once '../classes/Cart.php';

$cart = new Cart();
$cartItems = $cart->getItems();
$subtotal = $cart->subtotal();
$isEmpty = $cart->isEmpty();
?>

<div class="container py-5">
    <h1 class="mb-4">Shopping Cart</h1>

    <?php if ($isEmpty): ?>
        <div class="alert alert-info">
            Your cart is empty. <a href="products.php" class="alert-link">Continue shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th style="width: 120px;">Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr data-product-id="<?= $item['product_id'] ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($item['image']) ?>"
                                                    alt="<?= htmlspecialchars($item['name']) ?>"
                                                    style="width: 60px; height: 60px; object-fit: cover;"
                                                    class="me-3 rounded">
                                                <div>
                                                    <a href="product-detail.php?slug=<?= urlencode($item['slug']) ?>"
                                                        class="text-decoration-none">
                                                        <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$<?= number_format($item['price'], 2) ?></td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary cart-qty-btn" data-action="decrease">-</button>
                                                <input type="number" class="form-control text-center cart-qty-input"
                                                    value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>"
                                                    style="max-width: 60px;">
                                                <button class="btn btn-outline-secondary cart-qty-btn" data-action="increase">+</button>
                                            </div>
                                        </td>
                                        <td class="item-subtotal">$<?= number_format($item['subtotal'], 2) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger cart-remove">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="products.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-6">Subtotal</dt>
                            <dd class="col-6 text-end" id="cart-subtotal">$<?= number_format($subtotal, 2) ?></dd>

                            <dt class="col-6">Shipping</dt>
                            <dd class="col-6 text-end">Calculated at checkout</dd>

                            <dt class="col-6">Tax</dt>
                            <dd class="col-6 text-end">Calculated at checkout</dd>
                        </dl>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold mb-3">
                            <span>Total</span>
                            <span id="cart-total">$<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <a href="checkout.php" class="btn btn-primary w-100 <?= $isEmpty ? 'disabled' : '' ?>">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update quantity
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
            input.addEventListener('change', async function() {
                const row = this.closest('tr');
                let qty = parseInt(this.value);
                qty = Math.min(Math.max(qty, 1), parseInt(this.max));
                this.value = qty;
                await updateCartItem(row.dataset.productId, qty);
            });
        });

        // Remove item
        document.querySelectorAll('.cart-remove').forEach(btn => {
            btn.addEventListener('click', async function() {
                const row = this.closest('tr');
                const productId = row.dataset.productId;

                if (!confirm('Remove this item from cart?')) return;

                try {
                    const response = await fetch('../api/cart/remove.php', {
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
                        if (document.querySelectorAll('tbody tr').length === 0) {
                            location.reload();
                        }
                    }
                } catch (error) {
                    alert('Error removing item');
                }
            });
        });

        async function updateCartItem(productId, quantity) {
            try {
                const response = await fetch('../api/cart/update.php', {
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
                    const subtotal = price * quantity;
                    row.querySelector('.item-subtotal').textContent = '$' + subtotal.toFixed(2);

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
            const response = await fetch('../api/cart/get.php');
            const data = await response.json();
            document.getElementById('cart-subtotal').textContent = '$' + data.subtotal.toFixed(2);
            document.getElementById('cart-total').textContent = '$' + data.total.toFixed(2);
        }

        function updateHeaderCartCount() {
            fetch('../api/cart/get.php')
                .then(res => res.json())
                .then(data => {
                    const badge = document.querySelector('.cart-count');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                    }
                });
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>