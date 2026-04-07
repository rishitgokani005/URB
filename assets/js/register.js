function validatePassword() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const errorElement = document.getElementById('passwordError');

    if (password !== confirmPassword) {
        errorElement.textContent = "Passwords do not match!";
        return false;
    }
    
    if (password.length < 8) {
        errorElement.textContent = "Password must be at least 8 characters!";
        return false;
    }
    
    errorElement.textContent = "";
    return true;
}