<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_login('customer');

$customerId = (int) $_SESSION['user_id'];
$sql = "SELECT i.InvoiceID, i.InvoiceNumber, i.InvoiceDate, i.TotalAmount, i.InvoiceStatus, i.PaymentDate,
               r.ReservationID, rm.RoomNumber, rt.TypeName
        FROM INVOICE i
        JOIN RESERVATION r ON i.ReservationID = r.ReservationID
        JOIN ROOM rm ON r.RoomID = rm.RoomID
        JOIN ROOM_TYPE rt ON rm.RoomTypeID = rt.RoomTypeID
        WHERE r.CustomerID = ?
        ORDER BY i.InvoiceID DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$customerId]);
$invoices = $stmt->fetchAll();

$pageTitle = 'My Invoices';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <div class="panel">
        <h1>My Invoices</h1>
        <div class="dashboard-grid">
            <?php foreach ($invoices as $invoice): ?>
                <div class="stat-box">
                    <h3><?php echo h($invoice['InvoiceNumber']); ?></h3>
                    <p class="small-text">Reservation ID: <?php echo h($invoice['ReservationID']); ?></p>
                    <p class="small-text">Room: <?php echo h($invoice['TypeName']); ?> - <?php echo h($invoice['RoomNumber']); ?></p>
                    <p class="small-text">Invoice Date: <?php echo h($invoice['InvoiceDate']); ?></p>
                    <p class="small-text">Total Amount: RM <?php echo h($invoice['TotalAmount']); ?></p>
                    <p class="small-text">Status: <?php echo h($invoice['InvoiceStatus']); ?></p>
                    <p class="small-text">Payment Date: <?php echo h($invoice['PaymentDate'] ?: 'Not Paid Yet'); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
