<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CapBay Auto - AI Car Portal')</title>
    <!-- Premium stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('styles')
</head>
<body>

    <header>
        <div class="container">
            <div class="nav-wrapper">
                <a href="{{ route('customer.register') }}" class="logo">
                    CapBay <span>Auto</span>
                </a>
                <nav class="nav-links">
                    <a href="{{ route('customer.register') }}" class="nav-link {{ request()->routeIs('customer.register') ? 'active' : '' }}">Customer Register</a>
                    <a href="{{ route('agent.index') }}" class="nav-link {{ request()->routeIs('agent.*') ? 'active' : '' }}">Agent Dashboard</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 CapBay Auto Sdn. Bhd. (1189545-D). All rights reserved.</p>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
