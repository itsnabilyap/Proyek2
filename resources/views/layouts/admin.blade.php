<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - @bucket.imy</title>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body>

<div class="admin-wrap">

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <span class="brand">@bucket.imy</span>
            <small>DASHBOARD {{ strtoupper($user->role ?? 'ADMIN') }}</small>
        </div>

        <nav class="sidebar-nav">
            @foreach ($menu as [$label, $route, $icon])
                @php
                    $ada   = Route::has($route);
                    $aktif = $ada && request()->routeIs(str_replace('.index', '', $route) . '*');
                @endphp
                <a href="{{ $ada ? route($route) : '#' }}"
                   class="sidebar-link {{ $aktif ? 'active' : '' }}"
                   @if ($aktif) aria-current="page" @endif>
                    <i class="bi {{ $icon }}"></i>
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </nav>

        <div class="sidebar-user">
            <div class="user-avatar">{{ strtoupper(substr($nama, 0, 1)) }}</div>
            <div class="user-info">
                <strong>{{ $nama }}</strong>
                <small>{{ ucfirst($user->role ?? 'admin') }}</small>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout" aria-label="Keluar" title="Keluar">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <button type="button" class="btn-menu" id="menuButton" aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">
                <i class="bi bi-list"></i>
            </button>
            <span class="topbar-title">Dashboard Admin</span>
            <span class="topbar-date">
                <i class="bi bi-calendar3"></i>
                Hari ini: {{ now()->locale('id')->translatedFormat('l, j F Y') }}
            </span>
        </header>

        @if (session('status'))
            <div class="flash flash-ok">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="flash flash-error">{{ $errors->first() }}</div>
        @endif

        @yield('content')
    </main>

</div>

<script>
    // Menu sidebar di layar HP
    const menuButton = document.getElementById('menuButton');
    const sidebar = document.getElementById('sidebar');

    menuButton?.addEventListener('click', () => {
        const buka = sidebar.classList.toggle('open');
        menuButton.setAttribute('aria-expanded', String(buka));
    });
</script>
@stack('scripts')

</body>
</html>