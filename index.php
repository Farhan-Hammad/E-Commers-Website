<?php
$pageTitle = 'Home - MyStore';
require_once 'includes/header.php';
require_once 'classes/Product.php';

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

    /* Product Carousel */
    .product-carousel-container {
        position: relative;
        margin-bottom: 50px;
    }

    .product-carousel {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: none;
        /* Firefox */
        -ms-overflow-style: none;
        /* IE/Edge */
        padding: 10px 5px;
    }

    .product-carousel::-webkit-scrollbar {
        display: none;
        /* Chrome/Safari */
    }

    .carousel-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 45px;
        height: 45px;
        background: white;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        border: 1px solid #e0e0e0;
        transition: all 0.2s;
    }

    .carousel-arrow:hover {
        background: #3B82F6;
        color: white;
        border-color: #3B82F6;
    }

    .carousel-arrow-left {
        left: -20px;
    }

    .carousel-arrow-right {
        right: -20px;
    }

    @media (max-width: 768px) {
        .carousel-arrow {
            width: 35px;
            height: 35px;
        }

        .carousel-arrow-left {
            left: -10px;
        }

        .carousel-arrow-right {
            right: -10px;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero">
    <h1>🛍️ Welcome to MyStore</h1>
    <p>Discover amazing products at unbeatable prices</p>
    <a href="pages/products.php" class="hero-btn">Shop Now →</a>
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

<!-- Featured Products -->
<!-- Featured Products -->
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
            <div class="product-card" style="min-width: 250px; flex: 0 0 auto;">
                <?php
                $images = json_decode($p['images'] ?? '[]', true);
                $firstImage = !empty($images) ? $images[0] : '/E-Commers-Website/assets/images/placeholder.jpg';
                ?>
                <img src="<?= htmlspecialchars($firstImage) ?>"
                    alt="<?= htmlspecialchars($p['name']) ?>"
                    style="width: 100%; height: 200px; object-fit: cover;">
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

<!-- New Arrivals -->
<div class="section-header">
    <h2>🆕 New Arrivals</h2>
    <a href="pages/products.php?sort=newest" class="view-all">View All →</a>
</div>

<div class="product-grid">
    <?php foreach ($newArrivals as $p): ?>
        <div class="product-card">
            <?php
            $images = json_decode($p['images'] ?? '[]', true);
            $firstImage = !empty($images) ? $images[0] : '/E-Commers-Website/assets/images/placeholder.jpg';
            ?>
            <img src="<?= htmlspecialchars($firstImage) ?>"
                alt="<?= htmlspecialchars($p['name']) ?>"
                style="width: 100%; height: 200px; object-fit: cover;">
            <div class="product-info">
                <span class="product-category"><?php echo htmlspecialchars($p['category_name'] ?? 'General'); ?></span>
                <a href="pages/product-detail.php?slug=<?php echo $p['slug']; ?>" class="product-name">
                    <?php echo htmlspecialchars($p['name']); ?>
                </a>
                <div class="product-price">
                    <span class="price">$<?php echo number_format($p['price'], 2); ?></span>
                </div>
                <button class="add-to-cart" data-product-id="<?= $p['id'] ?>">
                    🛒 Add to Cart
                </button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    (function() {
        // Carousel functionality
        function initCarousel(carouselId, leftArrowId, rightArrowId) {
            const carousel = document.getElementById(carouselId);
            const leftArrow = document.getElementById(leftArrowId);
            const rightArrow = document.getElementById(rightArrowId);
            if (!carousel || !leftArrow || !rightArrow) return;

            const scrollAmount = 300; // Adjust based on card width + gap

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

            // Optional: Hide arrows if not scrollable
            const checkScrollable = () => {
                const isScrollable = carousel.scrollWidth > carousel.clientWidth;
                leftArrow.style.opacity = isScrollable ? '1' : '0.5';
                rightArrow.style.opacity = isScrollable ? '1' : '0.5';
                leftArrow.style.pointerEvents = isScrollable ? 'auto' : 'none';
                rightArrow.style.pointerEvents = isScrollable ? 'auto' : 'none';
            };

            checkScrollable();
            window.addEventListener('resize', checkScrollable);
            // Check after images load
            carousel.addEventListener('scroll', checkScrollable);
        }

        // Initialize both carousels
        initCarousel('featuredCarousel', 'featuredLeftArrow', 'featuredRightArrow');
        initCarousel('newArrivalsCarousel', 'newLeftArrow', 'newRightArrow');
    })();


    (function() {
        // Prevent multiple initializations
        if (window.__cartListenerAdded) return;
        window.__cartListenerAdded = true;

        // Toast helper
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

        // Update cart badge
        function updateCartCount(count) {
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge) {
                cartBadge.textContent = count;
                cartBadge.style.display = count > 0 ? 'inline-block' : 'none';
            }
        }

        // Global click listener using event delegation
        document.addEventListener('click', async function(e) {
            const addBtn = e.target.closest('.add-to-cart');
            if (!addBtn) return;

            e.preventDefault();

            // Don't proceed if already disabled (prevents double clicks)
            if (addBtn.disabled) return;

            const productId = addBtn.dataset.productId;
            if (!productId) return;

            // Get quantity if exists (for product detail page)
            const quantityInput = document.getElementById('quantity');
            const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;

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
                        quantity: quantity
                    })
                });
                const result = await response.json();

                if (response.status === 401) {
                    showToast('Please log in to add items to cart', 'danger');
                    setTimeout(() => window.location.href = '/E-Commers-Website/login.php', 1500);
                    // Keep button disabled during redirect
                    return;
                }

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
                // Reset button state (if not redirecting)
                if (addBtn.disabled) {
                    addBtn.disabled = false;
                    addBtn.innerHTML = originalHtml;
                }
            }
        });
    })();
</script>

<?php require_once 'includes/footer.php'; ?>