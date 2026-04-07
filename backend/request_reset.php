<?php

include 'includes/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email or username exists in the database
    $sql = "SELECT user_id FROM users WHERE email = ? OR name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $email); // Using the same $email for email and username check
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate a unique token and store it in the session (no email needed)
        $token = bin2hex(random_bytes(50));

        // Store the token and the user id in the session
        $_SESSION['reset_token'] = $token;
        $stmt->bind_result($id);
        $stmt->fetch();
        $_SESSION['user_id'] = $id;

        // Redirect to reset password page
       // header("Location: request_reset.php");
       // exit();
    } else {
        echo "No account found with that email or username!";
    }

    $stmt->close();
}
// Error handling
$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Your existing code...
    if ($stmt->num_rows === 0) {
        $error_message = "No account found with that email!";
    }
    // Other error handling...
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure the token and user_id are set
    if (isset($_SESSION['reset_token']) && isset($_SESSION['user_id'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if the passwords match
        if ($new_password !== $confirm_password) {
            echo "Passwords do not match!";
            exit();
        }

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $user_id = $_SESSION['user_id'];

        // Update the user's password in the database
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            // Clear the session variables
            unset($_SESSION['reset_token']);
            unset($_SESSION['user_id']);
            $success_message = "Password has been reset successfully!";
        } else {
            echo "Error resetting password!";
        }

        $stmt->close();
    } else {
        echo "Invalid session or token!";
    }
}
?>
<!--
<link rel="stylesheet" href="css/login.css">

<div class="container request-reset-container">
    <h2>Reset Password</h2>
    <form action="request_reset.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" class="confirm" required><br>

        <button type="submit">Reset Password</button>
    </form>
</div>
-->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - UrbanRide</title>
    <link rel="icon" href="logo/urbanride1.ico" sizes="1080x1080" type="image/x-icon">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="request-reset-container">
        <h2>Reset Password</h2>
        <form action="request_reset.php" method="POST" class="auth-form">
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="input-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>

            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <?php if(isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <button type="submit" class="auth-button">Reset Password</button>
        </form>
    </div>

    <script>
        window.onload = function() {
            const successMessage = "<?php echo isset($success_message) ? $success_message : ''; ?>";
            if (successMessage) {
                alert(successMessage);
                window.location.href = "index.php";
            }
        };
    </script>
</body>
</html>