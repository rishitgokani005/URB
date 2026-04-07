<?php
session_start();
include('includes/db.php');

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Check if user exists with this email and phone
    $query = "SELECT * FROM users WHERE email='$email' AND phone='$phone'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['reset_email'] = $email;
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Invalid Email or Phone number. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
        .back-link {
            display: block;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link:hover {
            color: white;
        }
    </style>
</head>
<body style="background: #0F1111; font-family: 'Outfit', sans-serif;">
    <div class="reset-container">
        <h2 style="font-size: 2rem; margin-bottom: 10px;">Forgot Password?</h2>
        <p style="color: rgba(255, 255, 255, 0.6); margin-bottom: 30px;">Enter your details to verify your account</p>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Registered Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-phone"></i>
                <input type="tel" name="phone" placeholder="Registered Phone (10 digits)" pattern="[0-9]{10}" required>
            </div>
            <button type="submit">Verify Account</button>
        </form>
        <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Login</a>
    </div>
</body>
</html>
