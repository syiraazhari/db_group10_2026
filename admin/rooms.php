<?php
// admin/rooms.php
require_once __DIR__ . '/../config/auth.php';
require_login('admin');

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$error = '';

$roomTypes = $pdo->query('SELECT RoomTypeID, TypeName FROM ROOM_TYPE ORDER BY TypeName')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomTypeId = (int)($_POST['RoomTypeID'] ?? 0);
    $roomNumber = trim($_POST['RoomNumber'] ?? '');
    $floorNumber = (int)($_POST['FloorNumber'] ?? 0);
    $roomStatus = trim($_POST['RoomStatus'] ?? 'Available');

    if ($roomTypeId <= 0 || $roomNumber === '' || $floorNumber <= 0 || $roomStatus === '') {
        $error = 'Please fill in all fields with valid values.';
    } else {
        if ($action === 'edit' && $id > 0) {
            $sql = "UPDATE ROOM SET RoomTypeID = :type, RoomNumber = :num, FloorNumber = :floor, RoomStatus = :status WHERE RoomID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'type' => $roomTypeId,
                'num' => $roomNumber,
                'floor' => $floorNumber,
                'status' => $roomStatus,
                'id' => $id,
            ]);
        } else {
            $sql = "INSERT INTO ROOM (RoomTypeID, RoomNumber, FloorNumber, RoomStatus) VALUES (:type, :num, :floor, :status)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'type' => $roomTypeId,
                'num' => $roomNumber,
                'floor' => $floorNumber,
                'status' => $roomStatus,
            ]);
        }

        header('Location: rooms.php');
        exit();
    }
}

if ($action === 'delete' && $id > 0) {
    $pdo->prepare('DELETE FROM ROOM WHERE RoomID = :id')->execute(['id' => $id]);
    header('Location: rooms.php');
    exit();
}

$editRow = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM ROOM WHERE RoomID = :id');
    $stmt->execute(['id' => $id]);
    $editRow = $stmt->fetch();
}

$sql = "
    SELECT r.RoomID, r.RoomNumber, r.FloorNumber, r.RoomStatus,
           rt.TypeName
    FROM ROOM r
    JOIN ROOM_TYPE rt ON r.RoomTypeID = rt.RoomTypeID
    ORDER BY r.RoomNumber ASC
";
$rooms = $pdo->query($sql)->fetchAll();

$pageTitle = 'Manage Rooms';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel">
        <h1>Rooms</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <h2><?php echo $editRow ? 'Edit Room' : 'Add New Room'; ?></h2>

        <form method="post">
            <label>Room Type</label>
            <select name="RoomTypeID" required>
                <option value="">-- Select Type --</option>
                <?php foreach ($roomTypes as $t): ?>
                    <option value="<?php echo (int)$t['RoomTypeID']; ?>" <?php echo ($editRow && $editRow['RoomTypeID'] == $t['RoomTypeID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($t['TypeName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Room Number</label>
            <input type="text" name="RoomNumber" required value="<?php echo htmlspecialchars($editRow['RoomNumber'] ?? ''); ?>">

            <label>Floor Number</label>
            <input type="number" name="FloorNumber" required min="1" value="<?php echo htmlspecialchars($editRow['FloorNumber'] ?? ''); ?>">

            <label>Status</label>
            <select name="RoomStatus" required>
                <?php $status = $editRow['RoomStatus'] ?? 'Available'; ?>
                <option value="Available" <?php echo $status === 'Available' ? 'selected' : ''; ?>>Available</option>
                <option value="Maintenance" <?php echo $status === 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                <option value="Cleaning" <?php echo $status === 'Cleaning' ? 'selected' : ''; ?>>Cleaning</option>
            </select>

            <button type="submit" class="btn-primary"><?php echo $editRow ? 'Update Room' : 'Add Room'; ?></button>
            <?php if ($editRow): ?>
                <a href="rooms.php" class="btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>

        <hr>

        <h2>Existing Rooms</h2>
        <div class="table-responsive">
            <table class="table-standard">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room Number</th>
                        <th>Type</th>
                        <th>Floor</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($rooms)): ?>
                    <tr><td colspan="6">No rooms found.</td></tr>
                <?php else: ?>
                    <?php foreach ($rooms as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['RoomID']); ?></td>
                            <td><?php echo htmlspecialchars($r['RoomNumber']); ?></td>
                            <td><?php echo htmlspecialchars($r['TypeName']); ?></td>
                            <td><?php echo htmlspecialchars($r['FloorNumber']); ?></td>
                            <td><?php echo htmlspecialchars($r['RoomStatus']); ?></td>
                            <td>
                                <a href="rooms.php?action=edit&id=<?php echo (int)$r['RoomID']; ?>" class="btn-small">Edit</a>
                                <a href="rooms.php?action=delete&id=<?php echo (int)$r['RoomID']; ?>" class="btn-small" onclick="return confirm('Delete this room?');">Delete</a>
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
