<?php
require_once 'auth_check.php';
require_once '../classes/Product.php';
require_once '../includes/header.php';

$productObj = new Product();
$db = db(); // Use global db() helper

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

// Get categories for dropdown
$categories = $db->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- rest of file unchanged -->

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar (same as dashboard) -->
        <nav class="col-md-2 d-md-block bg-light sidebar" style="min-height: 100vh;">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="/E-Commers-Website/admin/dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>" href="/E-Commers-Website/admin/products.php">
                            <i class="fas fa-box"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>" href="/E-Commers-Website/admin/categories.php">
                            <i class="fas fa-tags"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>" href="/E-Commers-Website/admin/orders.php">
                            <i class="fas fa-shopping-cart"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="/E-Commers-Website/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">Products</h1>
                <a href="/E-Commers-Website/admin/product-form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
            </div>

            <?php if (empty($products)): ?>
                <div class="alert alert-info">No products found.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
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
                                        <img src="<?= htmlspecialchars($img) ?>" width="50" height="50" style="object-fit: cover;">
                                    </td>
                                    <td><?= htmlspecialchars($prod['name']) ?></td>
                                    <td><?= htmlspecialchars($prod['category_name'] ?? 'N/A') ?></td>
                                    <td>$<?= number_format($prod['price'], 2) ?></td>
                                    <td><?= $prod['stock_quantity'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $prod['status'] == 'active' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst($prod['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/E-Commers-Website/admin/product-form.php?id=<?= $prod['id'] ?>"
                                            class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="?delete=<?= $prod['id'] ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Delete this product?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>