<?php
require_once 'auth_check.php';
require_once '../classes/Product.php';

$productObj = new Product();
$db = db();

// Handle delete
if (isset($_GET['delete'])) {
    $productObj->delete($_GET['delete']);
    header('Location: /E-Commers-Website/admin/products.php');
    exit;
}

// Get all products with pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$filters = ['limit' => $perPage, 'offset' => ($page - 1) * $perPage];
$products = $productObj->getAll($filters);
$totalProducts = $productObj->getAll([], true);
$totalPages = ceil($totalProducts / $perPage);

$pageTitle = 'Products';
require_once 'header.php'; // Admin header
?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <a href="/E-Commers-Website/admin/product-form.php" class="btn-admin text-decoration-none">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

<?php if (empty($products)): ?>
    <div class="admin-card">
        <div class="admin-card-body text-center py-5 text-muted">
            <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
            <p class="mb-0">No products found.</p>
        </div>
    </div>
<?php else: ?>
    <div class="admin-card">
        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $prod): ?>
                            <tr>
                                <td><?= $prod['id'] ?></td>
                                <td>
                                    <?php
                                    $images = json_decode($prod['images'] ?? '[]', true);
                                    $img = !empty($images) ? $images[0] : '/E-Commers-Website/assets/images/placeholder.jpg';
                                    ?>
                                    <img src="<?= htmlspecialchars($img) ?>" width="45" height="45" style="object-fit: cover;" class="rounded-2">
                                </td>
                                <td class="fw-semibold"><?= htmlspecialchars($prod['name']) ?></td>
                                <td><?= htmlspecialchars($prod['category_name'] ?? 'N/A') ?></td>
                                <td>$<?= number_format($prod['price'], 2) ?></td>
                                <td><?= $prod['stock_quantity'] ?></td>
                                <td>
                                    <?php if ($prod['status'] == 'active'): ?>
                                        <span class="badge-admin green">Active</span>
                                    <?php elseif ($prod['status'] == 'out_of_stock'): ?>
                                        <span class="badge-admin red">Out of Stock</span>
                                    <?php else: ?>
                                        <span class="badge-admin" style="background: #6b7280; color: white;">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/E-Commers-Website/admin/product-form.php?id=<?= $prod['id'] ?>"
                                        class="btn-admin-outline btn-sm me-1 text-decoration-none">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?= $prod['id'] ?>"
                                        class="btn-admin-outline btn-sm text-danger border-danger text-decoration-none"
                                        onclick="return confirm('Delete this product?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="admin-pagination pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php require_once 'footer.php'; ?>