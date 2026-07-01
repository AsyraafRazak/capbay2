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
                    @auth
                        <a href="{{ route('agent.index') }}" class="nav-link {{ request()->routeIs('agent.*') ? 'active' : '' }}">Agent Dashboard</a>
                        <span class="nav-link" style="color: var(--text-muted); cursor: default;">{{ Auth::user()->name }}</span>
                        <form action="{{ route('agent.logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.85rem;">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('agent.login') }}" class="nav-link {{ request()->routeIs('agent.login') ? 'active' : '' }}">Agent Login</a>
                    @endauth
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
