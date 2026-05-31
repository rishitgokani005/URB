<?php
session_start();
include('../includes/db.php');

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hardcoded central admin for now, or check a separate admins table
    // For simplicity, let's assume one super admin
    if ($email === 'admin@urbanride.com' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Central Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #0f1111; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { background: white; padding: 3rem; border-radius: 20px; width: 100%; max-width: 400px; }
        .login-card h2 { margin-bottom: 2rem; color: #ff3c00; text-align: center; }
        input { width: 100%; padding: 1rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 10px; }
        button { width: 100%; padding: 1rem; background: #ff3c00; color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Central Admin</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
            <?php if(isset($error)) echo "<p style='color:red; margin-top:10px;'>$error</p>"; ?>
        </form>
    </div>
</body>
</html>
