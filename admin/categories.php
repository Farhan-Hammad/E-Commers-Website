<?php
require_once 'auth_check.php';
$pageTitle = 'Categories';
require_once 'header.php'; // Admin header

$db = db();
$message = '';
$error = '';

// Handle category deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
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

$categories = $db->query("
    SELECT c.*, p.name as parent_name 
    FROM categories c 
    LEFT JOIN categories p ON c.parent_id = p.id 
    ORDER BY c.name
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <button class="btn-admin" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="resetForm()">
        <i class="fas fa-plus"></i> Add New Category
    </button>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <i class="fas fa-list me-2"></i>All Categories
    </div>
    <div class="admin-card-body p-0">
        <table class="admin-table">
            <thead>
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
                                <img src="<?= htmlspecialchars($cat['image']) ?>" width="40" height="40" style="object-fit: cover;" class="rounded-2">
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold"><?= htmlspecialchars($cat['name']) ?></td>
                        <td><?= htmlspecialchars($cat['slug']) ?></td>
                        <td><?= htmlspecialchars($cat['parent_name'] ?? '—') ?></td>
                        <td>
                            <?php if ($cat['status'] == 'active'): ?>
                                <span class="badge-admin green">Active</span>
                            <?php else: ?>
                                <span class="badge-admin" style="background: #6b7280; color: white;">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn-admin-outline btn-sm me-1" onclick="editCategory(<?= htmlspecialchars(json_encode($cat)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?delete=<?= $cat['id'] ?>" class="btn-admin-outline btn-sm text-danger border-danger" onclick="return confirm('Delete this category?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No categories found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <input type="hidden" name="id" id="catId">
                    <input type="hidden" name="existing_image" id="existingImage">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name *</label>
                        <input type="text" name="name" id="catName" class="admin-form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slug</label>
                        <input type="text" name="slug" id="catSlug" class="admin-form-control" placeholder="auto-generated if empty">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="catDesc" class="admin-form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Parent Category</label>
                        <select name="parent_id" id="catParent" class="admin-form-control">
                            <option value="">None (Top Level)</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" id="catStatus" class="admin-form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Image</label>
                        <input type="file" name="image" class="admin-form-control" accept="image/*">
                        <div id="currentImagePreview" class="mt-3"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn-admin-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-admin">Save Category</button>
                </div>
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
            preview = `<img src="${cat.image}" width="80" height="80" style="object-fit:cover;" class="border rounded-2">`;
        }
        document.getElementById('currentImagePreview').innerHTML = preview;
        new bootstrap.Modal(document.getElementById('categoryModal')).show();
    }
</script>

<?php require_once 'footer.php'; ?>