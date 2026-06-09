<nav class="navbar">
    <div class="logo">Hotel Reservation System</div>
    <ul>
        <li><a href="/hotel-reservation-system/index.php">Home</a></li>
        <li><a href="/hotel-reservation-system/customer/login.php">Customer</a></li>
        <li><a href="/hotel-reservation-system/staff/login.php">Staff</a></li>
        <li><a href="/hotel-reservation-system/admin/login.php">Admin</a></li>
        <?php if (isset($_SESSION['role'])): ?>
            <li><a href="/hotel-reservation-system/logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>
