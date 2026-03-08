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
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../src/css/login_signup.css">
</head>

<body>
    <div class="form-container">
        <h1>Login</h1>
        <form id="loginForm" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required maxlength="30" autocomplete="username">
                <small id="username-error" class="form-feedback"></small>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required maxlength="255" autocomplete="current-password">
                    <button type="button" id="toggle-password" class="password-toggle" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" id="submit" disabled>Login</button>
            </div>
        </form>

        <div class="text-center">
            <p>Don't have an account? <a href="../signup/">Sign Up</a></p>
        </div>
    </div>

    <script src="../src/js/sweetalert2.js"></script>
    <script src="../src/js/auth.js"></script>
    <script>
        const usernameField = document.getElementById('username');
        const usernameError = document.getElementById('username-error');
        const submitButton = document.getElementById('submit');

        const validateForm = () => {
            const isValid = AuthUI.validateUsername(usernameField.value.trim());
            AuthUI.setMessage(usernameError, isValid || usernameField.value.length === 0 ? '' : 'Username can only contain letters, numbers, and underscores!');
            submitButton.disabled = !isValid;
            return isValid;
        };

        usernameField.addEventListener('input', validateForm);
        document.getElementById('toggle-password').addEventListener('click', function() {
            AuthUI.togglePassword('password', this);
        });

        document.getElementById('loginForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            if (!validateForm()) return;

            try {
                const data = await AuthUI.submitForm('../api/auth/login.php', new FormData(this));
                if (data.status === 'success') {
                    await Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Login Successful!',
                        showConfirmButton: false,
                        timer: 1200
                    });
                    window.location.href = '../';
                    return;
                }

                Swal.fire({ icon: 'error', title: 'Oops...', text: data.message });
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred, please try again.' });
            }
        });
    </script>
</body>

</html>
