@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Task Manager</h1>
            <p>Manage your tasks efficiently and effectively</p>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <a href="/login" class="btn btn-primary" style="flex: 1; text-align: center;">Sign In</a>
            <a href="/register" class="btn btn-secondary" style="flex: 1; text-align: center;">Create Account</a>
        </div>
    </div>
</div>
@endsection
