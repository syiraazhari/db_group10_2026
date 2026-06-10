<?php
// staff/checkin.php
require_once __DIR__ . '/../config/auth.php';
require_login('staff');

$message = '';
$error = '';

$reservationId = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationId = (int)($_POST['reservation_id'] ?? 0);

    if ($reservationId <= 0) {
        $error = 'Invalid reservation ID.';
    } else {
        $sql = "SELECT ReservationStatus FROM RESERVATION WHERE ReservationID = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $reservationId]);
        $reservation = $stmt->fetch();

        if (!$reservation) {
            $error = 'Reservation not found.';
        } elseif ($reservation['ReservationStatus'] === 'Checked-in') {
            $error = 'This reservation is already checked-in.';
        } elseif ($reservation['ReservationStatus'] === 'Checked-out') {
            $error = 'This reservation has already been checked-out.';
        } else {
            $updateSql = "
                UPDATE RESERVATION
                SET ReservationStatus = 'Checked-in'
                WHERE ReservationID = :id
            ";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute(['id' => $reservationId]);

            $message = "Reservation #{$reservationId} checked-in successfully.";
        }
    }
}

$pageTitle = 'Guest Check-In';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel panel-narrow">
        <h1>Guest Check-In</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="reservation_id">Reservation ID</label>
            <input type="number" id="reservation_id" name="reservation_id" required min="1" value="<?php echo $reservationId > 0 ? (int)$reservationId : ''; ?>">

            <button type="submit" class="btn-primary">Check-In Guest</button>
        </form>

        <p style="margin-top: 10px;">
            <a href="reservations.php">Back to Reservations</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
