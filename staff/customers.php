<?php
// staff/customers.php
require_once __DIR__ . '/../config/auth.php';
require_login('staff');

$keyword = trim($_GET['q'] ?? '');
$customers = [];

if ($keyword !== '') {
    $sql = "
        SELECT CustomerID, FullName, IC_PassportNo, PhoneNo, Email
        FROM CUSTOMER
        WHERE FullName LIKE :kw OR IC_PassportNo LIKE :kw
        ORDER BY FullName ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['kw' => "%{$keyword}%"]);
    $customers = $stmt->fetchAll();
}

$pageTitle = 'Customer Search';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<div class="container">
    <div class="panel">
        <h1>Search Customers</h1>
        <p class="small-text">Search by customer full name or IC/passport number.</p>

        <form method="get" class="form-inline" style="margin-bottom: 15px;">
            <input type="text" name="q" placeholder="Enter name or IC/passport" value="<?php echo htmlspecialchars($keyword); ?>" style="min-width:260px;">
            <button type="submit" class="btn-primary">Search</button>
        </form>

        <div class="table-responsive">
            <table class="table-standard">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>IC / Passport</th>
                        <th>Phone</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($keyword === ''): ?>
                    <tr>
                        <td colspan="5">Enter a keyword and click Search.</td>
                    </tr>
                <?php elseif (empty($customers)): ?>
                    <tr>
                        <td colspan="5">No customers found for "<?php echo htmlspecialchars($keyword); ?>".</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $cust): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cust['CustomerID']); ?></td>
                            <td><?php echo htmlspecialchars($cust['FullName']); ?></td>
                            <td><?php echo htmlspecialchars($cust['IC_PassportNo']); ?></td>
                            <td><?php echo htmlspecialchars($cust['PhoneNo']); ?></td>
                            <td><?php echo htmlspecialchars($cust['Email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
