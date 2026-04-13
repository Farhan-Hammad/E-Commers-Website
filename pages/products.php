<?php
require_once '../includes/header.php';
require_once '../classes/Product.php';

$product = new Product();

// --- Fetch categories for sidebar ---
$db = Database::getInstance()->getConnection();
$catStmt = $db->query("SELECT id, name, slug FROM categories WHERE status = 1 ORDER BY name");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// --- Build filter array from GET parameters ---
$filters = [];
$pageTitle = 'All Products';
$activeFilters = [];

// Search
if (!empty($_GET['search'])) {
    $filters['search'] = trim($_GET['search']);
    $pageTitle = 'Search: ' . htmlspecialchars($filters['search']);
    $activeFilters['search'] = $filters['search'];
}

// Category filter (slug)
if (!empty($_GET['category'])) {
    $filters['category_slug'] = $_GET['category'];
    $catStmt = $db->prepare("SELECT name FROM categories WHERE slug = ?");
    $catStmt->execute([$_GET['category']]);
    $catName = $catStmt->fetchColumn();
    if ($catName) {
        $pageTitle = htmlspecialchars($catName);
        $activeFilters['category'] = $_GET['category'];
    }
}

// Price range
if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
    $filters['min_price'] = (float)$_GET['min_price'];
    $activeFilters['min_price'] = $filters['min_price'];
}
if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
    $filters['max_price'] = (float)$_GET['max_price'];
    $activeFilters['max_price'] = $filters['max_price'];
}

// Sorting
$allowedSort = ['price_asc', 'price_desc', 'name_asc', 'name_desc', 'newest'];
$sort = $_GET['sort'] ?? 'newest';
if (!in_array($sort, $allowedSort)) {
    $sort = 'newest';
}
$filters['sort'] = $sort;

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 12;
$filters['limit'] = $perPage;
$filters['offset'] = ($page - 1) * $perPage;

// --- Get total count (for pagination) and products ---
$countFilters = $filters;
unset($countFilters['limit'], $countFilters['offset']);
$totalProducts = $product->getAll($countFilters, true); // second param true = count only
$totalPages = ceil($totalProducts / $perPage);

$products = $product->getAll($filters); // returns plain array of products
?>

<!-- Page Header -->
<div class="bg-light py-4 mb-4">
    <div class="container">
        <h1 class="h2 mb-0"><?= $pageTitle ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <?php if (!empty($activeFilters['category'])): ?>
                    <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                    <li class="breadcrumb-item active"><?= $pageTitle ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item active"><?= $pageTitle ?></li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <form method="GET" id="filterForm" class="bg-white p-3 rounded shadow-sm">
                <!-- Preserve search query if present -->
                <?php if (!empty($filters['search'])): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($filters['search']) ?>">
                <?php endif; ?>

                <!-- Categories -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">Categories</h5>
                    <div class="list-group list-group-flush">
                        <a href="?<?= http_build_query(array_diff_key($_GET, ['category' => '', 'page' => ''])) ?>"
                            class="list-group-item list-group-item-action <?= empty($activeFilters['category']) ? 'active' : '' ?>">
                            All Categories
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['category' => $cat['slug'], 'page' => null])) ?>"
                                class="list-group-item list-group-item-action <?= ($activeFilters['category'] ?? '') === $cat['slug'] ? 'active' : '' ?>">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">Price Range</h5>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="number" name="min_price" class="form-control form-control-sm"
                                placeholder="Min $" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                        </div>
                        <div class="col-6">
                            <input type="number" name="max_price" class="form-control form-control-sm"
                                placeholder="Max $" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100 mt-2">Apply</button>
                </div>

                <!-- Clear Filters -->
                <?php if (!empty($activeFilters)): ?>
                    <a href="products.php" class="btn btn-outline-secondary btn-sm w-100">Clear All Filters</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Sort & Results Count -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted">Showing <?= count($products) ?> of <?= $totalProducts ?> products</p>
                <select class="form-select w-auto" name="sort" form="filterForm" onchange="document.getElementById('filterForm').submit()">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Name: A-Z</option>
                    <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Name: Z-A</option>
                </select>
            </div>

            <!-- Products Grid -->
            <?php if (empty($products)): ?>
                <div class="alert alert-info">No products found matching your criteria.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $prod): ?>
                        <div class="col-md-4 col-6">
                            <div class="card h-100 product-card shadow-sm">
                                <?php
                                $images = json_decode($prod['images'] ?? '[]', true);
                                $firstImage = !empty($images) ? $images[0] : '/E-Commers-Website/assets/images/placeholder.jpg';
                                ?>
                                <a href="product-detail.php?slug=<?= urlencode($prod['slug']) ?>">
                                    <img src="<?= htmlspecialchars($firstImage) ?>"
                                        class="card-img-top" alt="<?= htmlspecialchars($prod['name']) ?>"
                                        style="height: 200px; object-fit: cover;">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="product-detail.php?slug=<?= urlencode($prod['slug']) ?>"
                                            class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($prod['name']) ?>
                                        </a>
                                    </h5>
                                    <p class="card-text small text-muted">
                                        <?= htmlspecialchars(substr($prod['short_description'] ?? '', 0, 60)) ?>...
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php if (!empty($prod['sale_price']) && $prod['sale_price'] < $prod['price']): ?>
                                                <span class="text-danger fw-bold">$<?= number_format($prod['sale_price'], 2) ?></span>
                                                <small class="text-muted text-decoration-line-through ms-1">$<?= number_format($prod['price'], 2) ?></small>
                                            <?php else: ?>
                                                <span class="fw-bold">$<?= number_format($prod['price'], 2) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary add-to-cart"
                                            data-product-id="<?= $prod['id'] ?>">
                                            <i class="fas fa-cart-plus"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php
                            $queryParams = $_GET;
                            unset($queryParams['page']);
                            $baseUrl = '?' . http_build_query($queryParams);
                            $baseUrl = $baseUrl ? $baseUrl . '&' : '?';
                            ?>

                            <!-- Previous -->
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= $baseUrl ?>page=<?= $page - 1 ?>">&laquo;</a>
                                </li>
                            <?php endif; ?>

                            <!-- Page numbers -->
                            <?php
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= $baseUrl ?>page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next -->
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= $baseUrl ?>page=<?= $page + 1 ?>">&raquo;</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
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

<?php require_once '../includes/footer.php'; ?>