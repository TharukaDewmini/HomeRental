
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('#login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            const username = document.querySelector('#username').value;
            const password = document.querySelector('#password').value;
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in all fields.');
            }
        });
    }
});