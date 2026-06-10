<?php
// admin/customers.php
require_once __DIR__ . '/../config/auth.php';
require_login('admin');

$customers = $pdo->query('SELECT CustomerID, FullName, IC_PassportNo, PhoneNo, Email, Username FROM CUSTOMER ORDER BY FullName ASC')->fetchAll();

$pageTitle = 'Customer List';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel">
        <h1>Customers</h1>
        <p class="small-text">View registered customers and their contact information.</p>

        <div class="table-responsive">
            <table class="table-standard">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>IC / Passport</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Username</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($customers)): ?>
                    <tr><td colspan="6">No customers found.</td></tr>
                <?php else: ?>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['CustomerID']); ?></td>
                            <td><?php echo htmlspecialchars($c['FullName']); ?></td>
                            <td><?php echo htmlspecialchars($c['IC_PassportNo']); ?></td>
                            <td><?php echo htmlspecialchars($c['PhoneNo']); ?></td>
                            <td><?php echo htmlspecialchars($c['Email']); ?></td>
                            <td><?php echo htmlspecialchars($c['Username']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
