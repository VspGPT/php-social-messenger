<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: ../");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../src/css/login_signup.css">
</head>

<body>
    <div class="form-container">
        <h1>Sign Up</h1>
        <form id="signupForm" novalidate>
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required maxlength="30" autocomplete="name">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required maxlength="150" autocomplete="email">
                <small id="email-message" class="form-feedback"></small>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required maxlength="30" autocomplete="username">
                <small id="username-message" class="form-feedback"></small>
                <small id="username-error" class="form-feedback"></small>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required maxlength="255" autocomplete="new-password">
                    <button type="button" id="toggle-password" class="password-toggle" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="password-container">
                    <input type="password" id="confirm_password" name="confirm_password" required maxlength="255" autocomplete="new-password">
                    <button type="button" id="toggle-confirm-password" class="password-toggle" aria-label="Toggle confirm password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small id="confirm-password-error" class="form-feedback"></small>
            </div>

            <div class="form-group">
                <button type="submit" id="submit">Sign Up</button>
            </div>
        </form>

        <div class="text-center">
            <p>Already have an account? <a href="../login/">Login</a></p>
        </div>
    </div>

    <script src="../src/js/sweetalert2.js"></script>
    <script src="../src/js/auth.js"></script>
    <script>
        const emailField = document.getElementById('email');
        const usernameField = document.getElementById('username');
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm_password');

        const emailMessage = document.getElementById('email-message');
        const usernameMessage = document.getElementById('username-message');
        const usernameError = document.getElementById('username-error');
        const confirmPasswordError = document.getElementById('confirm-password-error');
        const submitButton = document.getElementById('submit');

        let isEmailAvailable = false;
        let isUsernameAvailable = false;

        const validateUsername = () => {
            const username = usernameField.value.trim();
            const isValid = AuthUI.validateUsername(username);
            AuthUI.setMessage(usernameError, isValid || username.length === 0 ? '' : 'Username can only contain letters, numbers, and underscores!');
            return isValid;
        };

        const validatePasswordsMatch = () => {
            const password = passwordField.value.trim();
            const confirmPassword = confirmPasswordField.value.trim();
            const isValid = confirmPassword.length === 0 || password === confirmPassword;
            AuthUI.setMessage(confirmPasswordError, isValid ? '' : 'Passwords do not match!');
            return isValid;
        };

        const validateFormState = () => {
            const isValid = validateUsername() && validatePasswordsMatch();
            submitButton.disabled = !isValid;
            return isValid;
        };

        emailField.addEventListener('input', async function() {
            const email = this.value.trim();
            AuthUI.setMessage(emailMessage, '');
            isEmailAvailable = false;

            if (!email) return;
            if (!AuthUI.validateEmail(email)) {
                AuthUI.setMessage(emailMessage, 'Email format is incorrect!');
                return;
            }

            const data = await AuthUI.checkAvailability('email', email);
            isEmailAvailable = !data.exists;
            if (!isEmailAvailable) AuthUI.setMessage(emailMessage, 'This email exists!');
        });

        usernameField.addEventListener('input', async function() {
            const username = this.value.trim();
            validateFormState();
            AuthUI.setMessage(usernameMessage, '');
            isUsernameAvailable = false;

            if (!username || !AuthUI.validateUsername(username)) return;

            const data = await AuthUI.checkAvailability('username', username);
            isUsernameAvailable = !data.exists;
            if (!isUsernameAvailable) AuthUI.setMessage(usernameMessage, 'This username exists!');
        });

        passwordField.addEventListener('input', validateFormState);
        confirmPasswordField.addEventListener('input', validateFormState);

        document.getElementById('toggle-password').addEventListener('click', function() {
            AuthUI.togglePassword('password', this);
        });

        document.getElementById('toggle-confirm-password').addEventListener('click', function() {
            AuthUI.togglePassword('confirm_password', this);
        });

        document.getElementById('signupForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            if (!validateFormState()) return;

            const email = emailField.value.trim();
            if (!AuthUI.validateEmail(email)) {
                AuthUI.setMessage(emailMessage, 'Email format is incorrect!');
                return;
            }

            if (!isEmailAvailable) {
                AuthUI.setMessage(emailMessage, 'This email exists!');
                return;
            }

            if (!isUsernameAvailable) {
                AuthUI.setMessage(usernameMessage, 'This username exists!');
                return;
            }

            try {
                const data = await AuthUI.submitForm('../api/auth/signup.php', new FormData(this));
                if (data.status === 'success') {
                    await Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Registration Successful!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    window.location.href = '../';
                    return;
                }

                Swal.fire({ icon: 'error', title: 'Oops...', text: data.message });
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred, please try again.' });
            }
        });

        validateFormState();
    </script>
</body>

</html>
