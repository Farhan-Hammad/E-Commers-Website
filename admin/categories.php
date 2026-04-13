<?php
require_once 'auth_check.php';
require_once '../includes/header.php';

$db = db();
$message = '';
$error = '';

// Handle category deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Check if category has products
    $check = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $check->execute([$id]);
    if ($check->fetchColumn() > 0) {
        $error = "Cannot delete category with existing products.";
    } else {
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Category deleted successfully.";
    }
}

// Handle form submission for add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']) ?: strtolower(str_replace(' ', '-', $name));
    $description = $_POST['description'] ?? '';
    $parent_id = $_POST['parent_id'] ?: null;
    $status = $_POST['status'] ?? 'active';

    // Handle image upload
    $imagePath = $_POST['existing_image'] ?? null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/../assets/uploads/categories/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'cat_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
            $imagePath = '/E-Commers-Website/assets/uploads/categories/' . $filename;
        }
    }

    try {
        if ($id) {
            $stmt = $db->prepare("UPDATE categories SET name=?, slug=?, description=?, parent_id=?, status=?, image=? WHERE id=?");
            $stmt->execute([$name, $slug, $description, $parent_id, $status, $imagePath, $id]);
            $message = "Category updated.";
        } else {
            $stmt = $db->prepare("INSERT INTO categories (name, slug, description, parent_id, status, image) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$name, $slug, $description, $parent_id, $status, $imagePath]);
            $message = "Category added.";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "Slug already exists. Please use a unique slug.";
        } else {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch all categories for display and parent dropdown
$categories = $db->query("
    SELECT c.*, p.name as parent_name 
    FROM categories c 
    LEFT JOIN categories p ON c.parent_id = p.id 
    ORDER BY c.name
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-md-block bg-light sidebar" style="min-height: 100vh;">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="/E-Commers-Website/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-Commers-Website/admin/products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/E-Commers-Website/admin/categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="/E-Commers-Website/admin/orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="/E-Commers-Website/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">Categories</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="resetForm()">
                    <i class="fas fa-plus"></i> Add New Category
                </button>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Parent</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?= $cat['id'] ?></td>
                                    <td>
                                        <?php if ($cat['image']): ?>
                                            <img src="<?= htmlspecialchars($cat['image']) ?>" width="40" height="40" style="object-fit: cover;" class="rounded">
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($cat['name']) ?></td>
                                    <td><?= htmlspecialchars($cat['slug']) ?></td>
                                    <td><?= htmlspecialchars($cat['parent_name'] ?? '—') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $cat['status'] == 'active' ? 'success' : 'secondary' ?>"><?= $cat['status'] ?></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editCategory(<?= htmlspecialchars(json_encode($cat)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this category?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="catId">
                <input type="hidden" name="existing_image" id="existingImage">
                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" id="catName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" id="catSlug" class="form-control" placeholder="auto-generated if empty">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="catDesc" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Parent Category</label>
                    <select name="parent_id" id="catParent" class="form-select">
                        <option value="">None (Top Level)</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" id="catStatus" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <div id="currentImagePreview" class="mt-2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Category</button>
            </div>
        </form>
    </div>
</div>

<script>
    function resetForm() {
        document.getElementById('modalTitle').textContent = 'Add Category';
        document.getElementById('catId').value = '';
        document.getElementById('catName').value = '';
        document.getElementById('catSlug').value = '';
        document.getElementById('catDesc').value = '';
        document.getElementById('catParent').value = '';
        document.getElementById('catStatus').value = 'active';
        document.getElementById('existingImage').value = '';
        document.getElementById('currentImagePreview').innerHTML = '';
    }

    function editCategory(cat) {
        document.getElementById('modalTitle').textContent = 'Edit Category';
        document.getElementById('catId').value = cat.id;
        document.getElementById('catName').value = cat.name;
        document.getElementById('catSlug').value = cat.slug;
        document.getElementById('catDesc').value = cat.description || '';
        document.getElementById('catParent').value = cat.parent_id || '';
        document.getElementById('catStatus').value = cat.status;
        document.getElementById('existingImage').value = cat.image || '';
        let preview = '';
        if (cat.image) {
            preview = `<img src="${cat.image}" width="80" height="80" style="object-fit:cover;" class="border rounded">`;
        }
        document.getElementById('currentImagePreview').innerHTML = preview;
        new bootstrap.Modal(document.getElementById('categoryModal')).show();
    }
</script>

<?php require_once '../includes/footer.php'; ?>