<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_login('customer');

$customerId = (int) $_SESSION['user_id'];
$success = '';
$error = '';

$rooms = $pdo->query("SELECT r.RoomID, r.RoomNumber, rt.TypeName FROM ROOM r JOIN ROOM_TYPE rt ON r.RoomTypeID = rt.RoomTypeID WHERE r.RoomStatus = 'Available' ORDER BY r.RoomNumber ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = (int) ($_POST['room_id'] ?? 0);
    $checkIn = trim($_POST['check_in'] ?? '');
    $checkOut = trim($_POST['check_out'] ?? '');
    $guests = (int) ($_POST['guests'] ?? 1);

    if ($roomId <= 0 || $checkIn === '' || $checkOut === '' || $guests <= 0) {
        $error = 'Please complete all reservation fields.';
    } else {
        $conflict = $pdo->prepare("SELECT ReservationID FROM RESERVATION WHERE RoomID = ? AND ReservationStatus IN ('Pending', 'Confirmed', 'Checked-in') AND NOT (CheckOutDate <= ? OR CheckInDate >= ?) LIMIT 1");
        $conflict->execute([$roomId, $checkIn, $checkOut]);
        if ($conflict->fetch()) {
            $error = 'Selected room is not available for those dates.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO RESERVATION (CustomerID, RoomID, StaffID, CheckInDate, CheckOutDate, NumberOfGuests, ReservationDate, ReservationStatus) VALUES (?, ?, NULL, ?, ?, ?, CURDATE(), 'Pending')");
            $stmt->execute([$customerId, $roomId, $checkIn, $checkOut, $guests]);
            $reservationId = (int) $pdo->lastInsertId();

            $invoiceStmt = $pdo->prepare("SELECT rt.PricePerNight FROM ROOM r JOIN ROOM_TYPE rt ON r.RoomTypeID = rt.RoomTypeID WHERE r.RoomID = ?");
            $invoiceStmt->execute([$roomId]);
            $priceRow = $invoiceStmt->fetch();
            $days = max(1, (strtotime($checkOut) - strtotime($checkIn)) / 86400);
            $total = ($priceRow['PricePerNight'] ?? 0) * $days;
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad((string)$reservationId, 4, '0', STR_PAD_LEFT);

            $insertInvoice = $pdo->prepare("INSERT INTO INVOICE (ReservationID, InvoiceNumber, InvoiceDate, TotalAmount, InvoiceStatus, PaymentDate) VALUES (?, ?, CURDATE(), ?, 'Unpaid', NULL)");
            $insertInvoice->execute([$reservationId, $invoiceNumber, $total]);

            $success = 'Reservation created successfully and invoice generated.';
        }
    }
}

$pageTitle = 'Make Reservation';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<div class="form-card">
    <h1>Make Reservation</h1>
    <p class="small-text">Create a new reservation as a customer.</p>

    <?php if ($error): ?><div class="alert alert-error"><?php echo h($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo h($success); ?></div><?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Select Room</label>
            <select name="room_id" required>
                <option value="">Choose Room</option>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?php echo h($room['RoomID']); ?>" <?php echo (($_GET['room_id'] ?? '') == $room['RoomID']) ? 'selected' : ''; ?>>
                        <?php echo h($room['TypeName'] . ' - Room ' . $room['RoomNumber']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Check-In Date</label><input type="date" name="check_in" required value="<?php echo h($_GET['check_in'] ?? ''); ?>"></div>
        <div class="form-group"><label>Check-Out Date</label><input type="date" name="check_out" required value="<?php echo h($_GET['check_out'] ?? ''); ?>"></div>
        <div class="form-group"><label>Number of Guests</label><input type="number" name="guests" min="1" required value="1"></div>
        <button type="submit" class="btn">Create Reservation</button>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
