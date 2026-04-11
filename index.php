<?php
$pageTitle = 'Home - MyStore';
require_once 'includes/header.php';
require_once 'classes/Product.php';
// Get featured products
$product = new Product();
$featuredProducts = $product->getFeatured(8);
$newArrivals = $product->getNewArrivals(4);
?>

<style>
    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 80px 20px;
        text-align: center;
        margin: -20px -20px 40px -20px;
    }

    .hero h1 {
        font-size: 48px;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .hero p {
        font-size: 20px;
        margin-bottom: 30px;
        opacity: 0.9;
    }

    .hero-btn {
        display: inline-block;
        padding: 15px 40px;
        background: white;
        color: #667eea;
        text-decoration: none;
        border-radius: 30px;
        font-weight: bold;
        font-size: 18px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .hero-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    /* Section Headers */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 3px solid #3B82F6;
    }

    .section-header h2 {
        font-size: 28px;
        color: #333;
    }

    .view-all {
        color: #3B82F6;
        text-decoration: none;
        font-weight: 500;
    }

    .view-all:hover {
        text-decoration: underline;
    }

    /* Product Grid */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 50px;
    }

    .product-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .product-image {
        height: 200px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
    }

    .product-info {
        padding: 20px;
    }

    .product-category {
        color: #3B82F6;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 600;
    }

    .product-name {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin: 8px 0;
        text-decoration: none;
        display: block;
    }

    .product-name:hover {
        color: #3B82F6;
    }

    .product-price {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 10px 0;
    }

    .price {
        font-size: 20px;
        font-weight: bold;
        color: #3B82F6;
    }

    .old-price {
        font-size: 16px;
        color: #999;
        text-decoration: line-through;
    }

    .add-to-cart {
        width: 100%;
        padding: 12px;
        background: #3B82F6;
        color: white;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .add-to-cart:hover {
        background: #2563EB;
    }

    /* Categories Section */
    .categories {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 50px;
    }

    .category-card {
        background: white;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        text-decoration: none;
        color: #333;
        transition: transform 0.3s;
    }

    .category-card:hover {
        transform: scale(1.05);
        color: #3B82F6;
    }

    .category-icon {
        font-size: 40px;
        margin-bottom: 10px;
    }

    /* Features */
    .features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin: 50px 0;
        padding: 40px;
        background: white;
        border-radius: 10px;
    }

    .feature {
        text-align: center;
    }

    .feature-icon {
        font-size: 40px;
        margin-bottom: 15px;
    }

    .feature h3 {
        margin-bottom: 10px;
        color: #333;
    }

    .feature p {
        color: #666;
    }
</style>

<!-- Hero Section -->
<section class="hero">
    <h1>🛍️ Welcome to MyStore</h1>
    <p>Discover amazing products at unbeatable prices</p>
    <a href="products.php" class="hero-btn">Shop Now →</a>
</section>

<!-- Features -->
<div class="features">
    <div class="feature">
        <div class="feature-icon">🚚</div>
        <h3>Free Shipping</h3>
        <p>On orders over $50</p>
    </div>
    <div class="feature">
        <div class="feature-icon">🔒</div>
        <h3>Secure Payment</h3>
        <p>100% secure checkout</p>
    </div>
    <div class="feature">
        <div class="feature-icon">↩️</div>
        <h3>Easy Returns</h3>
        <p>30-day return policy</p>
    </div>
    <div class="feature">
        <div class="feature-icon">🎧</div>
        <h3>24/7 Support</h3>
        <p>Always here to help</p>
    </div>
</div>

<!-- Categories -->
<div class="section-header">
    <h2>📂 Shop by Category</h2>
    <a href="products.php" class="view-all">View All →</a>
</div>

<div class="categories">
    <a href="products.php?category=electronics" class="category-card">
        <div class="category-icon">💻</div>
        <h3>Electronics</h3>
    </a>
    <a href="products.php?category=fashion" class="category-card">
        <div class="category-icon">👕</div>
        <h3>Fashion</h3>
    </a>
    <a href="products.php?category=home" class="category-card">
        <div class="category-icon">🏠</div>
        <h3>Home & Living</h3>
    </a>
    <a href="products.php?category=sports" class="category-card">
        <div class="category-icon">⚽</div>
        <h3>Sports</h3>
    </a>
</div>

<!-- Featured Products -->
<div class="section-header">
    <h2>⭐ Featured Products</h2>
    <a href="products.php?featured=1" class="view-all">View All →</a>
</div>

<div class="product-grid">
    <?php foreach ($featuredProducts as $p): ?>
        <div class="product-card">
            <div class="product-image">📦</div>
            <div class="product-info">
                <span class="product-category"><?php echo htmlspecialchars($p['category_name'] ?? 'General'); ?></span>
                <a href="product.php?slug=<?php echo $p['slug']; ?>" class="product-name">
                    <?php echo htmlspecialchars($p['name']); ?>
                </a>
                <div class="product-price">
                    <span class="price">$<?php echo number_format($p['price'], 2); ?></span>
                    <?php if ($p['sale_price']): ?>
                        <span class="old-price">$<?php echo number_format($p['price'], 2); ?></span>
                    <?php endif; ?>
                </div>
                <button class="add-to-cart" onclick="addToCart(<?php echo $p['id']; ?>)">
                    🛒 Add to Cart
                </button>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($featuredProducts)): ?>
        <p style="grid-column: 1/-1; text-align: center; color: #666; padding: 40px;">
            No products yet. <a href="admin/dashboard.php">Add products in admin panel</a>
        </p>
    <?php endif; ?>
</div>

<!-- New Arrivals -->
<div class="section-header">
    <h2>🆕 New Arrivals</h2>
    <a href="products.php?sort=newest" class="view-all">View All →</a>
</div>

<div class="product-grid">
    <?php foreach ($newArrivals as $p): ?>
        <div class="product-card">
            <div class="product-image">📦</div>
            <div class="product-info">
                <span class="product-category"><?php echo htmlspecialchars($p['category_name'] ?? 'General'); ?></span>
                <a href="product.php?slug=<?php echo $p['slug']; ?>" class="product-name">
                    <?php echo htmlspecialchars($p['name']); ?>
                </a>
                <div class="product-price">
                    <span class="price">$<?php echo number_format($p['price'], 2); ?></span>
                </div>
                <button class="add-to-cart" onclick="addToCart.call(this, <?php echo $p['id']; ?>)">
                    🛒 Add to Cart
                </button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    // Real cart add function (same as products.php)
    function showToast(message, type = 'success') {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        toast.style.minWidth = '250px';
        toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function updateCartCount(count) {
        const cartBadge = document.querySelector('.cart-count');
        if (cartBadge) {
            cartBadge.textContent = count;
            cartBadge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }

    async function addToCart(productId) {
        const btn = event.currentTarget;
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adding...';

        try {
            const response = await fetch('api/cart/add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            });
            const result = await response.json();

            if (result.success) {
                showToast(result.message || 'Item added to cart!', 'success');
                updateCartCount(result.cart_count);
            } else {
                showToast(result.message || 'Could not add item', 'danger');
            }
        } catch (error) {
            console.error('Cart error:', error);
            showToast('Something went wrong. Please try again.', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>