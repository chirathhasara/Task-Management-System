@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container">
    <div id="alertContainer" class="alert"></div>
    
    <div class="profile-container" id="profileContainer" style="display: none;">
        <div class="profile-header">
            <h2>My Profile</h2>
        </div>

        <form id="profileForm">
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-input" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="created" class="form-label">Account Created</label>
                <input 
                    type="text" 
                    id="created" 
                    class="form-input" 
                    readonly
                    style="background-color: #F3F4F6; cursor: not-allowed;"
                >
            </div>

            <div style="border-top: 1px solid var(--border); margin: 2rem 0; padding-top: 2rem;">
                <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: var(--text);">Change Password</h3>
                <p style="color: #6B7280; font-size: 0.875rem; margin-bottom: 1rem;">Leave blank if you don't want to change your password</p>
                
                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <div style="position: relative;">
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            class="form-input"
                        >
                        <button 
                            type="button" 
                            class="toggle-password" 
                            data-target="current_password"
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6B7280; font-size: 0.875rem;"
                        >
                            Show
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <div style="position: relative;">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input"
                            minlength="8"
                        >
                        <button 
                            type="button" 
                            class="toggle-password" 
                            data-target="password"
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6B7280; font-size: 0.875rem;"
                        >
                            Show
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <div style="position: relative;">
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            class="form-input"
                            minlength="8"
                        >
                        <button 
                            type="button" 
                            class="toggle-password" 
                            data-target="password_confirmation"
                            style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6B7280; font-size: 0.875rem;"
                        >
                            Show
                        </button>
                    </div>
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" id="updateBtn">Update Profile</button>
                <button type="button" class="btn btn-danger" id="logoutBtn">Logout</button>
            </div>
        </form>
    </div>

    <div id="loadingContainer" style="text-align: center; padding: 3rem;">
        <div class="loading" style="margin: 0 auto;"></div>
        <p style="margin-top: 1rem; color: #6B7280;">Loading profile...</p>
    </div>
</div>

@section('scripts')
<script>
let originalData = {};

document.addEventListener('DOMContentLoaded', async function() {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        window.location.href = '/login';
        return;
    }

    await loadProfile();
    initPasswordToggles();
    initProfileForm();
    initLogoutButton();
});

async function loadProfile() {
    const token = localStorage.getItem('auth_token');

    try {
        const response = await fetch('/api/auth/profile', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            const user = data.data;
            originalData = user;

            document.getElementById('name').value = user.name;
            document.getElementById('email').value = user.email;
            
            const createdDate = new Date(user.created_at);
            document.getElementById('created').value = createdDate.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });

            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('profileContainer').style.display = 'block';
        } else {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
    } catch (error) {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        window.location.href = '/login';
    }
}

function initPasswordToggles() {
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            
            if (input.type === 'password') {
                input.type = 'text';
                this.textContent = 'Hide';
            } else {
                input.type = 'password';
                this.textContent = 'Show';
            }
        });
    });
}

function initProfileForm() {
    const form = document.getElementById('profileForm');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const updateBtn = document.getElementById('updateBtn');
        const originalText = updateBtn.textContent;
        
        updateBtn.disabled = true;
        updateBtn.innerHTML = '<span class="loading"></span> Updating...';
        
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
        };
        
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        if (newPassword || confirmPassword) {
            if (!currentPassword) {
                showAlert('error', 'Current password is required to change password');
                updateBtn.disabled = false;
                updateBtn.textContent = originalText;
                return;
            }
            
            if (newPassword.length < 8) {
                showAlert('error', 'New password must be at least 8 characters');
                updateBtn.disabled = false;
                updateBtn.textContent = originalText;
                return;
            }
            
            if (newPassword !== confirmPassword) {
                showAlert('error', 'New password and confirmation do not match');
                updateBtn.disabled = false;
                updateBtn.textContent = originalText;
                return;
            }
            
            formData.current_password = currentPassword;
            formData.password = newPassword;
            formData.password_confirmation = confirmPassword;
        }
        
        const token = localStorage.getItem('auth_token');
        
        try {
            const response = await fetch('/api/auth/profile', {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (response.ok) {
                showAlert('success', 'Profile updated successfully');
                
                document.getElementById('current_password').value = '';
                document.getElementById('password').value = '';
                document.getElementById('password_confirmation').value = '';
                
                await loadProfile();
            } else {
                if (result.errors) {
                    displayErrors(result.errors);
                } else {
                    showAlert('error', result.message || 'Failed to update profile');
                }
            }
        } catch (error) {
            showAlert('error', 'An error occurred. Please try again.');
        } finally {
            updateBtn.disabled = false;
            updateBtn.textContent = originalText;
        }
    });
}

function initLogoutButton() {
    document.getElementById('logoutBtn').addEventListener('click', async function() {
        const token = localStorage.getItem('auth_token');
        
        try {
            await fetch('/api/auth/logout-current', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
    });
}

function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    alertContainer.className = `alert alert-${type} active`;
    alertContainer.textContent = message;
    
    if (type === 'success') {
        setTimeout(() => {
            alertContainer.classList.remove('active');
        }, 5000);
    }
}

function displayErrors(errors) {
    const errorMessages = Object.values(errors).flat();
    showAlert('error', errorMessages.join(' '));
}
</script>
@endsection
@endsection
