<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/helpers.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $icPassport = trim($_POST['ic_passport'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($fullName === '' || $icPassport === '' || $gender === '' || $dob === '' || $phone === '' || $address === '' || $username === '' || $password === '') {
        $errors[] = 'Please fill in all required fields.';
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (!$errors) {
        $check = $pdo->prepare("SELECT CustomerID FROM CUSTOMER WHERE Username = ? OR IC_PassportNo = ? LIMIT 1");
        $check->execute([$username, $icPassport]);
        if ($check->fetch()) {
            $errors[] = 'Username or IC/Passport already exists.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO CUSTOMER (FullName, IC_PassportNo, Gender, DateOfBirth, PhoneNo, Email, Address, Username, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fullName, $icPassport, $gender, $dob, $phone, $email, $address, $username, $password]);
            $success = 'Registration successful. You can now log in.';
        }
    }
}

$pageTitle = 'Customer Registration';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<div class="form-card">
    <h1>Customer Registration</h1>
    <p class="small-text">Create a customer account to access room booking features.</p>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo h($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo h($success); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group"><label>Full Name</label><input type="text" name="full_name" required></div>
        <div class="form-group"><label>IC / Passport No</label><input type="text" name="ic_passport" required></div>
        <div class="form-group"><label>Gender</label>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="form-group"><label>Date of Birth</label><input type="date" name="dob" required></div>
        <div class="form-group"><label>Phone Number</label><input type="text" name="phone" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email"></div>
        <div class="form-group"><label>Address</label><input type="text" name="address" required></div>
        <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <button class="btn" type="submit">Register</button>
        <div class="link-row">
            <a class="btn btn-secondary" href="/hotel-reservation-system/customer/login.php">Go to Login</a>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
