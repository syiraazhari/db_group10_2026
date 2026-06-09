<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: login.php');
    exit();
}
include '../includes/header.php';
?>
<div class="container">
    <div class="card">
        <h1 style="color:#6ee7ff;">Staff Invoices</h1>
        <p>This page will allow staff to manage invoice payments.</p>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
