<?php
// admin/invoices.php
require_once __DIR__ . '/../config/auth.php';
require_login('admin');

$sql = "
    SELECT 
        i.InvoiceID,
        i.InvoiceNumber,
        i.InvoiceDate,
        i.TotalAmount,
        i.InvoiceStatus,
        i.PaymentDate,
        r.ReservationID,
        c.FullName AS CustomerName
    FROM INVOICE i
    JOIN RESERVATION r ON i.ReservationID = r.ReservationID
    JOIN CUSTOMER c ON r.CustomerID = c.CustomerID
    ORDER BY i.InvoiceDate DESC, i.InvoiceID DESC
";
$invoices = $pdo->query($sql)->fetchAll();

$pageTitle = 'All Invoices';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel">
        <h1>All Invoices</h1>
        <p class="small-text">Read-only list of all invoices in the system.</p>

        <div class="table-responsive">
            <table class="table-standard">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Number</th>
                        <th>Reservation #</th>
                        <th>Customer</th>
                        <th>Invoice Date</th>
                        <th>Total (RM)</th>
                        <th>Status</th>
                        <th>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($invoices)): ?>
                    <tr><td colspan="8">No invoices found.</td></tr>
                <?php else: ?>
                    <?php foreach ($invoices as $inv): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inv['InvoiceID']); ?></td>
                            <td><?php echo htmlspecialchars($inv['InvoiceNumber']); ?></td>
                            <td><?php echo htmlspecialchars($inv['ReservationID']); ?></td>
                            <td><?php echo htmlspecialchars($inv['CustomerName']); ?></td>
                            <td><?php echo htmlspecialchars($inv['InvoiceDate']); ?></td>
                            <td><?php echo number_format($inv['TotalAmount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($inv['InvoiceStatus']); ?></td>
                            <td><?php echo htmlspecialchars($inv['PaymentDate'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
