<?php
// admin/dashboard.php
require_once __DIR__ . '/../config/auth.php';
require_login('admin');

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$totalRooms = $pdo->query('SELECT COUNT(*) AS c FROM ROOM')->fetch()['c'] ?? 0;
$totalReservations = $pdo->query('SELECT COUNT(*) AS c FROM RESERVATION')->fetch()['c'] ?? 0;
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(TotalAmount),0) AS t FROM INVOICE WHERE InvoiceStatus = 'Paid'")->fetch()['t'] ?? 0;
?>

<div class="container">
    <div class="panel">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['display_name'] ?? $_SESSION['username'] ?? 'Admin'); ?>.</p>
        <p class="small-text">Manage rooms, room types, staff, customers, invoices, and view reports.</p>

        <div class="dashboard-grid">
            <div class="stat-box">
                <h3>Total Rooms</h3>
                <p><?php echo (int)$totalRooms; ?></p>
                <a href="rooms.php" class="btn-primary">Manage Rooms</a>
            </div>

            <div class="stat-box">
                <h3>Total Reservations</h3>
                <p><?php echo (int)$totalReservations; ?></p>
                <a href="reports.php" class="btn-primary">View Reports</a>
            </div>

            <div class="stat-box">
                <h3>Paid Revenue (RM)</h3>
                <p><?php echo number_format($totalRevenue, 2); ?></p>
                <a href="invoices.php" class="btn-primary">View Invoices</a>
            </div>

            <div class="stat-box">
                <h3>Room Types</h3>
                <p>Configure room categories and pricing.</p>
                <a href="room_types.php" class="btn-primary">Manage Types</a>
            </div>

            <div class="stat-box">
                <h3>Staff Accounts</h3>
                <p>Manage staff login credentials.</p>
                <a href="staff.php" class="btn-primary">Manage Staff</a>
            </div>

            <div class="stat-box">
                <h3>Customers</h3>
                <p>View customer list and account status.</p>
                <a href="customers.php" class="btn-primary">View Customers</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
