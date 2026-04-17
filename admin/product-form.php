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

$pageTitle = $isEdit ? 'Edit Product' : 'Add New Product';
require_once 'header.php'; // Admin header
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><?= $isEdit ? 'Edit' : 'Add New' ?> Product</h2>
    <a href="/E-Commers-Website/admin/products.php" class="btn-admin-outline text-decoration-none">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-body p-4">
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Name *</label>
                    <input type="text" name="name" class="admin-form-control" required
                        value="<?= htmlspecialchars($product['name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category *</label>
                    <select name="category_id" class="admin-form-control" required>
                        <option value="">Select...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Price *</label>
                    <input type="number" step="0.01" name="price" class="admin-form-control" required
                        value="<?= $product['price'] ?? '' ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Sale Price</label>
                    <input type="number" step="0.01" name="sale_price" class="admin-form-control"
                        value="<?= $product['sale_price'] ?? '' ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" class="admin-form-control" required
                        value="<?= $product['stock_quantity'] ?? 0 ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">SKU *</label>
                    <input type="text" name="sku" class="admin-form-control" required
                        value="<?= htmlspecialchars($product['sku'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="admin-form-control">
                        <option value="active" <?= ($product['status'] ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= ($product['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="out_of_stock" <?= ($product['status'] ?? '') == 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                    </select>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="featured" id="featured"
                            <?= isset($product['featured']) && $product['featured'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="featured">Featured Product</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Product Images</label>
                    <input type="file" name="images[]" class="admin-form-control" accept="image/*" multiple>
                    <small class="text-muted d-block mt-1">You can select multiple images. First image will be the main thumbnail.</small>
                    <?php if ($isEdit && !empty($product['images'])): ?>
                        <div class="mt-3">
                            <p class="fw-semibold mb-2">Current Images:</p>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach (json_decode($product['images'], true) as $img): ?>
                                    <img src="<?= htmlspecialchars($img) ?>" width="80" height="80"
                                        style="object-fit: cover;" class="border rounded-2">
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Short Description</label>
                    <textarea name="short_description" class="admin-form-control" rows="2"><?= htmlspecialchars($product['short_description'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Full Description</label>
                    <textarea name="description" class="admin-form-control" rows="5"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-admin">
                    <i class="fas fa-save me-1"></i> Save Product
                </button>
                <a href="/E-Commers-Website/admin/products.php" class="btn-admin-outline text-decoration-none">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>