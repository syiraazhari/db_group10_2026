<?php
// staff/login.php
require_once __DIR__ . '/../config/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
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
?>

<div class="container">
    <div class="panel panel-narrow">
        <h1>Staff Login</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="username">Staff Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn-primary">Login</button>
        </form>

        <p class="small-text" style="margin-top:10px;">
            Use the sample staff credentials from the database (for example: <strong>siti_staff / staff123</strong>).
        </p>

        <p style="margin-top: 10px;">
            <a href="../index.php">Back to Home</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
