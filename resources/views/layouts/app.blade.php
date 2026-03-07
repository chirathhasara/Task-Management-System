<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Task Manager</title>
    @vite(['resources/css/index.css', 'resources/js/auth.js'])
    @yield('styles')
</head>
<body>
    <nav class="navbar" id="navbar" style="display: none;">
        <div class="navbar-content">
            <a href="/" class="navbar-brand">Task Manager</a>
            <div class="navbar-menu">
                <a href="/profile" class="btn btn-secondary">Profile</a>
                <button class="btn btn-danger" data-logout="all">Logout</button>
            </div>
        </div>
    </nav>

    @yield('content')

    <script>
        if (localStorage.getItem('auth_token') && window.location.pathname === '/profile') {
            document.getElementById('navbar').style.display = 'block';
        }
    </script>
    @yield('scripts')
</body>
</html>
