<?php
require_once __DIR__ . '/../config/auth.php';
require_login('staff');

$pageTitle = 'Staff Dashboard';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <div class="panel">
        <h1>Staff Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['display_name']); ?>.</p>
        <p class="small-text">Phase 2 complete: staff login, session, and role protection are active.</p>
        <div class="dashboard-grid">
            <div class="stat-box"><h3>Reservations</h3><p>Next phase: manage reservation records.</p></div>
            <div class="stat-box"><h3>Check-In</h3><p>Next phase: mark guests as checked in.</p></div>
            <div class="stat-box"><h3>Check-Out</h3><p>Next phase: finalize invoices and guest checkout.</p></div>
            <div class="stat-box"><h3>Invoices</h3><p>Next phase: update payment status.</p></div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
