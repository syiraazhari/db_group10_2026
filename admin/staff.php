<?php
// admin/staff.php
require_once __DIR__ . '/../config/auth.php';
require_login('admin');

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['StaffName'] ?? '');
    $phone = trim($_POST['PhoneNo'] ?? '');
    $email = trim($_POST['Email'] ?? '');
    $username = trim($_POST['Username'] ?? '');
    $password = trim($_POST['Password'] ?? '');
    $position = trim($_POST['Position'] ?? 'Receptionist');

    if ($name === '' || $phone === '' || $email === '' || $username === '' || $password === '') {
        $error = 'Please fill in all required fields.';
    } else {
        if ($action === 'edit' && $id > 0) {
            $sql = "UPDATE STAFF SET StaffName = :name, PhoneNo = :phone, Email = :email, Username = :username, Password = :password, Position = :pos WHERE StaffID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'username' => $username,
                'password' => $password,
                'pos' => $position,
                'id' => $id,
            ]);
        } else {
            $sql = "INSERT INTO STAFF (StaffName, PhoneNo, Email, Username, Password, Position) VALUES (:name, :phone, :email, :username, :password, :pos)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'username' => $username,
                'password' => $password,
                'pos' => $position,
            ]);
        }

        header('Location: staff.php');
        exit();
    }
}

if ($action === 'delete' && $id > 0) {
    $pdo->prepare('DELETE FROM STAFF WHERE StaffID = :id')->execute(['id' => $id]);
    header('Location: staff.php');
    exit();
}

$editRow = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM STAFF WHERE StaffID = :id');
    $stmt->execute(['id' => $id]);
    $editRow = $stmt->fetch();
}

$staffList = $pdo->query('SELECT * FROM STAFF ORDER BY StaffName ASC')->fetchAll();

$pageTitle = 'Manage Staff Accounts';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel">
        <h1>Staff Accounts</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <h2><?php echo $editRow ? 'Edit Staff' : 'Add New Staff'; ?></h2>

        <form method="post">
            <label>Full Name</label>
            <input type="text" name="StaffName" required value="<?php echo htmlspecialchars($editRow['StaffName'] ?? ''); ?>">

            <label>Phone Number</label>
            <input type="text" name="PhoneNo" required value="<?php echo htmlspecialchars($editRow['PhoneNo'] ?? ''); ?>">

            <label>Email</label>
            <input type="email" name="Email" required value="<?php echo htmlspecialchars($editRow['Email'] ?? ''); ?>">

            <label>Username</label>
            <input type="text" name="Username" required value="<?php echo htmlspecialchars($editRow['Username'] ?? ''); ?>">

            <label>Password</label>
            <input type="text" name="Password" required value="<?php echo htmlspecialchars($editRow['Password'] ?? ''); ?>">

            <label>Position</label>
            <input type="text" name="Position" required value="<?php echo htmlspecialchars($editRow['Position'] ?? 'Receptionist'); ?>">

            <button type="submit" class="btn-primary"><?php echo $editRow ? 'Update Staff' : 'Add Staff'; ?></button>
            <?php if ($editRow): ?>
                <a href="staff.php" class="btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>

        <hr>

        <h2>Existing Staff</h2>
        <div class="table-responsive">
            <table class="table-standard">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($staffList)): ?>
                    <tr><td colspan="7">No staff found.</td></tr>
                <?php else: ?>
                    <?php foreach ($staffList as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['StaffID']); ?></td>
                            <td><?php echo htmlspecialchars($s['StaffName']); ?></td>
                            <td><?php echo htmlspecialchars($s['PhoneNo']); ?></td>
                            <td><?php echo htmlspecialchars($s['Email']); ?></td>
                            <td><?php echo htmlspecialchars($s['Username']); ?></td>
                            <td><?php echo htmlspecialchars($s['Position']); ?></td>
                            <td>
                                <a href="staff.php?action=edit&id=<?php echo (int)$s['StaffID']; ?>" class="btn-small">Edit</a>
                                <a href="staff.php?action=delete&id=<?php echo (int)$s['StaffID']; ?>" class="btn-small" onclick="return confirm('Delete this staff account?');">Delete</a>
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
