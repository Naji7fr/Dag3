<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kniploket Tiko')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="page-shell">
    <header class="site-header">
        <div class="header-inner">
            <a href="{{ route('home') }}" class="brand">KNIPLOKET TIKO</a>

            <button
                type="button"
                class="menu-toggle"
                id="menu-toggle"
                aria-label="Menu openen"
                aria-expanded="false"
                aria-controls="main-nav"
            >
                <span class="menu-toggle-bar"></span>
                <span class="menu-toggle-bar"></span>
                <span class="menu-toggle-bar"></span>
            </button>

            <nav class="main-nav" id="main-nav">
                <a href="{{ route('home') }}" @class(['active' => ($activeNav ?? '') === 'home'])>Accounts</a>
                @auth
                    @if(auth()->user()->isEigenaar())
                        <a href="{{ route('medewerkers.index') }}" @class(['active' => ($activeNav ?? '') === 'medewerkers'])>Medewerkers</a>
                    @endif
                @endauth
                <a href="#">Beschikbaarheid</a>
                @auth
                    @if(auth()->user()->isEigenaar())
                        <a href="{{ route('klanten.index') }}" @class(['active' => ($activeNav ?? '') === 'klanten'])>Klanten</a>
                    @endif
                @endauth
                <a href="#">Afspraken</a>
                <a href="#">Behandelingen</a>
                <a href="#">Producten</a>
                <a href="{{ route('bestellingen.index') }}" @class(['active' => ($activeNav ?? '') === 'bestellingen'])>Bestellingen</a>

                <div class="mobile-user-panel">
                    @auth
                        <span class="user-label">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                        <form method="post" action="{{ route('logout') }}" class="logout-form">
                            @csrf
                            <button type="submit" class="btn-logout btn-logout--mobile">Uitloggen</button>
                        </form>
                    @endauth
                </div>
            </nav>

            <div class="header-right">
                @auth
                    <span class="user-label">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                    <form method="post" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="btn-logout">Uitloggen</button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <div class="nav-overlay" id="nav-overlay" hidden></div>

    <main class="page-content">
        @yield('content')
    </main>

    <footer class="site-footer">
        &copy; 2026 Kniploket Tiko - Alle rechten voorbehouden
    </footer>
</div>

@if(!empty($autoHideFlash))
<script>
    setTimeout(function () {
        var alertElement = document.getElementById('flash-alert');
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    }, {{ (int) ($flashAutoHideMs ?? config('kniploket.flash_auto_hide_ms', 3000)) }});
</script>
@endif
</body>
</html>
