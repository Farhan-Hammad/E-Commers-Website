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

// Handle profile update
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
    if ($result['success']) {
        $userData = $user->getById($currentUser['id']);
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $result = $user->changePassword(
        $currentUser['id'],
        $_POST['current_password'],
        $_POST['new_password']
    );
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'danger';
}
?>

<div class="container py-5">
    <h1 class="mb-4">My Profile</h1>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($userData['email']) ?>" disabled>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($userData['first_name']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($userData['last_name']) ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($userData['phone']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($userData['address']) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($userData['city']) ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($userData['country']) ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Postal Code</label>
                                <input type="text" name="postal_code" class="form-control" value="<?= htmlspecialchars($userData['postal_code']) ?>">
                            </div>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required minlength="6">
                        </div>
                        <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>