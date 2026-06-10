<?php
// admin/room_types.php
require_once __DIR__ . '/../config/auth.php';
require_login('admin');

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $typeName = trim($_POST['TypeName'] ?? '');
    $description = trim($_POST['Description'] ?? '');
    $price = (float)($_POST['PricePerNight'] ?? 0);
    $maxGuests = (int)($_POST['MaximumGuests'] ?? 1);

    if ($typeName === '' || $description === '' || $price <= 0 || $maxGuests <= 0) {
        $error = 'Please fill in all fields with valid values.';
    } else {
        if ($action === 'edit' && $id > 0) {
            $sql = "UPDATE ROOM_TYPE SET TypeName = :name, Description = :desc, PricePerNight = :price, MaximumGuests = :max WHERE RoomTypeID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $typeName,
                'desc' => $description,
                'price' => $price,
                'max' => $maxGuests,
                'id' => $id,
            ]);
        } else {
            $sql = "INSERT INTO ROOM_TYPE (TypeName, Description, PricePerNight, MaximumGuests) VALUES (:name, :desc, :price, :max)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $typeName,
                'desc' => $description,
                'price' => $price,
                'max' => $maxGuests,
            ]);
        }

        header('Location: room_types.php');
        exit();
    }
}

if ($action === 'delete' && $id > 0) {
    $pdo->prepare('DELETE FROM ROOM_TYPE WHERE RoomTypeID = :id')->execute(['id' => $id]);
    header('Location: room_types.php');
    exit();
}

$editRow = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM ROOM_TYPE WHERE RoomTypeID = :id');
    $stmt->execute(['id' => $id]);
    $editRow = $stmt->fetch();
}

$types = $pdo->query('SELECT * FROM ROOM_TYPE ORDER BY PricePerNight ASC')->fetchAll();

$pageTitle = 'Manage Room Types';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel">
        <h1>Room Types</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <h2><?php echo $editRow ? 'Edit Room Type' : 'Add New Room Type'; ?></h2>

        <form method="post">
            <label>Type Name</label>
            <input type="text" name="TypeName" required value="<?php echo htmlspecialchars($editRow['TypeName'] ?? ''); ?>">

            <label>Description</label>
            <textarea name="Description" required><?php echo htmlspecialchars($editRow['Description'] ?? ''); ?></textarea>

            <label>Price Per Night (RM)</label>
            <input type="number" step="0.01" name="PricePerNight" required value="<?php echo htmlspecialchars($editRow['PricePerNight'] ?? ''); ?>">

            <label>Maximum Guests</label>
            <input type="number" name="MaximumGuests" required min="1" value="<?php echo htmlspecialchars($editRow['MaximumGuests'] ?? ''); ?>">

            <button type="submit" class="btn-primary"><?php echo $editRow ? 'Update Type' : 'Add Type'; ?></button>
            <?php if ($editRow): ?>
                <a href="room_types.php" class="btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>

        <hr>

        <h2>Existing Room Types</h2>
        <div class="table-responsive">
            <table class="table-standard">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price (RM)</th>
                        <th>Max Guests</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($types)): ?>
                    <tr><td colspan="6">No room types found.</td></tr>
                <?php else: ?>
                    <?php foreach ($types as $t): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t['RoomTypeID']); ?></td>
                            <td><?php echo htmlspecialchars($t['TypeName']); ?></td>
                            <td><?php echo htmlspecialchars($t['Description']); ?></td>
                            <td><?php echo number_format($t['PricePerNight'], 2); ?></td>
                            <td><?php echo htmlspecialchars($t['MaximumGuests']); ?></td>
                            <td>
                                <a href="room_types.php?action=edit&id=<?php echo (int)$t['RoomTypeID']; ?>" class="btn-small">Edit</a>
                                <a href="room_types.php?action=delete&id=<?php echo (int)$t['RoomTypeID']; ?>" class="btn-small" onclick="return confirm('Delete this room type?');">Delete</a>
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
