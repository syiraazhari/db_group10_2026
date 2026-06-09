<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_login('customer');

$sql = "SELECT r.RoomID, r.RoomNumber, r.FloorNumber, r.RoomStatus, rt.TypeName, rt.Description, rt.PricePerNight, rt.MaximumGuests
        FROM ROOM r
        JOIN ROOM_TYPE rt ON r.RoomTypeID = rt.RoomTypeID
        ORDER BY r.RoomNumber ASC";
$rooms = $pdo->query($sql)->fetchAll();

$pageTitle = 'Room Listing';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<div class="container">
    <div class="panel">
        <h1>Room Listing</h1>
        <p class="small-text">Browse hotel rooms, price, description, and current status.</p>
        <div class="dashboard-grid">
            <?php foreach ($rooms as $room): ?>
                <div class="stat-box">
                    <h3><?php echo h($room['TypeName']); ?> - Room <?php echo h($room['RoomNumber']); ?></h3>
                    <p><?php echo h($room['Description']); ?></p>
                    <p class="small-text">Floor: <?php echo h($room['FloorNumber']); ?></p>
                    <p class="small-text">Max Guests: <?php echo h($room['MaximumGuests']); ?></p>
                    <p class="small-text">Price/Night: RM <?php echo h($room['PricePerNight']); ?></p>
                    <p class="small-text">Status: <?php echo h($room['RoomStatus']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
