<?php
require_once 'auth_check.php';
require_once '../classes/Product.php';

$productObj = new Product();
$db = db();

// Get categories for dropdown
$categories = $db->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$isEdit = isset($_GET['id']);
$product = $isEdit ? $productObj->getById($_GET['id']) : null;

if ($isEdit && !$product) {
    header('Location: /E-Commers-Website/admin/products.php');
    exit;
}

$error = '';
$message = '';

// Handle form submission BEFORE any output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadedImages = [];
    $uploadDir = __DIR__ . '/../assets/uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Process uploaded files
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                $filename = uniqid('prod_') . '.' . $ext;
                $destination = $uploadDir . $filename;
                if (move_uploaded_file($tmpName, $destination)) {
                    $uploadedImages[] = '/E-Commers-Website/assets/uploads/products/' . $filename;
                }
            }
        }
    }

    // If editing and no new images, keep existing ones
    if ($isEdit && empty($uploadedImages)) {
        $existingImages = json_decode($product['images'] ?? '[]', true);
        $uploadedImages = $existingImages ?: [];
    }

    // --- Price Validation ---
    $price = (float)$_POST['price'];
    $salePrice = $_POST['sale_price'] !== '' ? (float)$_POST['sale_price'] : null;

    if ($price > 99999999.99 || $price < 0) {
        $error = "Price must be between 0 and 99,999,999.99.";
    }
    if ($salePrice !== null && ($salePrice > 99999999.99 || $salePrice < 0)) {
        $error = "Sale price must be between 0 and 99,999,999.99.";
    }

    // Only proceed if no validation error
    if (!$error) {
        $data = [
            'category_id'      => $_POST['category_id'],
            'name'             => $_POST['name'],
            'slug'             => strtolower(str_replace(' ', '-', $_POST['name'])),
            'description'      => $_POST['description'],
            'short_description' => $_POST['short_description'],
            'price'            => $price,
            'sale_price'       => $salePrice,
            'stock_quantity'   => $_POST['stock_quantity'],
            'sku'              => $_POST['sku'],
            'status'           => $_POST['status'],
            'featured'         => isset($_POST['featured']) ? 1 : 0,
            'images'           => json_encode($uploadedImages)
        ];

        if ($isEdit) {
            $result = $productObj->update($_GET['id'], $data);
        } else {
            $result = $productObj->create($data);
        }

        if (is_array($result) && isset($result['error'])) {
            $error = $result['error'];
        } else {
            header('Location: /E-Commers-Website/admin/products.php');
            exit;
        }
    }
}

// Now include header (output starts here)
require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-md-block bg-light sidebar" style="min-height: 100vh;">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="/E-Commers-Website/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/E-Commers-Website/admin/products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-Commers-Website/admin/categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-Commers-Website/admin/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="/E-Commers-Website/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <h1 class="h2 mb-4"><?= $isEdit ? 'Edit' : 'Add New' ?> Product</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($product['name'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category *</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Price *</label>
                        <input type="number" step="0.01" name="price" class="form-control" required value="<?= $product['price'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sale Price</label>
                        <input type="number" step="0.01" name="sale_price" class="form-control" value="<?= $product['sale_price'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stock Quantity *</label>
                        <input type="number" name="stock_quantity" class="form-control" required value="<?= $product['stock_quantity'] ?? 0 ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SKU *</label>
                        <input type="text" name="sku" class="form-control" required value="<?= htmlspecialchars($product['sku'] ?? '') ?>">
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
                            <input class="form-check-input" type="checkbox" name="featured" id="featured" <?= isset($product['featured']) && $product['featured'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="featured">Featured Product</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Product Images</label>
                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">You can select multiple images. First image will be the main thumbnail.</small>
                        <?php if ($isEdit && !empty($product['images'])): ?>
                            <div class="mt-2">
                                <p>Current Images:</p>
                                <?php foreach (json_decode($product['images'], true) as $img): ?>
                                    <img src="<?= htmlspecialchars($img) ?>" width="80" height="80" style="object-fit: cover; margin-right: 5px;" class="border rounded">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
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