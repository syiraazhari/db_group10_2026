<?php
// staff/checkout.php
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
        } elseif ($reservation['ReservationStatus'] === 'Checked-out') {
            $error = 'This reservation has already been checked-out.';
        } else {
            $pdo->beginTransaction();
            try {
                $updateRes = "
                    UPDATE RESERVATION
                    SET ReservationStatus = 'Checked-out'
                    WHERE ReservationID = :id
                ";
                $stmtRes = $pdo->prepare($updateRes);
                $stmtRes->execute(['id' => $reservationId]);

                $updateInv = "
                    UPDATE INVOICE
                    SET InvoiceStatus = 'Paid', PaymentDate = CURRENT_DATE
                    WHERE ReservationID = :id
                ";
                $stmtInv = $pdo->prepare($updateInv);
                $stmtInv->execute(['id' => $reservationId]);

                $pdo->commit();
                $message = "Reservation #{$reservationId} checked-out and invoice marked as paid.";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = 'Error during check-out process.';
            }
        }
    }
}

$pageTitle = 'Guest Check-Out';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel panel-narrow">
        <h1>Guest Check-Out</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="reservation_id">Reservation ID</label>
            <input type="number" id="reservation_id" name="reservation_id" required min="1" value="<?php echo $reservationId > 0 ? (int)$reservationId : ''; ?>">

            <button type="submit" class="btn-primary">Check-Out Guest</button>
        </form>

        <p style="margin-top: 10px;">
            <a href="reservations.php">Back to Reservations</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
