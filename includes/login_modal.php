<?php
// login_modal.php - Modern Light Login Modal
$is_compulsory = isset($compulsory_login) && $compulsory_login;
$login_error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<div id="loginModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; justify-content: center; align-items: center; z-index: 3000; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(8px);">
    <div class="modal-content" style="position: relative; width: 90%; max-width: 450px; padding: 3rem; border-radius: 30px; background: white; box-shadow: 0 30px 60px rgba(0,0,0,0.2); border: 1px solid rgba(0,0,0,0.05); animation: fadeInUp 0.5s ease;">
        
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
            
            <?php if ($login_error): ?>
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
                <p style="color: var(--text-sub);">Don't have an account? <a href="<?php echo $base_url; ?>register.php?redirect_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" style="color: var(--primary); font-weight: 700;">Join for free</a></p>
                <a href="<?php echo $base_url; ?>request_reset.php" style="display: block; margin-top: 10px; font-size: 0.9rem; color: var(--text-sub);">Forgot Password?</a>
            </div>
        </div>
    </div>
</div>

<script>
    const loginModal = document.getElementById('loginModal');
    const loginCloseBtn = document.getElementById('closeLoginModal');

    function showLoginModal() {
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

    // Auto-show modal if there's an error
    if (<?php echo json_encode(!empty($login_error)); ?>) {
        showLoginModal();
    }
</script>
