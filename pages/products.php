<?php
require_once '../includes/header.php';
require_once '../classes/Product.php';

$product = new Product();

// --- Fetch categories for sidebar ---
$db = Database::getInstance()->getConnection();
$catStmt = $db->query("SELECT id, name, slug FROM categories WHERE status = 'active' ORDER BY name");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// --- Build filter array from GET parameters ---
$filters = [];
$pageTitle = 'All Products';
$activeFilters = [];

if (!empty($_GET['search'])) {
    $filters['search'] = trim($_GET['search']);
    $pageTitle = 'Search: ' . htmlspecialchars($filters['search']);
    $activeFilters['search'] = $filters['search'];
}
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
if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
    $filters['min_price'] = (float)$_GET['min_price'];
    $activeFilters['min_price'] = $filters['min_price'];
}
if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
    $filters['max_price'] = (float)$_GET['max_price'];
    $activeFilters['max_price'] = $filters['max_price'];
}

$allowedSort = ['price_asc', 'price_desc', 'name_asc', 'name_desc', 'newest'];
$sort = $_GET['sort'] ?? 'newest';
if (!in_array($sort, $allowedSort)) $sort = 'newest';
$filters['sort'] = $sort;

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 12;
$filters['limit'] = $perPage;
$filters['offset'] = ($page - 1) * $perPage;

$countFilters = $filters;
unset($countFilters['limit'], $countFilters['offset']);
$totalProducts = $product->getAll($countFilters, true);
$totalPages = ceil($totalProducts / $perPage);

$products = $product->getAll($filters);
?>

<!-- Page Header (theme‑aware) -->
<div class="page-header py-4 mb-4">
    <div class="container">
        <h1 class="h2 mb-0"><?= $pageTitle ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/E-Commers-Website/index.php">Home</a></li>
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
            <form method="GET" id="filterForm" class="filter-card card shadow-sm border-0">
                <div class="card-body">
                    <?php if (!empty($filters['search'])): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($filters['search']) ?>">
                    <?php endif; ?>

                    <h5 class="border-bottom pb-2 mb-3">Categories</h5>
                    <div class="list-group list-group-flush mb-4">
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

                    <h5 class="border-bottom pb-2 mb-3">Price Range</h5>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <input type="number" name="min_price" class="form-control form-control-sm"
                                placeholder="Min $" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                        </div>
                        <div class="col-6">
                            <input type="number" name="max_price" class="form-control form-control-sm"
                                placeholder="Max $" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Apply</button>

                    <?php if (!empty($activeFilters)): ?>
                        <a href="products.php" class="btn btn-outline-secondary btn-sm w-100 mt-2">Clear All Filters</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
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

            <?php if (empty($products)): ?>
                <div class="alert alert-info">No products found.</div>
            <?php else: ?>
                <div class="products-grid-container">
                    <?php foreach ($products as $prod): ?>
                        <div class="product-card">
                            <?php
                            $images = json_decode($prod['images'] ?? '[]', true);
                            $firstImage = !empty($images) ? $images[0] : '/E-Commers-Website/assets/images/placeholder.jpg';
                            ?>
                            <a href="product-detail.php?slug=<?= urlencode($prod['slug']) ?>">
                                <img src="<?= htmlspecialchars($firstImage) ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                            </a>
                            <div class="product-info">
                                <span class="product-category"><?= htmlspecialchars($prod['category_name'] ?? 'General') ?></span>
                                <a href="product-detail.php?slug=<?= urlencode($prod['slug']) ?>" class="product-name">
                                    <?= htmlspecialchars($prod['name']) ?>
                                </a>
                                <div class="product-price">
                                    <?php if (!empty($prod['sale_price']) && $prod['sale_price'] < $prod['price']): ?>
                                        <span class="price text-danger">$<?= number_format($prod['sale_price'], 2) ?></span>
                                        <span class="old-price">$<?= number_format($prod['price'], 2) ?></span>
                                    <?php else: ?>
                                        <span class="price">$<?= number_format($prod['price'], 2) ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="add-to-cart" data-product-id="<?= $prod['id'] ?>">
                                    🛒 Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php
                            $queryParams = $_GET;
                            unset($queryParams['page']);
                            $baseUrl = '?' . http_build_query($queryParams);
                            $baseUrl = $baseUrl ? $baseUrl . '&' : '?';

                            if ($page > 1): ?>
                                <li class="page-item"><a class="page-link" href="<?= $baseUrl ?>page=<?= $page - 1 ?>">&laquo;</a></li>
                            <?php endif;

                            for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= $baseUrl ?>page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor;

                            if ($page < $totalPages): ?>
                                <li class="page-item"><a class="page-link" href="<?= $baseUrl ?>page=<?= $page + 1 ?>">&raquo;</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Cart functionality (same as index)
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

<?php require_once '../includes/footer.php'; ?>