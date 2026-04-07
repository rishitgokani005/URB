<?php
session_start();
include('includes/db.php'); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if user exists
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            // Password is correct, set session
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['user_role'] = $row['role']; // Use $row instead of $user
            $_SESSION['user_id'] = $row['user_id']; // Save user ID to session
            
            // Redirect based on role
            if ($row['role'] === 'admin') {
                $dashboard_url = isset($row['dashboard_url']) ? $row['dashboard_url'] : 'admin_default_dashboard.php';
                header("Location: $dashboard_url");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            // Password is incorrect
            header("Location: index.php?error=incorrect_password");
            exit();
        }
    } else {
        // No user found
        header("Location: register.php?error=invalid_email");
        exit();
    }
}
?>

<?php if (!isset($_SESSION['loggedin'])): ?>
    <style>
    .error-message {
    color: #ff3c00; /* Color for the error text */
    background-color: #ffe6e6; /* Light red background */
    padding: 15px;
    border: 1px solid #ff3c00;
    border-radius: 5px;
    margin-bottom: 20px;
    font-size: 10px;
    text-align: center;
    width: 100%; /* Full width for small screens */
    box-sizing: border-box; /* Ensure padding is included in width */
}

@media screen and (min-width: 576px) {
    .error-message {
        font-size: 16px; /* Larger font for medium screens */
        padding: 18px; /* Slightly more padding */
    }
}

@media screen and (min-width: 768px) {
    .error-message {
        font-size: 18px; /* Larger font for large screens */
        max-width: 600px; /* Restrict the width on larger screens */
        margin: 20px auto; /* Center the error message */
    }
}

@media screen and (min-width: 992px) {
    .error-message {
        font-size: 22px; /* Even larger font for very large screens */
        padding: 20px; /* More padding for readability */
    }
}
    </style>
<div id="loginModal">
    <div class="modal-content">
        <!-- Removed the close button -->
        <div id="login-container" class="container login-container">
            <h2>Login</h2>

                <!-- Display error messages -->
                <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php
                    if ($_GET['error'] == 'incorrect_password') {
                        echo "Incorrect password or Invalid email. Please try again.";
                    } elseif ($_GET['error'] == 'invalid_email') {
                        echo "Invalid email. Please register.";
                    }
                    ?>
                </div>
            <?php endif; ?>
         


            <form action="" method="POST">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br>

                <button type="submit">Login</button>
            </form>
            <p><a href="register.php">New User? Register</a></p>
            <p><a href="request_reset.php">Forgot Password?</a></p>
        </div>
    </div>
</div> 

<?php endif; ?>
<script>  // Get the modal element
        var modal = document.getElementById('loginModal');

        <?php if (!isset($_SESSION['loggedin'])): ?>
        // Display the modal if the user is not logged in
        window.onload = function() {
            modal.style.display = "block"; // Show the modal
        }
    

        <?php endif; ?>
    
    </script>
