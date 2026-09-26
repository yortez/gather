<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gather') · Gather</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}" aria-label="Gather home">
            <span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span>
            <span>gather<span class="brand-period">.</span></span>
        </a>
        @auth
            <nav class="main-nav" aria-label="Main navigation">
                <a href="{{ route('forms.index') }}" @class(['active' => request()->routeIs('forms.index')])>My forms</a>
                <a href="{{ route('forms.create') }}" @class(['active' => request()->routeIs('forms.create')])>Create form</a>
            </nav>
            <div class="user-menu">
                <span class="user-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="user-name">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="quiet-button" type="submit">Sign out</button>
                </form>
            </div>
        @else
            <nav class="main-nav guest-nav" aria-label="Account navigation">
                <a href="{{ route('login') }}">Sign in</a>
                <a class="button button-dark button-small" href="{{ route('register') }}">Create account</a>
            </nav>
        @endauth
    </header>

    @if (session('status'))
        <div class="status-banner" role="status">{{ session('status') }}</div>
    @endif

    <main class="page-shell">
        @yield('content')
    </main>

    <footer class="site-footer">
        <span>Gather forms</span>
        <span>Made for better questions.</span>
    </footer>
</body>
</html>