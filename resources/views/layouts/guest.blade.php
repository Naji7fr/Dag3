<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inloggen - Kniploket Tiko')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="guest-body">
<div class="page-shell">
    <header class="site-header site-header--minimal">
        <div class="brand">KNIPLOKET TIKO</div>
    </header>

    <main class="page-content page-content--centered">
        @yield('content')
    </main>

    <footer class="site-footer">
        &copy; 2026 Kniploket Tiko - Alle rechten voorbehouden
    </footer>
</div>
</body>
</html>
