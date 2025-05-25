<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rozkład jazdy')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .nav-link-button {
            background: none;
            border: none;
            color: white;
            text-decoration: underline;
            cursor: pointer;
            font-size: 16px;
            padding: 0;
            font-family: inherit;
        }
        .user-info {
            margin: 0 15px;
            font-style: italic;
        }
        nav a, nav .user-info {
            margin-right: 15px;
        }
        .alert {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
    @yield('styles')
    @yield('extra_css')
</head>
<body>
    <header>
        <h1>@yield('header', 'Rozkład jazdy autobusów')</h1>
        <nav>
            <a href="{{ route('home') }}">Strona główna</a>
            
            @auth
                {{-- Show admin panel link only to admin users --}}
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin') }}">Panel administratora</a>
                @endif
                
                <span class="user-info">Zalogowany jako: {{ Auth::user()->name }}</span>
                
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link-button">Wyloguj</button>
                </form>
            @else
                {{-- Show login/register links only for guests --}}
                <a href="{{ route('login') }}">Logowanie</a>
                <a href="{{ route('register') }}">Zarejestruj</a>
            @endauth
            
            {{-- Additional custom navigation items if needed --}}
            @yield('navigation')
        </nav>
    </header>

    <main>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        <p>&copy; 2025 Rozkład jazdy – jazda z busami</p>
    </footer>

    @yield('scripts')
</body>
</html>
