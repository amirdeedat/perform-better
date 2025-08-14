<?php
session_start();

// If already logged in, redirect to admin panel
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

// Hardcoded admin credentials (plain text for testing)
$admin_username = "admin";
$admin_password = "pass"; // Short password for testing

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = true;
    }
}

$error = isset($error);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — Canteen</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="wrap" style="max-width:420px;">
    <div class="card">
      <h2>🔑 Admin Login</h2>
      <?php if($error): ?><p style="color:#e05353;">Invalid username or password</p><?php endif; ?>
      <form method="POST">
        <div class="row"><label>Username</label><input name="username" required></div>
        <div class="row"><label>Password</label><input type="password" name="password" required></div>
        <button class="btn" type="submit">Login</button>
        <a class="btn" href="index.html" style="background:#9aa5ff;">Back</a>
      </form>
    </div>
  </div>
</body>
</html>
