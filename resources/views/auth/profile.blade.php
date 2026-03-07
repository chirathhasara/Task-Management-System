@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container">
    <div id="alertContainer" class="alert"></div>
    
    <div class="profile-container" id="profileContainer" style="display: none;">
        <div class="profile-header">
            <h2>My Profile</h2>
        </div>

        <div class="profile-info">
            <div class="info-row">
                <span class="info-label">Full Name</span>
                <span class="info-value" id="userName"></span>
            </div>

            <div class="info-row">
                <span class="info-label">Email Address</span>
                <span class="info-value" id="userEmail"></span>
            </div>

            <div class="info-row">
                <span class="info-label">Account Created</span>
                <span class="info-value" id="userCreated"></span>
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button class="btn btn-danger" data-logout="current">Logout This Device</button>
            <button class="btn btn-danger" data-logout="all">Logout All Devices</button>
        </div>
    </div>

    <div id="loadingContainer" style="text-align: center; padding: 3rem;">
        <div class="loading" style="margin: 0 auto;"></div>
        <p style="margin-top: 1rem; color: #6B7280;">Loading profile...</p>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
        window.location.href = '/login';
        return;
    }

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

            document.getElementById('userName').textContent = user.name;
            document.getElementById('userEmail').textContent = user.email;
            
            const createdDate = new Date(user.created_at);
            document.getElementById('userCreated').textContent = createdDate.toLocaleDateString('en-US', {
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
});
</script>
@endsection
@endsection
