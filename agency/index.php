<?php
session_start();
include('../includes/db.php');

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM agencies WHERE email = '$email'";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $agency = $res->fetch_assoc();
        if (password_verify($password, $agency['password'])) {
            $_SESSION['agency_logged_in'] = true;
            $_SESSION['agency_id'] = $agency['id'];
            $_SESSION['agency_name'] = $agency['name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Agency not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agency Login</title>
    <style>
        body { background: #1a1a1a; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; }
        .login-card { background: white; padding: 3rem; border-radius: 20px; width: 100%; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .login-card h2 { margin-bottom: 2rem; color: #ff3c00; text-align: center; }
        input { width: 100%; padding: 1rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 10px; box-sizing: border-box;}
        button { width: 100%; padding: 1rem; background: #ff3c00; color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Agency Portal</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Agency Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login to Dashboard</button>
            <?php if(isset($error)) echo "<p style='color:red; margin-top:10px;'>$error</p>"; ?>
        </form>
    </div>
</body>
</html>
