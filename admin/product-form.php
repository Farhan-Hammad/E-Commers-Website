<?php
require_once 'auth_check.php';
require_once '../classes/Product.php';
require_once '../classes/Database.php';
require_once '../includes/header.php';

$productObj = new Product();
$db = Database::getInstance()->getConnection();

// Get categories for dropdown
$categories = $db->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$isEdit = isset($_GET['id']);
$product = $isEdit ? $productObj->getById($_GET['id']) : null;

if ($isEdit && !$product) {
    header('Location: /E-Commers-Website/admin/products.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'category_id' => $_POST['category_id'],
        'name' => $_POST['name'],
        'slug' => strtolower(str_replace(' ', '-', $_POST['name'])), // Fixed: space to dash
        'description' => $_POST['description'],
        'short_description' => $_POST['short_description'],
        'price' => $_POST['price'],
        'sale_price' => $_POST['sale_price'] ?: null,
        'stock_quantity' => $_POST['stock_quantity'],
        'sku' => $_POST['sku'],
        'status' => $_POST['status'],
        'featured' => isset($_POST['featured']) ? 1 : 0
    ];

    if ($isEdit) {
        $productObj->update($_GET['id'], $data);
    } else {
        $productObj->create($data);
    }

    header('Location: /E-Commers-Website/admin/products.php');
    exit;
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-md-block bg-light sidebar" style="min-height: 100vh;">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="/E-Commers-Website/admin/dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/E-Commers-Website/admin/products.php">
                            <i class="fas fa-box"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/E-Commers-Website/admin/orders.php">
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
            <h1 class="h2 mb-4"><?= $isEdit ? 'Edit' : 'Add New' ?> Product</h1>

            <form method="POST" class="bg-white p-4 rounded shadow-sm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required
                            value="<?= htmlspecialchars($product['name'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category *</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Price *</label>
                        <input type="number" step="0.01" name="price" class="form-control" required
                            value="<?= $product['price'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sale Price</label>
                        <input type="number" step="0.01" name="sale_price" class="form-control"
                            value="<?= $product['sale_price'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stock Quantity *</label>
                        <input type="number" name="stock_quantity" class="form-control" required
                            value="<?= $product['stock_quantity'] ?? 0 ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SKU *</label>
                        <input type="text" name="sku" class="form-control" required
                            value="<?= htmlspecialchars($product['sku'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= ($product['status'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($product['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="out_of_stock" <?= ($product['status'] ?? '') == 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="featured" id="featured"
                                <?= isset($product['featured']) && $product['featured'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="featured">Featured Product</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="2"><?= htmlspecialchars($product['short_description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Product</button>
                    <a href="/E-Commers-Website/admin/products.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </main>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>