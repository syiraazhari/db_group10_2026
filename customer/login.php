<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'customer') {
    redirect_by_role('customer');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $result = login_user($pdo, 'customer', $username, $password);
        if ($result['success']) {
            redirect_by_role('customer');
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Customer Login';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<div class="form-card">
    <h1>Customer Login</h1>
    <p class="small-text">Log in to browse rooms, make reservations, and view invoices.</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo h($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
        </div>
        <button type="submit" class="btn">Login</button>
        <div class="link-row">
            <a class="btn btn-secondary" href="/hotel-reservation-system/customer/register.php">Register New Account</a>
        </div>
    </form>

    <div class="panel" style="margin:20px 0 0 0; padding:20px;">
        <h3>Sample Customer Login</h3>
        <p class="small-text">Username: <strong>adief01</strong></p>
        <p class="small-text">Password: <strong>customer123</strong></p>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
