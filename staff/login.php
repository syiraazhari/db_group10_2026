<?php
require_once __DIR__ . '/../config/auth.php';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff') {
    redirect_by_role('staff');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $result = login_user($pdo, 'staff', $username, $password);
        if ($result['success']) {
            redirect_by_role('staff');
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Staff Login';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<div class="form-card">
    <h1>Staff Login</h1>
    <p class="small-text">Use the sample credentials from the imported database for this role.</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
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
            <a class="btn btn-secondary" href="/hotel-reservation-system/index.php">Back Home</a>
        </div>
    </form>

    <div class="panel" style="margin:20px 0 0 0; padding:20px;">
        <h3>Sample Staff Login</h3>
        <p class="small-text">Username: <strong>siti_staff</strong></p>
        <p class="small-text">Password: <strong>staff123</strong></p>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
