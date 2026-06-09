<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_login('customer');

$customerId = (int) $_SESSION['user_id'];
$message = '';

if (isset($_GET['cancel']) && ctype_digit($_GET['cancel'])) {
    $reservationId = (int) $_GET['cancel'];
    $stmt = $pdo->prepare("UPDATE RESERVATION SET ReservationStatus = 'Cancelled' WHERE ReservationID = ? AND CustomerID = ? AND ReservationStatus NOT IN ('Checked-in', 'Checked-out', 'Cancelled')");
    $stmt->execute([$reservationId, $customerId]);
    $message = 'Reservation status updated.';
}

$sql = "SELECT r.ReservationID, r.CheckInDate, r.CheckOutDate, r.NumberOfGuests, r.ReservationDate, r.ReservationStatus,
               rm.RoomNumber, rt.TypeName
        FROM RESERVATION r
        JOIN ROOM rm ON r.RoomID = rm.RoomID
        JOIN ROOM_TYPE rt ON rm.RoomTypeID = rt.RoomTypeID
        WHERE r.CustomerID = ?
        ORDER BY r.ReservationID DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$customerId]);
$reservations = $stmt->fetchAll();

$pageTitle = 'Reservation History';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <div class="panel">
        <h1>Reservation History</h1>
        <?php if ($message): ?><div class="alert alert-success"><?php echo h($message); ?></div><?php endif; ?>
        <div class="dashboard-grid">
            <?php foreach ($reservations as $row): ?>
                <div class="stat-box">
                    <h3><?php echo h($row['TypeName']); ?> - Room <?php echo h($row['RoomNumber']); ?></h3>
                    <p class="small-text">Reservation ID: <?php echo h($row['ReservationID']); ?></p>
                    <p class="small-text">Check-In: <?php echo h($row['CheckInDate']); ?></p>
                    <p class="small-text">Check-Out: <?php echo h($row['CheckOutDate']); ?></p>
                    <p class="small-text">Guests: <?php echo h($row['NumberOfGuests']); ?></p>
                    <p class="small-text">Status: <?php echo h($row['ReservationStatus']); ?></p>
                    <?php if (!in_array($row['ReservationStatus'], ['Checked-in', 'Checked-out', 'Cancelled'], true)): ?>
                        <a class="btn btn-secondary" href="/hotel-reservation-system/customer/history.php?cancel=<?php echo h($row['ReservationID']); ?>">Cancel Reservation</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
