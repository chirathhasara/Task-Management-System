document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const token = localStorage.getItem('auth_token');
    
    if (token && (currentPath === '/login' || currentPath === '/register')) {
        window.location.href = '/profile';
        return;
    }

    const forms = {
        login: document.getElementById('loginForm'),
        register: document.getElementById('registerForm')
    };

    if (forms.login) {
        initLoginForm(forms.login);
    }

    if (forms.register) {
        initRegisterForm(forms.register);
    }

    initPasswordToggles();
    initLogoutButtons();
});

function initLoginForm(form) {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        clearErrors();
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading"></span> Logging in...';
        
        const formData = {
            email: form.querySelector('[name="email"]').value,
            password: form.querySelector('[name="password"]').value
        };

        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                localStorage.setItem('auth_token', data.data.token);
                localStorage.setItem('user', JSON.stringify(data.data.user));
                
                showAlert('success', 'Login successful! Redirecting...');
                
                setTimeout(() => {
                    window.location.href = '/profile';
                }, 1000);
            } else {
                showAlert('error', data.message || 'Login failed. Please check your credentials.');
                if (data.errors) {
                    displayErrors(data.errors);
                }
            }
        } catch (error) {
            showAlert('error', 'An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

function initRegisterForm(form) {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        clearErrors();
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading"></span> Creating Account...';
        
        const formData = {
            name: form.querySelector('[name="name"]').value,
            email: form.querySelector('[name="email"]').value,
            password: form.querySelector('[name="password"]').value,
            password_confirmation: form.querySelector('[name="password_confirmation"]').value
        };

        try {
            const response = await fetch('/api/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                localStorage.setItem('auth_token', data.data.token);
                localStorage.setItem('user', JSON.stringify(data.data.user));
                
                showAlert('success', 'Account created successfully! Redirecting...');
                
                setTimeout(() => {
                    window.location.href = '/profile';
                }, 1000);
            } else {
                showAlert('error', data.message || 'Registration failed. Please check your information.');
                if (data.errors) {
                    displayErrors(data.errors);
                }
            }
        } catch (error) {
            showAlert('error', 'An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

function initPasswordToggles() {
    const toggleButtons = document.querySelectorAll('.password-toggle-btn');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Show' : 'Hide';
        });
    });
}

function initLogoutButtons() {
    const logoutButtons = document.querySelectorAll('[data-logout]');
    
    logoutButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const confirmLogout = confirm('Are you sure you want to logout?');
            if (!confirmLogout) return;
            
            const token = localStorage.getItem('auth_token');
            const logoutAll = this.dataset.logout === 'all';
            
            try {
                const response = await fetch(logoutAll ? '/api/auth/logout' : '/api/auth/logout-current', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user');
                    window.location.href = '/login';
                } else {
                    alert('Logout failed. Please try again.');
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        });
    });
}

function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) return;
    
    alertContainer.className = `alert alert-${type} active`;
    alertContainer.textContent = message;
    
    if (type === 'success') {
        setTimeout(() => {
            alertContainer.classList.remove('active');
        }, 5000);
    }
}

function displayErrors(errors) {
    Object.keys(errors).forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('error');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message active';
            errorDiv.textContent = errors[field][0];
            
            input.parentNode.appendChild(errorDiv);
        }
    });
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    document.querySelectorAll('.form-input.error').forEach(el => el.classList.remove('error'));
    
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        alertContainer.classList.remove('active');
    }
}
