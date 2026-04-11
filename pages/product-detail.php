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

// Redirect if product not found
if (!$product) {
    header('Location: /E-Commers-Website/pages/products.php');
    exit;
}

// Fetch related products (same category, excluding current)
$db = Database::getInstance()->getConnection();
$relatedStmt = $db->prepare("
    SELECT * FROM products 
    WHERE category_id = ? AND id != ? AND status = 'active' 
    ORDER BY created_at DESC 
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

<!-- Breadcrumb -->
<div class="bg-light py-3 mb-4">
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
    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <img src="<?= htmlspecialchars($mainImage) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                    class="card-img-top"
                    style="height: 400px; object-fit: contain; background: #f8f9fa;">
            </div>
            <!-- Thumbnail Gallery (if multiple images) -->
            <?php if (count($images) > 1): ?>
                <div class="row mt-3 g-2">
                    <?php foreach ($images as $img): ?>
                        <div class="col-3">
                            <img src="<?= htmlspecialchars($img) ?>"
                                class="img-thumbnail"
                                style="height: 80px; object-fit: cover; cursor: pointer;"
                                onclick="document.querySelector('.card-img-top').src = this.src">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="col-md-6">
            <h1 class="h2 mb-2"><?= htmlspecialchars($product['name']) ?></h1>

            <!-- Category & SKU -->
            <p class="text-muted mb-3">
                <span>Category: <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></span>
                <?php if (!empty($product['sku'])): ?>
                    <span class="ms-3">SKU: <?= htmlspecialchars($product['sku']) ?></span>
                <?php endif; ?>
            </p>

            <!-- Price -->
            <div class="mb-3">
                <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                    <span class="text-danger fs-2 fw-bold">$<?= number_format($product['sale_price'], 2) ?></span>
                    <span class="text-muted text-decoration-line-through ms-2 fs-5">$<?= number_format($product['price'], 2) ?></span>
                    <span class="badge bg-danger ms-2">Sale</span>
                <?php else: ?>
                    <span class="fs-2 fw-bold">$<?= number_format($product['price'], 2) ?></span>
                <?php endif; ?>
            </div>

            <!-- Stock Status -->
            <div class="mb-3">
                <?php
                $inStock = $product['stock_quantity'] > 0 && $product['status'] === 'active';
                ?>
                <span class="badge bg-<?= $inStock ? 'success' : 'danger' ?>">
                    <?= $inStock ? 'In Stock' : 'Out of Stock' ?>
                </span>
                <?php if ($inStock): ?>
                    <span class="text-muted ms-2">(<?= $product['stock_quantity'] ?> available)</span>
                <?php endif; ?>
            </div>

            <!-- Short Description -->
            <?php if (!empty($product['short_description'])): ?>
                <p class="lead"><?= nl2br(htmlspecialchars($product['short_description'])) ?></p>
            <?php endif; ?>

            <!-- Add to Cart -->
            <div class="mb-4">
                <div class="input-group" style="max-width: 200px;">
                    <input type="number" id="quantity" class="form-control" value="1" min="1"
                        max="<?= $product['stock_quantity'] ?>" <?= !$inStock ? 'disabled' : '' ?>>
                    <button class="btn btn-primary add-to-cart-btn"
                        data-product-id="<?= $product['id'] ?>"
                        <?= !$inStock ? 'disabled' : '' ?>>
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </div>
            </div>

            <!-- Full Description -->
            <?php if (!empty($product['description'])): ?>
                <div class="mt-4">
                    <h5>Description</h5>
                    <div class="border-top pt-3">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="mt-5">
            <h3 class="mb-4">You May Also Like</h3>
            <div class="row g-4">
                <?php foreach ($relatedProducts as $rel): ?>
                    <div class="col-md-3 col-6">
                        <div class="card h-100 product-card shadow-sm">
                            <?php
                            $relImages = json_decode($rel['images'] ?? '[]', true);
                            $relImage = !empty($relImages) ? $relImages[0] : '/E-Commers-Website/assets/images/placeholder.jpg';
                            ?>
                            <a href="/E-Commers-Website/pages/product-detail.php?slug=<?= urlencode($rel['slug']) ?>">
                                <img src="<?= htmlspecialchars($relImage) ?>"
                                    class="card-img-top" alt="<?= htmlspecialchars($rel['name']) ?>"
                                    style="height: 180px; object-fit: cover;">
                            </a>
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="/E-Commers-Website/pages/product-detail.php?slug=<?= urlencode($rel['slug']) ?>"
                                        class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($rel['name']) ?>
                                    </a>
                                </h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if (!empty($rel['sale_price']) && $rel['sale_price'] < $rel['price']): ?>
                                            <span class="text-danger fw-bold">$<?= number_format($rel['sale_price'], 2) ?></span>
                                            <small class="text-muted text-decoration-line-through ms-1">$<?= number_format($rel['price'], 2) ?></small>
                                        <?php else: ?>
                                            <span class="fw-bold">$<?= number_format($rel['price'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary add-to-cart"
                                        data-product-id="<?= $rel['id'] ?>">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Add to Cart functionality
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
            if (result.success) {
                showToast(result.message || 'Item added to cart!', 'success');
                updateCartCount(result.cart_count);
            } else {
                showToast(result.message || 'Could not add item', 'danger');
            }
        } catch (error) {
            showToast('Something went wrong. Please try again.', 'danger');
        }
    }

    // Main Add to Cart button
    document.querySelector('.add-to-cart-btn').addEventListener('click', async function() {
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

    // Related products Add to Cart buttons
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