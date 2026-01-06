<?php
session_start();
include_once __DIR__ . '/config.php';

// Redirect already-logged-in users directly to dashboard
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        // Prepared statement to prevent SQL injection
        $stmt = $conn->prepare('SELECT id, username FROM admins WHERE username = ? AND password = ? LIMIT 1');
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $_SESSION['user'] = $result->fetch_assoc()['username'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid credentials.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login — Rani Beauty Clinic</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" />

    <!-- Stylesheet -->
    <link rel="stylesheet" href="style.css" />
</head>
<body class="light-bg">
    <div class="login-wrapper">
        <form class="login-box" method="POST" action="">
            <h1 class="login-title">ADMIN LOGIN</h1>
            <?php if ($error !== ''): ?>
                <div class="error-msg"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required />

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required />

            <button type="submit" class="btn-primary">LOGIN</button>
        </form>
    </div>
</body>
</html>
