<?php
require_once __DIR__ . '/config.php';

function is_logged_in(): bool {
    return isset($_SESSION['user_id'], $_SESSION['role']);
}

function require_login(string $role = null): void {
    if (!is_logged_in()) {
        header('Location: /hotel-reservation-system/customer/login.php');
        exit;
    }

    if ($role !== null && ($_SESSION['role'] ?? '') !== $role) {
        header('Location: /hotel-reservation-system/index.php');
        exit;
    }
}

function logout_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function get_user_table_config(string $role): ?array {
    $map = [
        'customer' => ['table' => 'CUSTOMER', 'id' => 'CustomerID', 'name' => 'FullName'],
        'staff' => ['table' => 'STAFF', 'id' => 'StaffID', 'name' => 'StaffName'],
        'admin' => ['table' => 'ADMIN', 'id' => 'AdminID', 'name' => 'Username'],
    ];
    return $map[$role] ?? null;
}

function login_user(PDO $pdo, string $role, string $username, string $password): array {
    $config = get_user_table_config($role);
    if (!$config) {
        return ['success' => False, 'message' => 'Invalid user role.'];
    }

    $sql = "SELECT * FROM {$config['table']} WHERE Username = :username LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user) {
        return ['success' => False, 'message' => 'Username not found.'];
    }

    if ($user['Password'] !== $password) {
        return ['success' => False, 'message' => 'Incorrect password.'];
    }

    $_SESSION['user_id'] = $user[$config['id']];
    $_SESSION['username'] = $user['Username'];
    $_SESSION['display_name'] = $user[$config['name']];
    $_SESSION['role'] = $role;

    return ['success' => True, 'message' => 'Login successful.'];
}

function redirect_by_role(string $role): void {
    $routes = [
        'customer' => '/hotel-reservation-system/customer/dashboard.php',
        'staff' => '/hotel-reservation-system/staff/dashboard.php',
        'admin' => '/hotel-reservation-system/admin/dashboard.php',
    ];

    header('Location: ' . ($routes[$role] ?? '/hotel-reservation-system/index.php'));
    exit;
}
?>