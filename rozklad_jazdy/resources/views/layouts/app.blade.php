<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rozkład jazdy')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('extra_css')
</head>
<body>
    <header>
        <h1>@yield('header', 'Rozkład jazdy autobusów')</h1>
        <nav>
            @yield('navigation')
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2025 Rozkład jazdy – jazda z busami</p>
    </footer>

    @yield('scripts')
</body>
</html>
