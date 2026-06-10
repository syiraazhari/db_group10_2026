<?php
// staff/invoices.php
require_once __DIR__ . '/../config/auth.php';
require_login('staff');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid_id'])) {
    $invoiceId = (int) $_POST['mark_paid_id'];

    if ($invoiceId <= 0) {
        $error = 'Invalid invoice ID.';
    } else {
        $sql = "SELECT InvoiceStatus FROM INVOICE WHERE InvoiceID = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $invoiceId]);
        $invoice = $stmt->fetch();

        if (!$invoice) {
            $error = 'Invoice not found.';
        } elseif ($invoice['InvoiceStatus'] === 'Paid') {
            $error = 'This invoice is already marked as paid.';
        } else {
            $updateSql = "
                UPDATE INVOICE
                SET InvoiceStatus = 'Paid',
                    PaymentDate = CURRENT_DATE
                WHERE InvoiceID = :id
            ";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute(['id' => $invoiceId]);

            $message = "Invoice #{$invoiceId} has been marked as paid.";
        }
    }
}

$listSql = "
    SELECT 
        i.InvoiceID,
        i.InvoiceDate,
        i.TotalAmount,
        i.InvoiceStatus,
        i.PaymentDate,
        r.ReservationID,
        r.CheckInDate,
        r.CheckOutDate,
        c.CustomerID,
        c.FullName AS CustomerName
    FROM INVOICE i
    JOIN RESERVATION r ON i.ReservationID = r.ReservationID
    JOIN CUSTOMER c ON r.CustomerID = c.CustomerID
    ORDER BY i.InvoiceDate DESC, i.InvoiceID DESC
";
$listStmt = $pdo->query($listSql);
$invoices = $listStmt->fetchAll();

$pageTitle = 'Staff Invoices';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel">
        <h1>Invoices</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table-standard">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Reservation #</th>
                        <th>Customer</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Invoice Date</th>
                        <th>Total (RM)</th>
                        <th>Status</th>
                        <th>Payment Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($invoices)): ?>
                    <tr>
                        <td colspan="10">No invoices found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($invoices as $inv): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inv['InvoiceID']); ?></td>
                            <td><?php echo htmlspecialchars($inv['ReservationID']); ?></td>
                            <td><?php echo htmlspecialchars($inv['CustomerName']); ?></td>
                            <td><?php echo htmlspecialchars($inv['CheckInDate']); ?></td>
                            <td><?php echo htmlspecialchars($inv['CheckOutDate']); ?></td>
                            <td><?php echo htmlspecialchars($inv['InvoiceDate']); ?></td>
                            <td><?php echo number_format($inv['TotalAmount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($inv['InvoiceStatus']); ?></td>
                            <td><?php echo htmlspecialchars($inv['PaymentDate'] ?? '-'); ?></td>
                            <td>
                                <?php if ($inv['InvoiceStatus'] === 'Paid'): ?>
                                    <button class="btn-small" disabled>Paid</button>
                                <?php else: ?>
                                    <form method="post" style="margin:0;">
                                        <input type="hidden" name="mark_paid_id" value="<?php echo (int)$inv['InvoiceID']; ?>">
                                        <button type="submit" class="btn-small">Mark as Paid</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
