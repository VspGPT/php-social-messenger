const AuthUI = (() => {
    const USERNAME_PATTERN = /^[a-zA-Z0-9_]+$/;
    const EMAIL_PATTERN = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

    function setMessage(element, message = '') {
        if (!element) return;
        element.textContent = message;
    }

    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button?.querySelector('i');

        if (!input || !icon) return;

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !isHidden);
        icon.classList.toggle('fa-eye-slash', isHidden);
    }

    function validateUsername(username) {
        return USERNAME_PATTERN.test(username);
    }

    function validateEmail(email) {
        return EMAIL_PATTERN.test(email);
    }

    async function checkAvailability(type, value) {
        const response = await fetch('../api/auth/check_availability.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `${type}=${encodeURIComponent(value)}`
        });

        return response.json();
    }

    async function submitForm(url, formData) {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });

        return response.json();
    }

    return {
        setMessage,
        togglePassword,
        validateUsername,
        validateEmail,
        checkAvailability,
        submitForm
    };
})();
