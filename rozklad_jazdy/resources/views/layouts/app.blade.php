<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rozkład jazdy')</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
    <meta name="theme-color" content="#0077cc">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
        /* Style dla dropdown menu */
        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
        }
        
        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .dropdown-menu {
            min-width: 200px;
            padding: 0.5rem 0;
        }
        
        /* Centrowanie nawigacji niezależnie od widoczności przycisku admin */
        .navbar-nav.mx-auto {
            margin-left: auto !important;
            margin-right: auto !important;
            display: flex;
            justify-content: center;
        }
        
        /* Zmniejszenie wysokości nagłówka */
        header.bg-dark {
            min-height: 60px;
            display: flex;
            align-items: center;
        }
    </style>
    @yield('styles')
    @yield('extra_css')
</head>
<body>
    <header class="bg-dark text-white py-2">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <!-- Logo/Title section on the left -->
                <div class="navbar-brand">
                    <h1 class="h3 mb-0">@yield('header', 'Rozkład jazdy autobusów')</h1>
                </div>
                
                <!-- Navigation section in the center -->
                <nav class="navbar navbar-expand-lg navbar-dark py-0">
                    <div class="container-fluid p-0">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="collapse navbar-collapse" id="navbarMain">
                            <ul class="navbar-nav mx-auto mb-0">
                                <li class="nav-item">
                                    <a class="nav-link active" href="{{ route('home') }}"><i class="fas fa-home me-2"></i>Strona główna</a>
                                </li>
                                
                                @auth
                                    {{-- Show admin panel link only to admin users --}}
                                    @if(Auth::user()->role === 'admin')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('admin') }}"><i class="fas fa-user-shield me-2"></i>Panel administratora</a>
                                        </li>
                                    @endif
                                    
                                    {{-- User account dropdown --}}
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-user-circle me-2"></i>{{ Auth::user()->name }}
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('lines.favorites') }}">
                                                    <i class="fas fa-star"></i>
                                                    <span>Ulubione linie</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('tickets.index') }}">
                                                    <i class="fas fa-ticket-alt"></i>
                                                    <span>Bilety</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('account.index') }}">
                                                    <i class="fas fa-cog"></i>
                                                    <span>Ustawienia konta</span>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="{{ route('logout') }}" class="w-100">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item w-100 text-start">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                        <span>Wyloguj</span>
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </li>
                                @else
                                    {{-- Show login/register links only for guests --}}
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-2"></i>Logowanie</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}"><i class="fas fa-user-plus me-2"></i>Rejestracja</a>
                                    </li>
                                @endauth
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
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
