<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: login.php');
    exit();
}
include '../includes/header.php';
?>
<div class="container">
    <div class="card">
        <h1 style="color:#6ee7ff; font-size:48px; margin-bottom:10px;">Staff Dashboard</h1>
        <p style="font-size:22px;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>.</p>
        <p>Manage reservations, search customer records, check-in/check-out, and invoices.</p>
        <div class="grid">
            <div class="box">
                <h3>Reservations</h3>
                <p>View, create, update, and cancel reservations.</p>
                <a href="reservations.php" class="btn">Open</a>
            </div>
            <div class="box">
                <h3>Customers</h3>
                <p>Search customer records by name or IC/passport.</p>
                <a href="customers.php" class="btn">Open</a>
            </div>
            <div class="box">
                <h3>Check-In / Check-Out</h3>
                <p>Update guest reservation status during arrival and departure.</p>
                <a href="checkin.php" class="btn">Open</a>
            </div>
            <div class="box">
                <h3>Invoices</h3>
                <p>View invoices and mark them as paid.</p>
                <a href="invoices.php" class="btn">Open</a>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
