<?php
session_start();
include('includes/db.php');

$redirect_url = isset($_GET['redirect_url']) ? $_GET['redirect_url'] : (isset($_POST['redirect_url']) ? $_POST['redirect_url'] : 'index.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];

    $check_email_query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $check_email_query);

    if (mysqli_num_rows($result) > 0) {
        $error = "Email already exists. Please try another one.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO users (name, email, phone, password) VALUES ('$name', '$email', '$phone', '$hashed_password')";

        if (mysqli_query($conn, $insert_query)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['user_id'] = mysqli_insert_id($conn);
            $_SESSION['user_role'] = 'user';
            session_write_close();
            header('Location: ' . $redirect_url);
            exit();
        } else {
            $error = "Registration failed: " . mysqli_error($conn);
        }
    }
}

require 'includes/header.php';
?>

<link rel="stylesheet" href="css/style.css">

<section class="register-section">
    <div class="register-card reveal">
        <h2>Join UrbanRide</h2>
        <p style="text-align: center; color: var(--text-sub); margin-bottom: 30px;">Create an account to start booking your journey.</p>
        
        <?php if (isset($error)): ?>
            <div style="background: #FEF2F2; color: #DC2626; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid #FEE2E2; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" id="registerForm">
            <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">
            <div class="input-group" style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-main); margin-bottom: 8px;">Full Name</label>
                <input type="text" name="name" required placeholder="Enter your full name" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1.5px solid #E2E8F0; outline: none; font-size: 0.95rem;">
            </div>

            <div class="input-group" style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-main); margin-bottom: 8px;">Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1.5px solid #E2E8F0; outline: none; font-size: 0.95rem;">
            </div>

            <div class="input-group" style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-main); margin-bottom: 8px;">Phone Number</label>
                <input type="tel" name="phone" pattern="[0-9]{10}" required placeholder="10-digit mobile number" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1.5px solid #E2E8F0; outline: none; font-size: 0.95rem;">
            </div>

            <div class="input-group" style="margin-bottom: 1.2rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-main); margin-bottom: 8px;">Create Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1.5px solid #E2E8F0; outline: none; font-size: 0.95rem;">
            </div>

            <div class="input-group" style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-main); margin-bottom: 8px;">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••" style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1.5px solid #E2E8F0; outline: none; font-size: 0.95rem;">
            </div>

            <button type="submit" class="btn-signup" style="width: 100%; padding: 15px; border: none; cursor: pointer; font-size: 1.1rem; display: block; text-align: center;">Register Now</button>
        </form>
        
        <div style="margin-top: 25px; text-align: center;">
            <p style="color: var(--text-sub); font-size: 0.9rem;">Already have an account? <a href="javascript:void(0)" onclick="showLoginModal()" style="color: var(--primary); font-weight: 700;">Sign In</a></p>
        </div>
    </div>
</section>

<script>
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', (e) => {
        const pass = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;
        if (pass !== confirm) {
            e.preventDefault();
            alert('Passwords do not match!');
        }
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php require 'includes/footer.php'; ?>