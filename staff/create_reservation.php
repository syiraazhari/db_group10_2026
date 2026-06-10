<?php
// staff/create_reservation.php
require_once __DIR__ . '/../config/auth.php';
require_login('staff');

$error = '';
$success = '';

$customers = $pdo->query("
    SELECT CustomerID, FullName 
    FROM CUSTOMER 
    ORDER BY FullName ASC
")->fetchAll();

$rooms = $pdo->query("
    SELECT r.RoomID, r.RoomNumber, rt.TypeName
    FROM ROOM r
    JOIN ROOM_TYPE rt ON r.RoomTypeID = rt.RoomTypeID
    WHERE r.RoomStatus = 'Available'
    ORDER BY r.RoomNumber ASC
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = (int)($_POST['customer_id'] ?? 0);
    $roomId = (int)($_POST['room_id'] ?? 0);
    $checkIn = trim($_POST['check_in'] ?? '');
    $checkOut = trim($_POST['check_out'] ?? '');
    $guests = (int)($_POST['guests'] ?? 1);

    if ($customerId <= 0 || $roomId <= 0 || $checkIn === '' || $checkOut === '' || $guests <= 0) {
        $error = 'Please complete all reservation fields.';
    } else {
        $conflict = $pdo->prepare("
            SELECT ReservationID 
            FROM RESERVATION
            WHERE RoomID = ?
              AND ReservationStatus IN ('Pending', 'Confirmed', 'Checked-in')
              AND NOT (CheckOutDate <= ? OR CheckInDate >= ?)
            LIMIT 1
        ");
        $conflict->execute([$roomId, $checkIn, $checkOut]);

        if ($conflict->fetch()) {
            $error = 'Selected room is not available for those dates.';
        } else {
            $staffId = (int)($_SESSION['user_id'] ?? 0);

            $stmt = $pdo->prepare("
                INSERT INTO RESERVATION
                    (CustomerID, RoomID, StaffID, CheckInDate, CheckOutDate,
                     NumberOfGuests, ReservationDate, ReservationStatus)
                VALUES
                    (?, ?, ?, ?, ?, ?, CURDATE(), 'Pending')
            ");
            $stmt->execute([$customerId, $roomId, $staffId, $checkIn, $checkOut, $guests]);

            $reservationId = (int)$pdo->lastInsertId();

            $invoiceStmt = $pdo->prepare("
                SELECT rt.PricePerNight
                FROM ROOM r
                JOIN ROOM_TYPE rt ON r.RoomTypeID = rt.RoomTypeID
                WHERE r.RoomID = ?
            ");
            $invoiceStmt->execute([$roomId]);
            $priceRow = $invoiceStmt->fetch();

            $days = max(1, (strtotime($checkOut) - strtotime($checkIn)) / 86400);
            $total = ($priceRow['PricePerNight'] ?? 0) * $days;

            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad((string)$reservationId, 4, '0', STR_PAD_LEFT);

            $insertInvoice = $pdo->prepare("
                INSERT INTO INVOICE
                    (ReservationID, InvoiceNumber, InvoiceDate, TotalAmount, InvoiceStatus, PaymentDate)
                VALUES
                    (?, ?, CURDATE(), ?, 'Unpaid', NULL)
            ");
            $insertInvoice->execute([$reservationId, $invoiceNumber, $total]);

            $success = "Walk-in reservation #{$reservationId} created and invoice generated.";
        }
    }
}

$pageTitle = 'Create Walk-In Reservation';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel">
        <h1>Create Walk-In Reservation</h1>
        <p class="small-text">Create a reservation for an existing customer at the front desk.</p>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Customer</label>
            <select name="customer_id" required>
                <option value="">-- Select Customer --</option>
                <?php foreach ($customers as $c): ?>
                    <option value="<?php echo (int)$c['CustomerID']; ?>">
                        <?php echo htmlspecialchars($c['FullName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Room</label>
            <select name="room_id" required>
                <option value="">-- Select Room --</option>
                <?php foreach ($rooms as $r): ?>
                    <option value="<?php echo (int)$r['RoomID']; ?>">
                        <?php echo htmlspecialchars($r['RoomNumber'] . ' - ' . $r['TypeName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Check-In Date</label>
            <input type="date" name="check_in" required>

            <label>Check-Out Date</label>
            <input type="date" name="check_out" required>

            <label>Number of Guests</label>
            <input type="number" name="guests" min="1" value="1" required>

            <button type="submit" class="btn-primary">Create Reservation</button>
        </form>

        <p style="margin-top: 10px;">
            <a href="reservations.php">Back to Reservations</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
