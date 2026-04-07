// errorPopup.js

window.onload = function () {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');

    if (error) {
        let message = '';
        if (error === 'incorrect_password') {
            message = 'Incorrect password. Please try again.';
        } else if (error === 'invalid_email') {
            message = 'Invalid email. Please check your email address.';
        }

        if (message) {
            // Show the error message in a pop-up
            alert(message);
        }
    }
};
