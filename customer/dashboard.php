<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_login('customer');

$pageTitle = 'Customer Dashboard';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <div class="panel">
        <h1>Customer Dashboard</h1>
        <p>Welcome, <?php echo h($_SESSION['display_name']); ?>.</p>
        <p class="small-text">Phase 3 complete: customer registration, profile, rooms, reservation, history, and invoice pages are ready.</p>
        <div class="dashboard-grid">
            <div class="stat-box"><h3>Profile</h3><p>View and update your customer information.</p><a class="btn" href="/hotel-reservation-system/customer/profile.php">Open</a></div>
            <div class="stat-box"><h3>Rooms</h3><p>Browse available room types and room list.</p><a class="btn" href="/hotel-reservation-system/customer/rooms.php">Open</a></div>
            <div class="stat-box"><h3>Availability</h3><p>Check room availability by date range.</p><a class="btn" href="/hotel-reservation-system/customer/availability.php">Open</a></div>
            <div class="stat-box"><h3>New Reservation</h3><p>Create a room reservation.</p><a class="btn" href="/hotel-reservation-system/customer/reserve.php">Open</a></div>
            <div class="stat-box"><h3>Reservation History</h3><p>View and cancel your reservations.</p><a class="btn" href="/hotel-reservation-system/customer/history.php">Open</a></div>
            <div class="stat-box"><h3>Invoices</h3><p>View your generated invoices.</p><a class="btn" href="/hotel-reservation-system/customer/invoices.php">Open</a></div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
