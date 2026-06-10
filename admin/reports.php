<?php
// admin/reports.php
require_once __DIR__ . '/../config/auth.php';
require_login('admin');

$monthlyReservations = $pdo->query("SELECT DATE_FORMAT(ReservationDate, '%Y-%m') AS month, COUNT(*) AS total FROM RESERVATION GROUP BY month ORDER BY month DESC")->fetchAll();
$monthlyRevenue = $pdo->query("SELECT DATE_FORMAT(InvoiceDate, '%Y-%m') AS month, COALESCE(SUM(TotalAmount),0) AS total FROM INVOICE WHERE InvoiceStatus = 'Paid' GROUP BY month ORDER BY month DESC")->fetchAll();

$pageTitle = 'Reports';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel">
        <h1>Reports</h1>

        <h2>Monthly Reservations</h2>
        <div class="table-responsive">
            <table class="table-standard">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Reservations</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($monthlyReservations)): ?>
                    <tr><td colspan="2">No reservation data yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($monthlyReservations as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['month']); ?></td>
                            <td><?php echo htmlspecialchars($row['total']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <h2>Monthly Paid Revenue</h2>
        <div class="table-responsive">
            <table class="table-standard">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Revenue (RM)</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($monthlyRevenue)): ?>
                    <tr><td colspan="2">No paid invoice data yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($monthlyRevenue as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['month']); ?></td>
                            <td><?php echo number_format($row['total'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
