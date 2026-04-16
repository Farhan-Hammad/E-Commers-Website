<?php
require_once 'includes/header.php';
require_once 'classes/User.php';

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: /E-Commers-Website/login.php');
    exit;
}

$currentUser = $user->getCurrentUser();
$userData = $user->getById($currentUser['id']);
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $updateData = [
        'first_name' => $_POST['first_name'],
        'last_name'  => $_POST['last_name'],
        'phone'      => $_POST['phone'],
        'address'    => $_POST['address'],
        'city'       => $_POST['city'],
        'country'    => $_POST['country'],
        'postal_code' => $_POST['postal_code']
    ];
    $result = $user->updateProfile($currentUser['id'], $updateData);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'danger';
    if ($result['success']) $userData = $user->getById($currentUser['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $result = $user->changePassword($currentUser['id'], $_POST['current_password'], $_POST['new_password']);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'danger';
}
?>

<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <h1 class="display-5 fw-bold mb-0">
            <i class="fas fa-user-circle me-3 text-primary"></i>My Profile
        </h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Profile Information -->
        <div class="col-lg-6">
            <div class="glass-card rounded-4 p-4 p-md-5 h-100">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">
                    <i class="fas fa-id-card me-2"></i>Profile Information
                </h5>
                <form method="POST">
                    <!-- Email (readonly) -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted">
                            <i class="fas fa-envelope me-1"></i>Email Address
                        </label>
                        <input type="email" class="form-control form-control-lg rounded-pill"
                            value="<?= htmlspecialchars($userData['email']) ?>" disabled>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">First Name *</label>
                            <input type="text" name="first_name" class="form-control form-control-lg rounded-pill"
                                value="<?= htmlspecialchars($userData['first_name']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Last Name *</label>
                            <input type="text" name="last_name" class="form-control form-control-lg rounded-pill"
                                value="<?= htmlspecialchars($userData['last_name']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="tel" name="phone" class="form-control form-control-lg rounded-pill"
                            value="<?= htmlspecialchars($userData['phone']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Street Address</label>
                        <textarea name="address" class="form-control rounded-4" rows="2"><?= htmlspecialchars($userData['address']) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control form-control-lg rounded-pill"
                                value="<?= htmlspecialchars($userData['city']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Country</label>
                            <input type="text" name="country" class="form-control form-control-lg rounded-pill"
                                value="<?= htmlspecialchars($userData['country']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control form-control-lg rounded-pill"
                                value="<?= htmlspecialchars($userData['postal_code']) ?>">
                        </div>
                    </div>

                    <button type="submit" name="update_profile" class="btn btn-primary rounded-pill px-4 py-2 mt-3">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-lg-6">
            <div class="glass-card rounded-4 p-4 p-md-5 h-100">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">
                    <i class="fas fa-lock me-2"></i>Change Password
                </h5>
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Current Password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="current_password"
                                class="form-control form-control-lg rounded-pill" required>
                            <button class="btn btn-outline-secondary rounded-pill toggle-password" type="button"
                                data-target="current_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password" id="new_password"
                                class="form-control form-control-lg rounded-pill" required minlength="6">
                            <button class="btn btn-outline-secondary rounded-pill toggle-password" type="button"
                                data-target="new_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-info-circle me-1"></i>Minimum 6 characters
                        </small>
                    </div>

                    <button type="submit" name="change_password" class="btn btn-warning rounded-pill px-4 py-2">
                        <i class="fas fa-key me-2"></i>Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Password Toggle Script -->
<script>
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>