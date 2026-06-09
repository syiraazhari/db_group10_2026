<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_login('customer');

$customerId = (int) $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($fullName === '' || $phone === '' || $address === '') {
        $error = 'Full name, phone number, and address are required.';
    } else {
        $stmt = $pdo->prepare("UPDATE CUSTOMER SET FullName = ?, PhoneNo = ?, Email = ?, Address = ? WHERE CustomerID = ?");
        $stmt->execute([$fullName, $phone, $email, $address, $customerId]);
        $_SESSION['display_name'] = $fullName;
        $success = 'Profile updated successfully.';
    }
}

$stmt = $pdo->prepare("SELECT * FROM CUSTOMER WHERE CustomerID = ?");
$stmt->execute([$customerId]);
$customer = $stmt->fetch();

$pageTitle = 'My Profile';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<div class="form-card">
    <h1>My Profile</h1>
    <p class="small-text">Update your customer profile details.</p>

    <?php if ($error): ?><div class="alert alert-error"><?php echo h($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo h($success); ?></div><?php endif; ?>

    <form method="POST">
        <div class="form-group"><label>Full Name</label><input type="text" name="full_name" value="<?php echo h($customer['FullName'] ?? ''); ?>" required></div>
        <div class="form-group"><label>Phone Number</label><input type="text" name="phone" value="<?php echo h($customer['PhoneNo'] ?? ''); ?>" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo h($customer['Email'] ?? ''); ?>"></div>
        <div class="form-group"><label>Address</label><input type="text" name="address" value="<?php echo h($customer['Address'] ?? ''); ?>" required></div>
        <button type="submit" class="btn">Update Profile</button>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
