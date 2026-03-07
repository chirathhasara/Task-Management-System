@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, var(--background) 0%, #E0E7FF 100%);">
    <div style="background: var(--card); border-bottom: 1px solid var(--border); box-shadow: 0 1px 3px var(--shadow);">
        <div class="container" style="padding: 1rem 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--primary); margin: 0;">
                    Task Manager
                </h1>
                <div style="display: flex; gap: 1rem;">
                    <a href="/login" class="btn btn-secondary" style="width: auto; padding: 0.75rem 1.5rem;">
                        Sign In
                    </a>
                    <a href="/register" class="btn btn-primary" style="width: auto; padding: 0.75rem 1.5rem; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);">
                        Create Account
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container" style="padding-top: 4rem; padding-bottom: 4rem;">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h2 style="font-size: 3.5rem; font-weight: 800; color: var(--text); margin-bottom: 1rem; line-height: 1.2;">
                Manage Your Tasks
            </h2>
            <p style="font-size: 1.25rem; color: #6B7280; max-width: 600px; margin: 0 auto;">
                Take control of your productivity with a modern, intuitive task management system
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 4rem;">
            <div style="background: var(--card); border-radius: 16px; padding: 2rem; box-shadow: 0 4px 6px var(--shadow); border: 2px solid transparent; transition: all 0.3s ease;">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary), #60A5FA); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                    <svg style="width: 32px; height: 32px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text); margin-bottom: 0.75rem;">
                    Organize Tasks
                </h3>
                <p style="color: #6B7280; line-height: 1.6;">
                    Create, update, and manage your tasks with ease. Keep everything organized in one place.
                </p>
            </div>

            <div style="background: var(--card); border-radius: 16px; padding: 2rem; box-shadow: 0 4px 6px var(--shadow); border: 2px solid transparent; transition: all 0.3s ease;">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--success), #4ADE80); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                    <svg style="width: 32px; height: 32px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text); margin-bottom: 0.75rem;">
                    Track Progress
                </h3>
                <p style="color: #6B7280; line-height: 1.6;">
                    Monitor your task completion status and stay on top of deadlines with visual indicators.
                </p>
            </div>

            <div style="background: var(--card); border-radius: 16px; padding: 2rem; box-shadow: 0 4px 6px var(--shadow); border: 2px solid transparent; transition: all 0.3s ease;">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #8B5CF6, #A78BFA); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                    <svg style="width: 32px; height: 32px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text); margin-bottom: 0.75rem;">
                    Secure & Private
                </h3>
                <p style="color: #6B7280; line-height: 1.6;">
                    Your tasks are protected with enterprise-grade security and authentication.
                </p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--border); color: #6B7280;">
            <p style="font-size: 0.875rem;">
                Simple. Powerful. Efficient.
            </p>
        </div>
    </div>
</div>

@section('scripts')
<script>
    if (localStorage.getItem('auth_token')) {
        window.location.href = '/tasks';
    }

    const cards = document.querySelectorAll('[style*="background: var(--card)"]');
    cards.forEach((card, index) => {
        if (index > 0 && index < 4) {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
                this.style.borderColor = 'var(--primary)';
                this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.borderColor = 'transparent';
                this.style.boxShadow = '0 4px 6px var(--shadow)';
            });
        }
    });
</script>
@endsection
@endsection
