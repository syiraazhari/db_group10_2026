<?php
require_once __DIR__ . '/config/auth.php';
logout_user();
header('Location: /hotel-reservation-system/index.php');
exit;
?>