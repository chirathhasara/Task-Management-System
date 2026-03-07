@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Create Account</h1>
            <p>Sign up to get started with Task Manager</p>
        </div>

        <div id="alertContainer" class="alert"></div>

        <form id="registerForm">
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-input" 
                    placeholder="Enter your full name"
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
                        placeholder="Create a password"
                        required
                    >
                    <button type="button" class="password-toggle-btn">Show</button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="password-toggle">
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-input" 
                        placeholder="Confirm your password"
                        required
                    >
                    <button type="button" class="password-toggle-btn">Show</button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                Create Account
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="/login">Sign In</a>
        </div>
    </div>
</div>
@endsection
