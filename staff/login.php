<?php
session_start();
require_once '../config/config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === "" || $password === "") {
        $error = "Please enter username and password.";
    } else {
        $sql = "SELECT * FROM STAFF WHERE Username = ? AND Password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $_SESSION['user_id'] = $row['StaffID'];
            $_SESSION['username'] = $row['Username'];
            $_SESSION['role'] = 'staff';
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid staff username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0a1931;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: rgba(255,255,255,0.08);
            padding: 30px;
            border-radius: 15px;
            width: 340px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #35d7ff;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        .error {
            color: #ff6b6b;
            margin-bottom: 8px;
        }
        a {
            color: #6ee7ff;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Staff Login</h2>
    <?php if ($error !== ""): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Staff Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <p><a href="../index.php">Back to Home</a></p>
</div>
</body>
</html>
