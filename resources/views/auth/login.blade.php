@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Welcome Back</h1>
            <p>Sign in to your account to continue</p>
        </div>

        <div id="alertContainer" class="alert"></div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="Enter your email"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-toggle">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Enter your password"
                        required
                    >
                    <button type="button" class="password-toggle-btn">Show</button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                Sign In
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="/register">Create Account</a>
        </div>
    </div>
</div>
@endsection
