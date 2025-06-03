<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rozkład jazdy')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Niestandardowe style (mają niższy priorytet niż Bootstrap) -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Dodatkowe style -->
    <style>
        /* Style dla zakładek */
        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            width: 100%;
        }
        
        .tab-button {
            padding: 10px 20px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            cursor: pointer;
            font-size: 16px;
            margin: 0 5px;
        }
        
        .tab-button.active {
            background-color: #0077cc;
            color: white;
            border-color: #0077cc;
        }
        
        .tab-content {
            display: block;
            width: 100%;
        }
        
        .tab-content.hidden {
            display: none;
        }
        
        /* Style dla formularzy wyszukiwania */
        .route-search-form,
        .city-route-search-form {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 20px;
            gap: 10px;
        }
        
        .route-search-form select,
        .route-search-form input,
        .city-route-search-form select,
        .city-route-search-form input {
            flex: 1;
            min-width: 150px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        
        .route-search-form button,
        .city-route-search-form button {
            padding: 10px 20px;
            background-color: #0077cc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            min-width: 120px;
        }
        
        .route-search-form button:hover,
        .city-route-search-form button:hover {
            background-color: #005fa3;
        }
    </style>
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
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2025 Rozkład jazdy – jazda z busami</p>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <!-- Skrypt do obsługi powiadomień -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obsługa błędów
            @if(session('error'))
                Swal.fire({
                    title: 'Błąd!',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            @endif
            
            // Obsługa powiadomień o sukcesie
            @if(session('success'))
                Swal.fire({
                    title: 'Sukces!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            @endif
        });
    </script>
    
    @yield('scripts')
</body>
</html>
