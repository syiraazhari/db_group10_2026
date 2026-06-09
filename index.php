<?php
require_once 'config/config.php';
$pageTitle = 'Hotel Reservation System - Home';
include 'includes/header.php';
include 'includes/navbar.php';
?>
<section class="hero">
    <h1>Modern Hotel Reservation System</h1>
    <p>This is a diploma-level student project built with PHP, MySQL, HTML, CSS, and JavaScript.</p>
    <p>The system includes Customer, Staff, and Admin modules with reservation, invoice, and room management features.</p>
    <?php if (isset($_SESSION['role'])): ?>
        <div class="alert alert-success">
            Logged in as <strong><?php echo htmlspecialchars($_SESSION['display_name']); ?></strong> (<?php echo htmlspecialchars($_SESSION['role']); ?>).
        </div>
    <?php endif; ?>
    <div class="cards">
        <div class="card">
            <h3>Customer</h3>
            <p>Register, log in, browse rooms, make reservations, and view invoices.</p>
            <a class="btn" href="customer/login.php">Open</a>
        </div>
        <div class="card">
            <h3>Staff</h3>
            <p>Manage reservations, check-in/check-out guests, and update invoice status.</p>
            <a class="btn" href="staff/login.php">Open</a>
        </div>
        <div class="card">
            <h3>Admin</h3>
            <p>Manage rooms, room types, staff accounts, customer accounts, and reports.</p>
            <a class="btn" href="admin/login.php">Open</a>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
