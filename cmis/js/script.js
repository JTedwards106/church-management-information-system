/**
 * Custom JavaScript File
 * Purpose: Client-side interactions and enhancements
 * Author: Justin Edwards
 */

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            // Use Bootstrap's alert close method
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000); // 5000ms = 5 seconds
    });
    
    // Confirm before deactivating users
    const deactivateLinks = document.querySelectorAll('a[href*="action=deactivate"]');
    deactivateLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to deactivate this user?')) {
                e.preventDefault();
            }
        });
    });
    
    // Password strength indicator (optional enhancement)
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
    }
    
    // Validate password match on confirm password field
    const confirmPasswordInput = document.getElementById('confirm_password');
    if (confirmPasswordInput && passwordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value !== passwordInput.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});

/**
 * Check password strength and display indicator
 * @param {string} password - Password to check
 */
function checkPasswordStrength(password) {
    let strength = 0;
    
    // Check length
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    
    // Check for numbers
    if (/[0-9]/.test(password)) strength++;
    
    // Check for lowercase and uppercase
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    
    // Check for special characters
    if (/[\W_]/.test(password)) strength++;
    
    // Display strength (you can add a visual indicator in your HTML)
    const strengthText = ['Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
    console.log('Password strength:', strengthText[Math.min(strength - 1, 4)]);
}

/**
 * Format numbers with commas (for large attendance counts)
 * @param {number} num - Number to format
 * @return {string} - Formatted number
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/**
 * Confirm action before proceeding
 * @param {string} message - Confirmation message
 * @return {boolean} - True if confirmed
 */
function confirmAction(message) {
    return confirm(message);
}