/**
 * Toggle password field visibility
 * @param {string} id - The ID of the password input field
 */
function togglePassword(id) {
    const input = document.getElementById(id);
    const button = input.nextElementSibling;
    
    if (input.type === 'password') {
        input.type = 'text';
        button.innerHTML = '👁️ Hide';
        button.classList.add('active');
    } else {
        input.type = 'password';
        button.innerHTML = '👁️ Show';
        button.classList.remove('active');
    }
}

/**
 * Initialize auto-dismiss for alert messages
 */
function initAutoDismiss() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 1s ease';
            alert.style.opacity = '0';
            
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 1000);
        }, 5000);
    });
}

/**
 * Validate registration form before submission
 */
function validateRegistration() {
    const username = document.querySelector('input[name="register_username"]');
    const password = document.querySelector('input[name="register_password"]');
    let isValid = true;
    
    if (username.value.length < 3) {
        alert('Username must be at least 3 characters');
        isValid = false;
    }
    
    if (password.value.length < 6) {
        alert('Password must be at least 6 characters');
        isValid = false;
    }
    
    return isValid;
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initAutoDismiss();
    
    // Add form validation
    const registerForm = document.querySelector('form[method="POST"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            if (!validateRegistration()) {
                e.preventDefault();
            }
        });
    }
    
    // Add keyboard shortcut for password toggle (Shift+E)
    document.addEventListener('keydown', function(e) {
        if (e.shiftKey && e.key === 'E') {
            const focused = document.activeElement;
            if (focused && focused.type === 'password') {
                togglePassword(focused.id);
            }
        }
    });
});
