<?php
require_once '../includes/header.php';
require_once '../classes/Product.php';

// Get slug from URL
$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: /E-Commers-Website/pages/products.php');
    exit;
}

$productObj = new Product();
$product = $productObj->getBySlug($slug);

if (!$product) {
    header('Location: /E-Commers-Website/pages/products.php');
    exit;
}

// Fetch related products
$db = Database::getInstance()->getConnection();
$relatedStmt = $db->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' 
    ORDER BY p.created_at DESC 
    LIMIT 4
");
$relatedStmt->execute([$product['category_id'], $product['id']]);
$relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle images
$images = json_decode($product['images'] ?? '[]', true);
if (empty($images)) {
    $images = ['/E-Commers-Website/assets/images/placeholder.jpg'];
}
$mainImage = $images[0];
?>

<!-- Premium Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0 position-absolute top-0 end-0" style="z-index: 10;">
                <button type="button" class="btn btn-dark btn-lg rounded-circle bg-opacity-50 backdrop-blur" data-bs-dismiss="modal" style="width: 48px; height: 48px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center p-0">
                <img id="previewImage" src="" class="img-fluid rounded-4 shadow-lg" style="max-height: 85vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<div class="page-header py-3 mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/E-Commers-Website/index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="/E-Commers-Website/pages/products.php">Products</a></li>
                <?php if (!empty($product['category_name'])): ?>
                    <li class="breadcrumb-item">
                        <a href="/E-Commers-Website/pages/products.php?category=<?= urlencode($product['category_slug'] ?? '') ?>">
                            <?= htmlspecialchars($product['category_name']) ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <!-- Product Images Gallery -->
        <div class="col-lg-6">
            <div class="product-gallery">
                <div class="main-image-container glass-card rounded-4 p-3 mb-3">
                    <img src="<?= htmlspecialchars($mainImage) ?>"
                        alt="<?= htmlspecialchars($product['name']) ?>"
                        class="main-image img-fluid rounded-3"
                        id="mainProductImage"
                        data-src="<?= htmlspecialchars($mainImage) ?>"
                        style="width: 100%; height: 450px; object-fit: contain; cursor: zoom-in;">
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="thumbnails-scroll">
                        <div class="d-flex gap-2 pb-2" style="overflow-x: auto;">
                            <?php foreach ($images as $index => $img): ?>
                                <div class="thumbnail-wrapper <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="<?= htmlspecialchars($img) ?>"
                                        class="thumbnail-img rounded-3"
                                        data-src="<?= htmlspecialchars($img) ?>"
                                        style="width: 90px; height: 90px; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <div class="product-info-card glass-card rounded-4 p-4 h-100">
                <h1 class="display-6 fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h1>

                <div class="d-flex flex-wrap gap-3 mb-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                        <i class="fas fa-tag me-1"></i> <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?>
                    </span>
                    <?php if (!empty($product['sku'])): ?>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">
                            <i class="fas fa-barcode me-1"></i> SKU: <?= htmlspecialchars($product['sku']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="price-section mb-4">
                    <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                        <div class="d-flex align-items-center gap-3">
                            <span class="display-5 fw-bold text-danger">$<?= number_format($product['sale_price'], 2) ?></span>
                            <span class="fs-4 text-muted text-decoration-line-through">$<?= number_format($product['price'], 2) ?></span>
                            <span class="badge bg-danger px-3 py-2 rounded-pill">SALE</span>
                        </div>
                    <?php else: ?>
                        <span class="display-5 fw-bold">$<?= number_format($product['price'], 2) ?></span>
                    <?php endif; ?>
                </div>

                <div class="stock-section mb-4">
                    <?php $inStock = $product['stock_quantity'] > 0 && $product['status'] === 'active'; ?>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-<?= $inStock ? 'success' : 'danger' ?> px-3 py-2 rounded-pill">
                            <i class="fas fa-<?= $inStock ? 'check-circle' : 'times-circle' ?> me-1"></i>
                            <?= $inStock ? 'In Stock' : 'Out of Stock' ?>
                        </span>
                        <?php if ($inStock): ?>
                            <span class="text-muted">(<?= $product['stock_quantity'] ?> units available)</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($product['short_description'])): ?>
                    <div class="short-description mb-4 p-3 bg-light bg-opacity-50 rounded-3">
                        <p class="lead mb-0 fs-6"><?= nl2br(htmlspecialchars($product['short_description'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="add-to-cart-section mb-4">
                    <div class="input-group input-group-lg" style="max-width: 250px;">
                        <input type="number" id="quantity" class="form-control form-control-lg" value="1" min="1"
                            max="<?= $product['stock_quantity'] ?>" <?= !$inStock ? 'disabled' : '' ?>>
                        <button class="btn btn-primary btn-lg add-to-cart-btn"
                            data-product-id="<?= $product['id'] ?>"
                            <?= !$inStock ? 'disabled' : '' ?>>
                            <i class="fas fa-cart-plus me-2"></i> Add to Cart
                        </button>
                    </div>
                </div>

                <?php if (!empty($product['description'])): ?>
                    <div class="description-section mt-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-align-left me-2"></i>Description</h5>
                        <div class="description-content text-muted">
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="mt-5 pt-4">
            <div class="section-header mb-4">
                <h2><i class="fas fa-th-large me-2"></i>You May Also Like</h2>
                <a href="/E-Commers-Website/pages/products.php?category=<?= urlencode($product['category_slug'] ?? '') ?>" class="view-all">
                    View All <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="products-grid-container">
                <?php foreach ($relatedProducts as $rel): ?>
                    <div class="product-card">
                        <?php
                        $relImages = json_decode($rel['images'] ?? '[]', true);
                        $relImage = !empty($relImages) ? $relImages[0] : '/E-Commers-Website/assets/images/placeholder.jpg';
                        ?>
                        <a href="/E-Commers-Website/pages/product-detail.php?slug=<?= urlencode($rel['slug']) ?>">
                            <img src="<?= htmlspecialchars($relImage) ?>" alt="<?= htmlspecialchars($rel['name']) ?>">
                        </a>
                        <div class="product-info">
                            <span class="product-category"><?= htmlspecialchars($rel['category_name'] ?? 'General') ?></span>
                            <a href="/E-Commers-Website/pages/product-detail.php?slug=<?= urlencode($rel['slug']) ?>" class="product-name">
                                <?= htmlspecialchars($rel['name']) ?>
                            </a>
                            <div class="product-price">
                                <?php if (!empty($rel['sale_price']) && $rel['sale_price'] < $rel['price']): ?>
                                    <span class="price text-danger">$<?= number_format($rel['sale_price'], 2) ?></span>
                                    <span class="old-price">$<?= number_format($rel['price'], 2) ?></span>
                                <?php else: ?>
                                    <span class="price">$<?= number_format($rel['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="add-to-cart" data-product-id="<?= $rel['id'] ?>">
                                <i class="fas fa-cart-plus me-1"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainImg = document.getElementById('mainProductImage');
        const thumbnails = document.querySelectorAll('.thumbnail-img');
        const previewModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
        const previewImg = document.getElementById('previewImage');

        // Thumbnail click
        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', function() {
                const newSrc = this.dataset.src;
                mainImg.src = newSrc;
                mainImg.dataset.src = newSrc;
                document.querySelectorAll('.thumbnail-wrapper').forEach(w => w.classList.remove('active'));
                this.closest('.thumbnail-wrapper').classList.add('active');
            });
        });

        // Main image click -> preview
        mainImg.addEventListener('click', function() {
            previewImg.src = this.dataset.src || this.src;
            previewModal.show();
        });

        // Double click thumbnail -> preview
        thumbnails.forEach(thumb => {
            thumb.addEventListener('dblclick', function() {
                previewImg.src = this.dataset.src;
                previewModal.show();
            });
        });
    });

    // Cart functionality (unchanged)
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

    async function addToCart(productId, quantity = 1) {
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
                return false;
            }
            if (result.success) {
                showToast(result.message || 'Item added to cart!', 'success');
                updateCartCount(result.cart_count);
                return true;
            } else {
                showToast(result.message || 'Could not add item', 'danger');
                return false;
            }
        } catch (error) {
            showToast('Something went wrong. Please try again.', 'danger');
            return false;
        }
    }

    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            const quantity = parseInt(document.getElementById('quantity').value);
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adding...';
            await addToCart(productId, quantity);
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            await addToCart(productId, 1);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>