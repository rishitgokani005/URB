<?php
// login_modal.php - Modern Light Login Modal
$is_compulsory = isset($compulsory_login) && $compulsory_login;
$login_error = isset($_GET['error']) ? $_GET['error'] : '';
$is_register_error = in_array($login_error, ['email_exists', 'register_failed']);
$is_login_error = in_array($login_error, ['incorrect_password', 'invalid_email']);
?>

<div id="loginModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; justify-content: center; align-items: center; z-index: 3000; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(8px);">
    <div class="modal-content" style="position: relative; width: 90%; max-width: 450px; padding: 3rem; border-radius: 30px; background: white; box-shadow: 0 30px 60px rgba(0,0,0,0.2); border: 1px solid rgba(0,0,0,0.05); animation: fadeInUp 0.5s ease; max-height: 90vh; overflow-y: auto;">
        
        <?php if (!$is_compulsory): ?>
            <button class="close-modal" id="closeLoginModal" style="position: absolute; top: 25px; right: 25px; background: #F1F5F9; border: none; color: #64748B; width: 40px; height: 40px; border-radius: 50%; font-size: 1.5rem; cursor: pointer; transition: 0.3s; display: flex; justify-content: center; align-items: center;">&times;</button>
        <?php endif; ?>

        <div class="logo-container" style="text-align: center; margin-bottom: 2rem;">
            <a href="#" class="logo" style="justify-content: center; font-size: 2rem;">
                Urban<span>Ride</span>
            </a>
        </div>

        <div id="login-container">
            <h2 style="font-family: var(--font-heading); color: var(--accent); font-size: 1.8rem; margin-bottom: 1rem; text-align: center;">
                <?php echo $is_compulsory ? "Identification Required" : "Welcome Back"; ?>
            </h2>
            <p style="text-align: center; color: var(--text-sub); margin-bottom: 2rem;">Enter your credentials to continue</p>
            
            <?php if ($is_login_error): ?>
                <div class="login-error-message" style="background: #FEF2F2; color: #DC2626; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid #FEE2E2; text-align: center; font-weight: 500;">
                    <?php
                    if ($login_error === 'incorrect_password') {
                        echo "Incorrect password. Please try again.";
                    } elseif ($login_error === 'invalid_email') {
                        echo "No account found with this email. Please register or check your email.";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo $base_url; ?>index.php" method="POST">
                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                <div class="input-group" style="margin-bottom: 1.2rem;">
                    <div style="position: relative;">
                        <i class="fas fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-sub);"></i>
                        <input type="email" name="email" required placeholder="Email Address" style="width: 100%; padding: 15px 15px 15px 45px; border-radius: 12px; border: 1.5px solid #E2E8F0; outline: none; transition: 0.3s;">
                    </div>
                </div>
                <div class="input-group" style="margin-bottom: 1.5rem;">
                    <div style="position: relative;">
                        <i class="fas fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-sub);"></i>
                        <input type="password" name="password" required placeholder="Password" style="width: 100%; padding: 15px 15px 15px 45px; border-radius: 12px; border: 1.5px solid #E2E8F0; outline: none; transition: 0.3s;">
                    </div>
                </div>
                <button type="submit" class="btn-signup" style="width: 100%; padding: 15px; border: none; cursor: pointer; font-size: 1.1rem;">Login Now</button>
            </form>
            <div class="modal-footer" style="margin-top: 2rem; text-align: center;">
                <p style="color: var(--text-sub);">Don't have an account? <a href="javascript:void(0)" onclick="showRegisterForm()" style="color: var(--primary); font-weight: 700;">Join for free</a></p>
                <a href="<?php echo $base_url; ?>request_reset.php" style="display: block; margin-top: 10px; font-size: 0.9rem; color: var(--text-sub);">Forgot Password?</a>
            </div>
        </div>

        <div id="register-container" style="display: none;">
            <h2 style="font-family: var(--font-heading); color: var(--accent); font-size: 1.8rem; margin-bottom: 1rem; text-align: center;">
                Join UrbanRide
            </h2>
            <p style="text-align: center; color: var(--text-sub); margin-bottom: 2rem;">Create an account to start booking your journey.</p>
            
            <?php if ($is_register_error): ?>
                <div class="register-error-message" style="background: #FEF2F2; color: #DC2626; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid #FEE2E2; text-align: center; font-weight: 500;">
                    <?php
                    if ($login_error === 'email_exists') {
                        echo "Email already exists. Please try another one.";
                    } elseif ($login_error === 'register_failed') {
                        echo "Registration failed. Please try again.";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo $base_url; ?>index.php" method="POST" id="modalRegisterForm">
                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                
                <div class="input-group" style="margin-bottom: 1.2rem;">
                    <div style="position: relative;">
                        <i class="fas fa-user" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-sub);"></i>
                        <input type="text" name="name" required placeholder="Full Name" style="width: 100%; padding: 15px 15px 15px 45px; border-radius: 12px; border: 1.5px solid #E2E8F0; outline: none; transition: 0.3s; font-size: 0.95rem;">
                    </div>
                </div>

                <div class="input-group" style="margin-bottom: 1.2rem;">
                    <div style="position: relative;">
                        <i class="fas fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-sub);"></i>
                        <input type="email" name="email" required placeholder="Email Address" style="width: 100%; padding: 15px 15px 15px 45px; border-radius: 12px; border: 1.5px solid #E2E8F0; outline: none; transition: 0.3s; font-size: 0.95rem;">
                    </div>
                </div>

                <div class="input-group" style="margin-bottom: 1.2rem;">
                    <div style="position: relative;">
                        <i class="fas fa-phone" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-sub);"></i>
                        <input type="tel" name="phone" pattern="[0-9]{10}" required placeholder="Phone Number (10 digits)" style="width: 100%; padding: 15px 15px 15px 45px; border-radius: 12px; border: 1.5px solid #E2E8F0; outline: none; transition: 0.3s; font-size: 0.95rem;">
                    </div>
                </div>

                <div class="input-group" style="margin-bottom: 1.2rem;">
                    <div style="position: relative;">
                        <i class="fas fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-sub);"></i>
                        <input type="password" id="modal_password" name="password" required placeholder="Create Password" style="width: 100%; padding: 15px 15px 15px 45px; border-radius: 12px; border: 1.5px solid #E2E8F0; outline: none; transition: 0.3s; font-size: 0.95rem;">
                    </div>
                </div>

                <div class="input-group" style="margin-bottom: 1.5rem;">
                    <div style="position: relative;">
                        <i class="fas fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-sub);"></i>
                        <input type="password" id="modal_confirm_password" name="confirm_password" required placeholder="Confirm Password" style="width: 100%; padding: 15px 15px 15px 45px; border-radius: 12px; border: 1.5px solid #E2E8F0; outline: none; transition: 0.3s; font-size: 0.95rem;">
                    </div>
                </div>

                <button type="submit" name="register" class="btn-signup" style="width: 100%; padding: 15px; border: none; cursor: pointer; font-size: 1.1rem;">Register Now</button>
            </form>
            <div class="modal-footer" style="margin-top: 2rem; text-align: center;">
                <p style="color: var(--text-sub);">Already have an account? <a href="javascript:void(0)" onclick="showLoginForm()" style="color: var(--primary); font-weight: 700;">Sign In</a></p>
            </div>
        </div>

    </div>
</div>

<script>
    const loginModal = document.getElementById('loginModal');
    const loginCloseBtn = document.getElementById('closeLoginModal');
    const loginContainer = document.getElementById('login-container');
    const registerContainer = document.getElementById('register-container');

    function showRegisterForm() {
        loginContainer.style.display = 'none';
        registerContainer.style.display = 'block';
    }

    function showLoginForm() {
        registerContainer.style.display = 'none';
        loginContainer.style.display = 'block';
    }

    function showLoginModal() {
        // Reset to login form unless it is an active register error redirect
        if (!<?php echo json_encode($is_register_error); ?>) {
            showLoginForm();
        }
        loginModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function hideLoginModal() {
        loginModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    if (loginCloseBtn) {
        loginCloseBtn.onclick = hideLoginModal;
    }

    window.onclick = function(event) {
        if (event.target == loginModal && !<?php echo json_encode($is_compulsory); ?>) {
            hideLoginModal();
        }
    }

    // Auto-show modal and switch view if there's an error
    if (<?php echo json_encode($is_login_error); ?>) {
        showLoginForm();
        showLoginModal();
    } else if (<?php echo json_encode($is_register_error); ?>) {
        showRegisterForm();
        showLoginModal();
    }

    // Password mismatch validation
    const registerForm = document.getElementById('modalRegisterForm');
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            const pass = document.getElementById('modal_password').value;
            const confirm = document.getElementById('modal_confirm_password').value;
            if (pass !== confirm) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    }
</script>
