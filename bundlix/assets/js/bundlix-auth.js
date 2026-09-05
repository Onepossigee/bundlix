/**
 * BundliX Authentication Handler
 * Handles Login and Logout forms via AJAX
 */

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('bundlix-login-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('bx-login-btn');
            const btnText = btn.querySelector('.btn-text');
            const btnLoader = btn.querySelector('.btn-loader');
            const msgBox = document.getElementById('bx-login-message');
            const nonceField = document.getElementById('bundlix_nonce_field');
            
            // Safety check for nonce
            if (!nonceField) {
                console.error('BundliX: Nonce field missing in form');
                return;
            }
            
            const nonce = nonceField.value;
            
            // UI Loading State
            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline';
            msgBox.className = 'bx-message';
            msgBox.style.display = 'none';
            msgBox.textContent = '';

            const formData = new FormData();
            formData.append('action', 'bundlix_login');
            formData.append('nonce', nonce);
            formData.append('username', document.getElementById('bx-username').value);
            formData.append('password', document.getElementById('bx-password').value);
            
            const rememberCheckbox = document.querySelector('input[name="remember"]');
            formData.append('remember', rememberCheckbox && rememberCheckbox.checked ? '1' : '0');

            // Use bundlixApp object if available, otherwise fallback to standard ajaxurl
            const ajaxUrl = (typeof bundlixApp !== 'undefined' && bundlixApp.ajaxUrl) 
                            ? bundlixApp.ajaxUrl 
                            : (window.ajaxurl || '/wp-admin/admin-ajax.php');

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin' // Important for cookies/session
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    msgBox.textContent = data.data.message;
                    msgBox.classList.add('success');
                    msgBox.style.display = 'block';
                    
                    // Small delay for UX before redirect
                    setTimeout(() => {
                        window.location.href = data.data.redirect_url;
                    }, 1000);
                } else {
                    msgBox.textContent = data.data.message || 'Login failed. Please try again.';
                    msgBox.classList.add('error');
                    msgBox.style.display = 'block';
                    
                    // Reset Button
                    btn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoader.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('BundliX Login Error:', error);
                msgBox.textContent = 'An unexpected error occurred. Please check your connection and try again.';
                msgBox.classList.add('error');
                msgBox.style.display = 'block';
                
                // Reset Button
                btn.disabled = false;
                btnText.style.display = 'inline';
                btnLoader.style.display = 'none';
            });
        });
    }
});
