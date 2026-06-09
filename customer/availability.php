<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_login('customer');

$results = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['check_in']) && !empty($_GET['check_out'])) {
    $checkIn = $_GET['check_in'];
    $checkOut = $_GET['check_out'];

    $sql = "SELECT r.RoomID, r.RoomNumber, r.RoomStatus, rt.TypeName, rt.PricePerNight, rt.MaximumGuests
            FROM ROOM r
            JOIN ROOM_TYPE rt ON r.RoomTypeID = rt.RoomTypeID
            WHERE r.RoomStatus = 'Available'
            AND r.RoomID NOT IN (
                SELECT RoomID FROM RESERVATION
                WHERE ReservationStatus IN ('Pending', 'Confirmed', 'Checked-in')
                AND NOT (CheckOutDate <= :check_in OR CheckInDate >= :check_out)
            )
            ORDER BY r.RoomNumber ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['check_in' => $checkIn, 'check_out' => $checkOut]);
    $results = $stmt->fetchAll();

    if (!$results) {
        $message = 'No rooms available for the selected date range.';
    }
}

$pageTitle = 'Room Availability';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <div class="panel">
        <h1>Check Room Availability</h1>
        <form method="GET" class="form-card" style="margin:20px 0; max-width:100%;">
            <div class="form-group"><label>Check-In Date</label><input type="date" name="check_in" required value="<?php echo h($_GET['check_in'] ?? ''); ?>"></div>
            <div class="form-group"><label>Check-Out Date</label><input type="date" name="check_out" required value="<?php echo h($_GET['check_out'] ?? ''); ?>"></div>
            <button class="btn" type="submit">Check Availability</button>
        </form>

        <?php if ($message): ?><div class="alert alert-error"><?php echo h($message); ?></div><?php endif; ?>

        <div class="dashboard-grid">
            <?php foreach ($results as $room): ?>
                <div class="stat-box">
                    <h3><?php echo h($room['TypeName']); ?> - Room <?php echo h($room['RoomNumber']); ?></h3>
                    <p class="small-text">Price/Night: RM <?php echo h($room['PricePerNight']); ?></p>
                    <p class="small-text">Max Guests: <?php echo h($room['MaximumGuests']); ?></p>
                    <a class="btn" href="/hotel-reservation-system/customer/reserve.php?room_id=<?php echo h($room['RoomID']); ?>&check_in=<?php echo h($_GET['check_in'] ?? ''); ?>&check_out=<?php echo h($_GET['check_out'] ?? ''); ?>">Reserve This Room</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
