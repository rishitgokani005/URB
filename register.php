<?php
// Start session
session_start();

// Include database connection
include('includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if email already exists
    $check_email_query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $check_email_query);

    if (mysqli_num_rows($result) > 0) {
        echo "Email already exists. Please try another one.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $insert_query = "INSERT INTO users (name, email, phone, password) VALUES ('$name', '$email', '$phone', '$hashed_password')";

        if (mysqli_query($conn, $insert_query)) {
            // Set session variable or any other success message if needed
            $_SESSION['email'] = $email;

            // Redirect to index.php after successful registration
            header('Location: index.php');
            exit(); // Ensure no further code is executed after redirection
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>
<html lang>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="logo/urbanride1.ico"  sizes="1080x1080" type="image/x-icon">
<link rel="stylesheet" href="css/login.css">
<script src="assets/js/register.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
<meta name="theme-color" content="#FF6B6B">

</head>
  <!-- Registration Page HTML -->
   <body>
    
    <div class="container register-container">
        
    <h2>Create Account</h2>
    <form action="register.php" method="POST" onsubmit="return validatePassword()">
        <div class="input-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="input-group">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" inputmode="numeric">
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="new-password"> 
        </div>

        <div class="input-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <p id="passwordError"></p>
        <button type="submit">Register</button>
    </form>
    
</div>

</body>
</html>

 