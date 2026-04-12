<?php
// MUST BE FIRST
require_once 'classes/User.php';

$pageTitle = 'Login - MyStore';
$message = '';
$messageType = '';

// Process login BEFORE any HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $result = $user->login($_POST['email'] ?? '', $_POST['password'] ?? '');


    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';

    // Redirect immediately on success
    if ($result['success']) {
        $redirect = $result['user']['is_admin'] ? '/E-Commers-Website/admin/dashboard.php' : '/E-Commers-Website/index.php';
        echo '<meta http-equiv="refresh" content="0;url=' . $redirect . '">';
        exit;
    }
}

// NOW include header
require_once 'includes/header.php';
?>

<style>
    .auth-container {
        max-width: 400px;
        margin: 80px auto;
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .auth-container h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #555;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 5px;
        font-size: 16px;
        transition: border-color 0.3s;
    }

    .form-group input:focus {
        outline: none;
        border-color: #3B82F6;
    }

    .btn-submit {
        width: 100%;
        padding: 12px;
        background: #3B82F6;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        margin-top: 10px;
    }

    .btn-submit:hover {
        background: #2563EB;
    }

    .auth-footer {
        text-align: center;
        margin-top: 25px;
        color: #666;
    }

    .auth-footer a {
        color: #3B82F6;
        text-decoration: none;
    }

    .alert {
        padding: 12px 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .divider {
        text-align: center;
        margin: 20px 0;
        color: #999;
        position: relative;
    }

    .divider::before,
    .divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 45%;
        height: 1px;
        background: #e0e0e0;
    }

    .divider::before {
        left: 0;
    }

    .divider::after {
        right: 0;
    }
</style>

<div class="auth-container">
    <h2>🔐 Welcome Back</h2>

    <?php if (isset($_GET['registered'])): ?>
        <div class="alert alert-success">✅ Account created! Please login.</div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="your@email.com">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn-submit">Sign In</button>
    </form>

    <div class="divider">or</div>

    <div class="auth-footer">
        Don't have an account? <a href="register.php">Create one</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>