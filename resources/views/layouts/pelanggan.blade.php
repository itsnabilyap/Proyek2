<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - @bucket.imy</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg: #fff0f3;
            --card: #fff7f8;
            --line: #f4d5dc;
            --rose: #8c3b50;
            --rose-dark: #732f41;
            --rose-soft: #fde4ea;
            --text: #2b1d21;
            --muted: #8a7077;
        }
        * { box-sizing: border-box; }
        [hidden] { display: none !important; }
        body {
            margin: 0;
            display: flex;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 13px;
            color: var(--text);
            background: var(--bg);
        }
        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; }

        /* ===== Sidebar ===== */
        .sidebar {
            position: sticky; top: 0;
            display: flex; flex-direction: column;
            width: 230px; height: 100vh; flex-shrink: 0;
            padding: 22px 0 18px;
            background: #fff5f7;
            border-right: 1px solid var(--line);
        }
        .sidebar-brand { padding: 0 22px 26px; }
        .sidebar-brand strong { display: block; font-family: 'Playfair Display', Georgia, serif; font-size: 17px; color: var(--rose); }
        .sidebar-brand small { color: var(--muted); font-size: 11px; }
        .side-link {
            display: flex; align-items: center; gap: 10px;
            margin: 2px 10px; padding: 11px 14px;
            border-radius: 12px; color: #4a383d; font-weight: 500;
            transition: background .15s;
        }
        .side-link i { font-size: 15px; }
        .side-link:hover { background: var(--rose-soft); }
        .side-link.active { background: var(--rose); color: #fff; }
        .sidebar-bottom { margin-top: auto; padding: 0 12px; }
        .side-user {
            display: flex; align-items: center; gap: 10px;
            padding: 12px; margin-bottom: 12px;
            background: #fff; border-radius: 14px;
            box-shadow: 0 2px 10px rgba(140,59,80,.08);
        }
        .side-user .dot { width: 32px; height: 32px; border-radius: 50%; background: #fbd3dc; }
        .side-user strong { display: block; font-size: 12px; }
        .side-user small { color: var(--muted); font-size: 11px; }
        .side-help { display: flex; align-items: center; gap: 6px; padding: 4px 6px; color: #5b484d; font-size: 12px; }

        /* ===== Konten ===== */
        .content { flex: 1; min-width: 0; }
        .topbar {
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            padding: 14px 26px;
            background: #fff7f8; border-bottom: 1px solid var(--line);
        }
        .topbar h2 { margin: 0; font-family: 'Playfair Display', Georgia, serif; font-size: 15px; color: var(--rose); }
        .topbar p { margin: 2px 0 0; color: var(--muted); font-size: 11px; }
        .topbar-actions { display: flex; align-items: center; gap: 14px; }
        .search {
            display: flex; align-items: center; gap: 8px;
            width: 260px; padding: 0 14px; height: 34px;
            background: #fff; border: 1px solid var(--line); border-radius: 20px;
        }
        .search input { flex: 1; min-width: 0; border: 0; outline: 0; background: transparent; font: inherit; font-size: 12px; }
        .search i { color: var(--muted); }
        .cart-link { position: relative; font-size: 18px; color: var(--rose); }
        .cart-link .badge {
            position: absolute; top: -6px; right: -8px;
            min-width: 15px; height: 15px; padding: 0 4px;
            border-radius: 10px; background: #e0566f; color: #fff;
            font-size: 9px; line-height: 15px; text-align: center;
        }

        /* ===== Profil + popup ===== */
        .account-menu { position: relative; }
        .avatar {
            width: 34px; height: 34px; border: 0; border-radius: 50%;
            background: #e08aa0; color: #fff; font-size: 14px; font-weight: 700; cursor: pointer;
        }
        .avatar:focus-visible { outline: 2px solid var(--rose); outline-offset: 2px; }
        .account-dropdown {
            position: absolute; top: calc(100% + 10px); right: 0; z-index: 50;
            width: 310px; max-width: calc(100vw - 24px); padding: 8px;
            background: #fff0f3; border: 1px solid #f7dce3; border-radius: 18px;
            box-shadow: 0 10px 28px rgba(120,55,70,.16);
        }
        .account-profile {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; margin-bottom: 6px;
            border: 1px solid #f7d2dc; border-radius: 14px;
        }
        .profile-avatar {
            display: grid; place-items: center; width: 42px; height: 42px; flex-shrink: 0;
            border-radius: 50%; background: #e08aa0; color: #fff; font-size: 18px; font-weight: 700;
        }
        .profile-info { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
        .profile-info strong { font-size: 14px; }
        .profile-info small { color: #765d64; font-size: 11px; overflow-wrap: anywhere; }
        .account-status {
            align-self: flex-start; padding: 2px 8px; border-radius: 20px;
            background: #d7edcf; color: #29472c; font-size: 10px;
        }
        .account-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 8px; border-radius: 10px; transition: background .15s;
        }
        .account-link:hover, .account-link.active { background: #f9dfe6; }
        .account-link > i:first-child { width: 22px; text-align: center; font-size: 16px; color: #a94760; }
        .account-link > i:last-child { margin-left: auto; font-size: 11px; color: #d0a5b0; }
        .account-link div { flex: 1; min-width: 0; }
        .account-link strong { display: block; font-size: 13px; font-weight: 600; }
        .account-link small { display: block; margin-top: 1px; color: #80666d; font-size: 11px; }
        .account-logout {
            display: flex; align-items: center; gap: 9px; width: 100%;
            margin-top: 4px; padding: 10px 8px 5px;
            border: 0; border-top: 1px solid #f1dce1; background: transparent;
            color: #d92727; font-size: 13px; cursor: pointer;
        }

        /* ===== Umum ===== */
        .page { padding: 26px 30px 48px; }
        .page h1 { margin: 0 0 4px; font-size: 22px; font-weight: 800; }
        .page .lead { margin: 0 0 18px; color: var(--muted); }
        .alert-success {
            margin-bottom: 16px; padding: 11px 16px; border-radius: 10px;
            background: #e5f4e5; color: #2f6a39; font-size: 12.5px;
        }
        .alert-error {
            margin-bottom: 16px; padding: 11px 16px; border-radius: 10px;
            background: #fce7eb; color: #9f2948; font-size: 12.5px;
        }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 9px 16px; border: 0; border-radius: 10px;
            background: var(--rose); color: #fff; font-size: 12px; font-weight: 600; cursor: pointer;
            transition: background .15s;
        }
        .btn:hover { background: var(--rose-dark); }

        @media (max-width: 860px) {
            body { flex-direction: column; }
            .sidebar {
                position: static; flex-direction: row; flex-wrap: wrap; align-items: center;
                width: 100%; height: auto; padding: 10px; gap: 4px;
            }
            .sidebar-brand { padding: 0 10px; width: 100%; }
            .side-link { margin: 0; padding: 8px 12px; font-size: 12px; }
            .sidebar-bottom { display: none; }
            .topbar { flex-wrap: wrap; padding: 12px 16px; }
            .search { width: 100%; order: 3; }
            .page { padding: 20px 16px 40px; }
        }
    </style>
    @stack('styles')
</head>
<body>

@php
    $user = auth()->user();
    $inisial = strtoupper(substr($user->name ?: $user->username, 0, 1));
    $namaDepan = explode(' ', trim($user->name ?: $user->username))[0];
    $jumlahKeranjang = \App\Models\Cart::where('id_pelanggan', $user->id)->sum('jumlah');
@endphp

<aside class="sidebar">
    <a href="{{ route('home') }}" class="sidebar-brand">
        <strong>@bucket.imy</strong>
        <small>Store Bucket Bunga</small>
    </a>

    <nav>
        <a href="{{ route('katalog.index') }}" class="side-link {{ request()->routeIs('katalog.*') ? 'active' : '' }}">
            <i class="bi bi-grid"></i> Katalog Produk
        </a>
        <a href="{{ route('pesanan.custom') }}" class="side-link {{ request()->routeIs('pesanan.custom*') ? 'active' : '' }}">
            <i class="bi bi-pencil"></i> Form Pemesanan Custom
        </a>
        <a href="{{ route('keranjang.index') }}" class="side-link {{ request()->routeIs('keranjang.*', 'pesanan.sukses') ? 'active' : '' }}">
            <i class="bi bi-cart3"></i> Check Out &amp; Keranjang
        </a>
        <a href="{{ route('pesanan.riwayat') }}" class="side-link {{ request()->routeIs('pesanan.riwayat') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Riwayat Pesanan
        </a>
    </nav>

    <div class="sidebar-bottom">
        <div class="side-user">
            <span class="dot"></span>
            <div><strong>{{ $namaDepan }}</strong><small>Pelanggan</small></div>
        </div>
        <a href="{{ url('/bantuan') }}" class="side-help"><i class="bi bi-question-circle"></i> Bantuan</a>
    </div>
</aside>

<div class="content">
    <header class="topbar">
        <div>
            <h2>Halo {{ $namaDepan }}, Selamat Datang di @Bucket.imy</h2>
            <p>Bikin momen spesialmu makin berkesan, yuk lengkapi data pesananmu di bawah ini.</p>
        </div>

        <div class="topbar-actions">
            @yield('topbar-search')

            <a href="{{ route('keranjang.index') }}" class="cart-link" aria-label="Keranjang">
                <i class="bi bi-cart3"></i>
                @if($jumlahKeranjang > 0)<span class="badge">{{ $jumlahKeranjang }}</span>@endif
            </a>

            {{-- Profil -> popup --}}
            <div class="account-menu">
                <button type="button" class="avatar" id="accountButton"
                    aria-expanded="false" aria-controls="accountDropdown">{{ $inisial }}</button>

                <div class="account-dropdown" id="accountDropdown" hidden>
                    <div class="account-profile">
                        <div class="profile-avatar">{{ $inisial }}</div>
                        <div class="profile-info">
                            <strong>{{ $user->username }}</strong>
                            <small>{{ $user->email }}</small>
                            <span class="account-status">ACTIVE</span>
                        </div>
                    </div>

                    <a href="{{ route('home') }}" class="account-link">
                        <i class="bi bi-house"></i>
                        <div><strong>Beranda</strong><small>Kembali ke halaman utama</small></div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="{{ route('katalog.index') }}" class="account-link {{ request()->routeIs('katalog.*') ? 'active' : '' }}">
                        <i class="bi bi-flower1"></i>
                        <div><strong>Katalog Koleksi Bucket</strong><small>Jelajahi Rangkaian Bunga</small></div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="{{ route('pesanan.custom') }}" class="account-link {{ request()->routeIs('pesanan.custom*') ? 'active' : '' }}">
                        <i class="bi bi-pencil-square"></i>
                        <div><strong>Form Pemesanan Custom</strong><small>Rancang Bucket Sesuai Keinginan</small></div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="{{ route('pesanan.riwayat') }}" class="account-link {{ request()->routeIs('pesanan.riwayat') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        <div><strong>Status Pesanan &amp; Riwayat</strong><small>Lacak Pengiriman</small></div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="{{ route('keranjang.index') }}" class="account-link">
                        <i class="bi bi-bag"></i>
                        <div><strong>Keranjang &amp; Checkout</strong><small>Rangkaian Siap Diproses</small></div>
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="account-logout">
                            <i class="bi bi-box-arrow-right"></i> Keluar / Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="page">
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>
</div>

<script>
    (function () {
        const btn = document.getElementById('accountButton');
        const menu = document.getElementById('accountDropdown');
        if (!btn || !menu) return;

        const setOpen = (open) => {
            menu.hidden = !open;
            btn.setAttribute('aria-expanded', String(open));
        };

        btn.addEventListener('click', (e) => { e.stopPropagation(); setOpen(menu.hidden); });
        menu.addEventListener('click', (e) => e.stopPropagation());
        document.addEventListener('click', () => setOpen(false));
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') { setOpen(false); btn.focus(); }
        });
    })();
</script>
@stack('scripts')
</body>
</html>
