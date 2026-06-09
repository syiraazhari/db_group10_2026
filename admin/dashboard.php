<?php
require_once __DIR__ . '/../config/auth.php';
require_login('admin');

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <div class="panel">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['display_name']); ?>.</p>
        <p class="small-text">Phase 2 complete: admin login, session, and protected dashboard are active.</p>
        <div class="dashboard-grid">
            <div class="stat-box"><h3>Room Types</h3><p>Next phase: add, edit, and delete room types.</p></div>
            <div class="stat-box"><h3>Rooms</h3><p>Next phase: manage room records and status.</p></div>
            <div class="stat-box"><h3>Staff</h3><p>Next phase: manage staff accounts.</p></div>
            <div class="stat-box"><h3>Reports</h3><p>Next phase: reservation, revenue, and occupancy reports.</p></div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
