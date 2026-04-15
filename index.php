<?php
$pageTitle = 'Home - MyStore';
require_once 'includes/header.php';
require_once 'classes/Product.php';

$product = new Product();
$featuredProducts = $product->getFeatured(8);
$newArrivals = $product->getNewArrivals(4);
?>

<!-- Hero Section -->
<section class="hero">
    <h1>Elevate Your Everyday</h1>
    <p>Discover handpicked premium products with unmatched quality and style.</p>
    <a href="pages/products.php" class="hero-btn">Explore Collection →</a>
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
    <a href="pages/products.php" class="view-all">View All →</a>
</div>

<div class="categories">
    <a href="pages/products.php?category=electronics" class="category-card">
        <div class="category-icon">💻</div>
        <h3>Electronics</h3>
    </a>
    <a href="pages/products.php?category=fashion" class="category-card">
        <div class="category-icon">👕</div>
        <h3>Fashion</h3>
    </a>
    <a href="pages/products.php?category=home" class="category-card">
        <div class="category-icon">🏠</div>
        <h3>Home & Living</h3>
    </a>
    <a href="pages/products.php?category=sports" class="category-card">
        <div class="category-icon">⚽</div>
        <h3>Sports</h3>
    </a>
</div>

<!-- Featured Products Carousel -->
<div class="section-header">
    <h2>⭐ Featured Products</h2>
    <a href="pages/products.php?featured=1" class="view-all">View All →</a>
</div>

<div class="product-carousel-container">
    <div class="carousel-arrow carousel-arrow-left" id="featuredLeftArrow">
        <i class="fas fa-chevron-left"></i>
    </div>
    <div class="product-carousel" id="featuredCarousel">
        <?php foreach ($featuredProducts as $p): ?>
            <div class="product-card">
                <?php
                $images = json_decode($p['images'] ?? '[]', true);
                $firstImage = !empty($images) ? $images[0] : '/E-Commers-Website/assets/images/placeholder.jpg';
                ?>
                <img src="<?= htmlspecialchars($firstImage) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                <div class="product-info">
                    <span class="product-category"><?= htmlspecialchars($p['category_name'] ?? 'General') ?></span>
                    <a href="pages/product-detail.php?slug=<?= $p['slug'] ?>" class="product-name">
                        <?= htmlspecialchars($p['name']) ?>
                    </a>
                    <div class="product-price">
                        <span class="price">$<?= number_format($p['price'], 2) ?></span>
                        <?php if ($p['sale_price']): ?>
                            <span class="old-price">$<?= number_format($p['price'], 2) ?></span>
                        <?php endif; ?>
                    </div>
                    <button class="add-to-cart" data-product-id="<?= $p['id'] ?>">
                        🛒 Add to Cart
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="carousel-arrow carousel-arrow-right" id="featuredRightArrow">
        <i class="fas fa-chevron-right"></i>
    </div>
</div>

<!-- New Arrivals Grid -->
<!-- New Arrivals Carousel -->
<div class="section-header">
    <h2>🆕 New Arrivals</h2>
    <a href="pages/products.php?sort=newest" class="view-all">View All →</a>
</div>

<div class="product-carousel-container">
    <div class="carousel-arrow carousel-arrow-left" id="newLeftArrow">
        <i class="fas fa-chevron-left"></i>
    </div>
    <div class="product-carousel" id="newArrivalsCarousel">
        <?php foreach ($newArrivals as $p): ?>
            <div class="product-card">
                <?php
                $images = json_decode($p['images'] ?? '[]', true);
                $firstImage = !empty($images) ? $images[0] : '/E-Commers-Website/assets/images/placeholder.jpg';
                ?>
                <img src="<?= htmlspecialchars($firstImage) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                <div class="product-info">
                    <span class="product-category"><?= htmlspecialchars($p['category_name'] ?? 'General') ?></span>
                    <a href="pages/product-detail.php?slug=<?= $p['slug'] ?>" class="product-name">
                        <?= htmlspecialchars($p['name']) ?>
                    </a>
                    <div class="product-price">
                        <span class="price">$<?= number_format($p['price'], 2) ?></span>
                        <?php if ($p['sale_price']): ?>
                            <span class="old-price">$<?= number_format($p['sale_price'], 2) ?></span>
                        <?php endif; ?>
                    </div>
                    <button class="add-to-cart" data-product-id="<?= $p['id'] ?>">
                        🛒 Add to Cart
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="carousel-arrow carousel-arrow-right" id="newRightArrow">
        <i class="fas fa-chevron-right"></i>
    </div>
</div>

<script>
    (function() {
        // Carousel functionality
        function initCarousel(carouselId, leftArrowId, rightArrowId) {
            const carousel = document.getElementById(carouselId);
            const leftArrow = document.getElementById(leftArrowId);
            const rightArrow = document.getElementById(rightArrowId);
            if (!carousel || !leftArrow || !rightArrow) return;

            const scrollAmount = 300;

            leftArrow.addEventListener('click', () => {
                carousel.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            });
            rightArrow.addEventListener('click', () => {
                carousel.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });

            const checkScrollable = () => {
                const isScrollable = carousel.scrollWidth > carousel.clientWidth;
                leftArrow.style.opacity = isScrollable ? '1' : '0.5';
                rightArrow.style.opacity = isScrollable ? '1' : '0.5';
                leftArrow.style.pointerEvents = isScrollable ? 'auto' : 'none';
                rightArrow.style.pointerEvents = isScrollable ? 'auto' : 'none';
            };
            checkScrollable();
            window.addEventListener('resize', checkScrollable);
            carousel.addEventListener('scroll', checkScrollable);
        }
        initCarousel('featuredCarousel', 'featuredLeftArrow', 'featuredRightArrow');
        initCarousel('newArrivalsCarousel', 'newLeftArrow', 'newRightArrow');
    })();

    (function() {
        if (window.__cartListenerAdded) return;
        window.__cartListenerAdded = true;

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
            toast.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
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

        document.addEventListener('click', async function(e) {
            const addBtn = e.target.closest('.add-to-cart');
            if (!addBtn) return;
            e.preventDefault();
            if (addBtn.disabled) return;

            const productId = addBtn.dataset.productId;
            if (!productId) return;

            const originalHtml = addBtn.innerHTML;
            addBtn.disabled = true;
            addBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adding...';

            try {
                const response = await fetch('/E-Commers-Website/api/cart/add.php', {
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

                if (response.status === 401) {
                    showToast('Please log in to add items to cart', 'danger');
                    setTimeout(() => window.location.href = '/E-Commers-Website/login.php', 1500);
                    return;
                }
                if (result.success) {
                    showToast(result.message || 'Item added to cart!', 'success');
                    updateCartCount(result.cart_count);
                } else {
                    showToast(result.message || 'Could not add item', 'danger');
                }
            } catch (error) {
                showToast('Something went wrong. Please try again.', 'danger');
            } finally {
                if (addBtn.disabled) {
                    addBtn.disabled = false;
                    addBtn.innerHTML = originalHtml;
                }
            }
        });
    })();
</script>

<?php require_once 'includes/footer.php'; ?>