<?php
// MUST BE FIRST - No output before this!
require_once 'classes/User.php';

$user = new User();
if ($user->isLoggedIn()) {
    header('Location: /E-Commers-Website/index.php');
    exit;
}

$pageTitle = 'Register - MyStore';
$message = '';
$messageType = '';

// Process form BEFORE any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $result = $user->register([
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'address' => $_POST['address'] ?? '',
        'city' => $_POST['city'] ?? '',
        'country' => $_POST['country'] ?? '',
        'postal_code' => $_POST['postal_code'] ?? ''
    ]);

    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';

    // Redirect immediately on success (before any HTML)
    if ($result['success']) {
        header("Location: login.php?registered=1");
        exit; // STOP execution
    }
}

// NOW include header (after all PHP processing)
require_once 'includes/header.php';
?>

<style>
    .auth-container {
        max-width: 500px;
        margin: 50px auto;
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
        margin-bottom: 5px;
        font-weight: 500;
        color: #555;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 5px;
        font-size: 16px;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #3B82F6;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
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
    }

    .btn-submit:hover {
        background: #2563EB;
    }

    .auth-footer {
        text-align: center;
        margin-top: 20px;
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
</style>

<div class="auth-container">
    <h2>📝 Create Your Account</h2>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-row">
            <div class="form-group">
                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password * (min 6 characters)</label>
            <input type="password" id="password" name="password" minlength="6" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone">
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="2"></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city">
            </div>
            <div class="form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" id="postal_code" name="postal_code">
            </div>
        </div>

        <div class="form-group">
            <label for="country">Country</label>
            <input type="text" id="country" name="country">
        </div>

        <button type="submit" class="btn-submit">Create Account</button>
    </form>

    <div class="auth-footer">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>