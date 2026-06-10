<?php
// staff/dashboard.php
require_once __DIR__ . '/../config/auth.php';
require_login('staff');

$pageTitle = 'Staff Dashboard';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel">
        <h1>Staff Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['display_name'] ?? $_SESSION['username'] ?? 'Staff'); ?>.</p>
        <p class="small-text">
            Manage reservations, check-in/check-out guests, search customers, and update invoice payments.
        </p>

        <div class="dashboard-grid">
            <div class="stat-box">
                <h3>Reservations</h3>
                <p>View, create (walk-in), update, and cancel reservations.</p>
                <a href="reservations.php" class="btn-primary">Open</a>
            </div>

            <div class="stat-box">
                <h3>Check-In / Check-Out</h3>
                <p>Update guest reservation status during arrival and departure.</p>
                <a href="reservations.php" class="btn-primary">Open</a>
            </div>

            <div class="stat-box">
                <h3>Customer Search</h3>
                <p>Search customer records by name or IC/passport.</p>
                <a href="customers.php" class="btn-primary">Open</a>
            </div>

            <div class="stat-box">
                <h3>Invoices</h3>
                <p>View invoices and mark them as paid.</p>
                <a href="invoices.php" class="btn-primary">Open</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
