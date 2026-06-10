<?php
// staff/reservations.php
require_once __DIR__ . '/../config/auth.php';
require_login('staff');

$sql = "
    SELECT 
        r.ReservationID,
        r.CheckInDate,
        r.CheckOutDate,
        r.NumberOfGuests,
        r.ReservationDate,
        r.ReservationStatus,
        c.FullName AS CustomerName,
        rm.RoomNumber,
        rt.TypeName AS RoomTypeName
    FROM RESERVATION r
    JOIN CUSTOMER c ON r.CustomerID = c.CustomerID
    JOIN ROOM rm ON r.RoomID = rm.RoomID
    JOIN ROOM_TYPE rt ON rm.RoomTypeID = rt.RoomTypeID
    ORDER BY r.ReservationDate DESC, r.ReservationID DESC
";
$reservations = $pdo->query($sql)->fetchAll();

$pageTitle = 'Staff Reservations';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel">
        <h1>Reservations</h1>
        <p class="small-text">
            View all reservations. Use Check-In / Check-Out actions from this list.
        </p>

        <div class="table-responsive">
            <table class="table-standard">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Room</th>
                        <th>Room Type</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Guests</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($reservations)): ?>
                    <tr>
                        <td colspan="9">No reservations found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($res['ReservationID']); ?></td>
                            <td><?php echo htmlspecialchars($res['CustomerName']); ?></td>
                            <td><?php echo htmlspecialchars($res['RoomNumber']); ?></td>
                            <td><?php echo htmlspecialchars($res['RoomTypeName']); ?></td>
                            <td><?php echo htmlspecialchars($res['CheckInDate']); ?></td>
                            <td><?php echo htmlspecialchars($res['CheckOutDate']); ?></td>
                            <td><?php echo htmlspecialchars($res['NumberOfGuests']); ?></td>
                            <td><?php echo htmlspecialchars($res['ReservationStatus']); ?></td>
                            <td>
                                <a href="checkin.php?id=<?php echo (int)$res['ReservationID']; ?>" class="btn-small">Check-In</a>
                                <a href="checkout.php?id=<?php echo (int)$res['ReservationID']; ?>" class="btn-small">Check-Out</a>
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
