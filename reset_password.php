<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['reset_email'])) {
    header("Location: request_reset.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];

        $update_query = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
        if (mysqli_query($conn, $update_query)) {
            $success = "Password successfully reset! Redirection to login...";
            unset($_SESSION['reset_email']);
            header("refresh:3;url=index.php");
        } else {
            $error = "Error updating password: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" href="logo/urbanride1.ico" sizes="1080x1080" type="image/x-icon">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .reset-container {
            max-width: 450px;
            margin: 100px auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            text-align: center;
        }
        .error-msg {
            color: #ff4757;
            margin-bottom: 20px;
            background: rgba(255, 71, 87, 0.1);
            padding: 10px;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .success-msg {
            color: #2ed573;
            margin-bottom: 20px;
            background: rgba(46, 213, 115, 0.1);
            padding: 10px;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .input-group {
            margin-bottom: 20px;
            position: relative;
        }
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
        }
        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            transition: all 0.3s ease;
        }
        .input-group input:focus {
            border-color: #FF4D01;
            background: rgba(255, 255, 255, 0.1);
            outline: none;
        }
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #FF4D01 0%, #FF8A00 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 77, 1, 0.3);
        }
    </style>
</head>
<body style="background: #0F1111; font-family: 'Outfit', sans-serif;">
    <div class="reset-container">
        <h2 style="font-size: 2rem; margin-bottom: 10px;">New Password</h2>
        <p style="color: rgba(255, 255, 255, 0.6); margin-bottom: 30px;">Set a strong password for your account</p>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-msg"><?php echo $success; ?></div>
        <?php else: ?>
            <form action="" method="POST">
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="New Password" required minlength="6">
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" required minlength="6">
                </div>
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
