
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>@yield('title') - @bucket.imy</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #21191b;
            background: #fff0f3;
            font-size: 13px;
        }

        a {
            color: inherit;
        }

        .navbar {
            position: relative;
            z-index: 1000;
            height: 50px;
            padding: 0 18px;
            background: #fffafb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            border-bottom: 1px solid #f7e8eb;
        }

        .brand {
            font-family: Georgia, serif;
            color: #a84b61;
            text-decoration: none;
            white-space: nowrap;
            font-size: 14px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-links a {
            text-decoration: none;
            color: #65565a;
            white-space: nowrap;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-actions a:not(.icon-keranjang) {
            text-decoration: none;
            border: 1px solid #ad4e66;
            border-radius: 20px;
            padding: 4px 15px;
            white-space: nowrap;
        }

        .nav-actions .filled {
            color: white;
            background: #a84b61;
        }

        .icon-keranjang {
            margin-left: 8px;
            color: #111;
            font-size: 27px;
            text-decoration: none;
        }

        /* Avatar navbar */
        button.avatar {
            appearance: none;
            -webkit-appearance: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            margin-left: 6px;
            padding: 0;
            border: 0 !important;
            outline: 0;
            box-shadow: none !important;
            border-radius: 50%;
            background: #d9798d;
            color: #fff;
            font-family: Arial, sans-serif;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
        }

        button.avatar::before,
        button.avatar::after {
            content: none !important;
            display: none !important;
        }

        button.avatar:hover,
        button.avatar:focus,
        button.avatar:focus-visible,
        button.avatar:active {
            border: 0 !important;
            outline: 0 !important;
            box-shadow: none !important;
        }

        main {
            min-height: 435px;
            padding: 26px 20px 45px;
            background:
                radial-gradient(ellipse at 8% 12%, #ffe6eb 0%, transparent 45%),
                radial-gradient(ellipse at 95% 85%, #ffe6eb 0%, transparent 45%),
                #fff0f3;
        }

        main.main-home {
            min-height: auto;
            padding: 0;
            margin: 0;
            width: 100%;
            background: #fff8f8;
        }

        .auth-container {
            width: 100%;
            max-width: 315px;
            margin: 0 auto;
        }

        .auth-title {
            text-align: center;
            color: #a84b61;
            font-size: 17px;
            margin: 0 0 10px;
        }

        .auth-subtitle {
            text-align: center;
            margin: 0 0 25px;
        }

        .auth-subtitle a {
            text-decoration: underline;
        }

        .form-input {
            width: 100%;
            height: 36px;
            padding: 0 13px;
            border: 1px solid #f0dfe3;
            background: #fffafa;
            outline-color: #c76b8a;
            margin-bottom: 15px;
            font-size: 12px;
        }

        .btn-akun,
        .btn-daftar {
            padding: 8px 22px;
            border: 1px solid #b44d67;
            border-radius: 25px;
            color: #21191b;
            text-decoration: none;
        }

        .btn-keluar,
        .btn-masuk {
            padding: 8px 22px;
            border: none;
            border-radius: 25px;
            background: #ad4963;
            color: white;
            cursor: pointer;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap .form-input {
            padding-right: 42px;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 9px;
            border: 0;
            background: transparent;
            cursor: pointer;
            color: #8b777d;
            padding: 0;
            font-size: 15px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: -10px 0 12px;
            font-size: 12px;
        }

        .remember input {
            margin: 0;
        }

        .forgot {
            text-align: right;
            margin: -5px 0 5px;
        }

        .forgot a {
            text-decoration: none;
        }

        .primary-button {
            padding: 12px 23px;
            border: 0;
            border-radius: 3px;
            background: #a84b61;
            color: white;
            cursor: pointer;
        }

        .secondary-link {
            margin-left: 12px;
            text-decoration: none;
        }

        .alert {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            background: #fce7eb;
            color: #9f2948;
        }

        /* Dropdown akun */
        .account-menu {
            position: relative;
        }

        .account-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 320px;
            max-width: calc(100vw - 24px);
            padding: 8px;
            border: 1px solid #f7dce3;
            border-radius: 18px;
            background: #fff0f3;
            box-shadow: 0 6px 18px rgba(120, 55, 70, 0.10);
            z-index: 2000;
            overflow: hidden;
        }

        .account-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px;
            margin-bottom: 5px;
            border: 1px solid #f7d2dc;
            border-radius: 14px;
            background: #fff0f3;
        }

        .profile-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            border: none;
            outline: none;
            box-shadow: none;
            border-radius: 50%;
            background: #d9798d;
            color: #fff;
            font-size: 20px;
        }

        .profile-info {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 3px;
            min-width: 0;
        }

        .profile-info strong {
            color: #302329;
            font-size: 14px;
            font-weight: 600;
        }

        .profile-info small {
            color: #765d64;
            font-size: 12px;
            overflow-wrap: anywhere;
        }

        .account-status {
            align-self: flex-start;
            padding: 3px 8px;
            border-radius: 20px;
            background: #d7edcf;
            color: #29472c;
            font-size: 10px;
        }

        .account-link,
        .account-dropdown a.account-link {
            display: flex;
            align-items: center;
            gap: 9px;
            width: 100%;
            margin: 0;
            padding: 9px 7px;
            border: none !important;
            border-radius: 8px !important;
            background: transparent;
            box-shadow: none !important;
            color: #35262b;
            text-decoration: none;
        }

        .account-link:hover {
            background: #fbe1e7;
        }

        .account-link > i:first-child {
            width: 22px;
            flex-shrink: 0;
            color: #a94760;
            font-size: 16px;
            text-align: center;
        }

        .account-link > i:last-child {
            margin-left: auto;
            color: #d9b7c0;
            font-size: 11px;
        }

        .account-link div {
            flex: 1;
            min-width: 0;
        }

        .account-link strong,
        .account-link small {
            display: block;
            text-align: left;
        }

        .account-link strong {
            color: #35262b;
            font-size: 13px;
            font-weight: 500;
        }

        .account-link small {
            margin-top: 2px;
            color: #80666d;
            font-size: 11px;
            line-height: 1.35;
        }

        .account-logout {
            display: flex;
            align-items: center;
            gap: 9px;
            width: 100%;
            margin-top: 4px;
            padding: 10px 7px 5px;
            border: none;
            border-top: 1px solid #f1dce1;
            border-radius: 0;
            background: transparent;
            color: #d92727;
            font-size: 13px;
            text-align: left;
            cursor: pointer;
        }

        .account-logout:hover {
            background: #ffe3e8;
        }

        [hidden] {
            display: none !important;
        }

        /* Footer */
        .footer {
            min-height: 185px;
            padding: 32px 5% 15px;
            background: #fff0f3;
            border-top: 1px solid #f7e8eb;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.4fr 1.3fr 1.4fr;
            gap: 25px;
            align-items: center;
        }

        .footer-brand {
            font-family: Georgia, serif;
            font-size: 24px;
            font-weight: bold;
            white-space: nowrap;
        }

        .footer h4 {
            font-size: 12px;
            margin: 0 0 6px;
        }

        .footer p {
            color: #8d7078;
            font-size: 9px;
            line-height: 1.7;
            margin: 4px 0;
        }

        .copyright {
            border-top: 1px solid #f8e6ea;
            margin-top: 20px;
            padding-top: 13px;
            color: #8d7078;
            font-size: 9px;
        }

        .alert-success {
            margin-bottom: 15px;
            padding: 12px 22px;
            border-radius: 6px;
            background: #e5f4e5;
            color: #326b3b;
            font-size: 14px;
        }

        
/* HOVER MENU DROPDOWN */
.account-link {
    transition: background 0.2s ease, color 0.2s ease;
}

.account-link:hover {
    background: #f9dfe6 !important;
    color: #a84b61;
}

.account-link:hover > i:first-child {
    color: #a84b61;
}

.account-link:hover > i:last-child {
    color: #a84b61;
}

/* DASHBOARD AKTIF */
.account-link.active {
    background: #f9dfe6 !important;
    color: #a84b61;
}

.account-link.active > i:first-child,
.account-link.active > i:last-child {
    color: #a84b61;
}

.nav-links a:hover {
    color: #a84b61;
}


        @media (max-width: 650px) {
            .navbar {
                height: auto;
                min-height: 50px;
                flex-wrap: wrap;
                padding: 12px;
            }

            .nav-links {
                order: 3;
                width: 100%;
                justify-content: center;
                gap: 15px;
                font-size: 11px;
            }

            .nav-actions a {
                padding: 4px 10px;
            }

            main {
                min-height: 430px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .footer-brand {
                font-size: 22px;
            }

            .account-dropdown {
                right: -5px;
                width: 300px;
                max-width: calc(100vw - 24px);
            }

            .account-link {
                padding: 9px 7px;
            }
        }
    </style>
</head>
<body>

<header class="navbar">
    <a href="{{ route('home') }}" class="brand">@bucket.imy</a>

    <nav class="nav-links">
        <a href="{{ route('beranda') }}">Beranda</a>
        <a href="{{ route('home') }}#katalog">Katalog</a>
        <a href="{{ url('/#cara-pesan') }}">Cara Pesan</a>
        <a href="{{ url('/#tentang-kami') }}">Tentang Kami</a>
    </nav>

    <div class="nav-actions">
        @auth
            <div class="account-menu">
                <button
                    type="button"
                    class="avatar"
                    id="accountButton"
                    aria-expanded="false"
                    aria-controls="accountDropdown">
                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                </button>

                <div class="account-dropdown" id="accountDropdown" hidden>
                    <div class="account-profile">
                        <div class="profile-avatar">
                            {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                        </div>

                        <div class="profile-info">
                            <strong>{{ auth()->user()->username }}</strong>
                            <small>{{ auth()->user()->email }}</small>
                            <span class="account-status">ACTIVE</span>
                        </div>
                    </div>

                    <a href="{{ route('home') }}"
                        class="account-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i>
                        <div>
                            <strong>Dashboard Pelanggan</strong>
                            <small>Ringkasan & Profil Saya</small>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <a href="{{ route('home') }}#katalog" class="account-link">
                        <i class="bi bi-flower1"></i>
                        <div>
                            <strong>Katalog Koleksi Bucket</strong>
                            <small>Jelajahi Rangkaian Bunga</small>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <a href="{{ url('/pesanan/custom') }}" class="account-link">
                        <i class="bi bi-pencil-square"></i>
                        <div>
                            <strong>Form Pemesanan Custom</strong>
                            <small>Rancang Bucket Sesuai Keinginan</small>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <a href="{{ url('/pesanan/riwayat') }}" class="account-link">
                        <i class="bi bi-clock-history"></i>
                        <div>
                            <strong>Status Pesanan & Riwayat</strong>
                            <small>Lacak Pengiriman</small>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <a href="{{ url('/keranjang') }}" class="account-link">
                        <i class="bi bi-bag"></i>
                        <div>
                            <strong>Keranjang & Checkout</strong>
                            <small>Rangkaian Siap Diproses</small>
                        </div>
                    </a>

                    <a href="{{ url('/bantuan') }}" class="account-link">
                        <i class="bi bi-headset"></i>
                        <div>
                            <strong>Pusat Bantuan & WhatsApp Florist</strong>
                            <small>Chat Tim Konsultan Bunga</small>
                        </div>
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="account-logout">
                            <i class="bi bi-box-arrow-right"></i>
                            Keluar / Logout
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ url('/register') }}" class="btn-daftar">Daftar</a>
            <a href="{{ route('login') }}" class="btn-masuk">Masuk</a>
        @endauth
    </div>
</header>

<main class="@yield('main-class')">
    @yield('content')
</main>

<footer class="footer">
    <div class="footer-grid">
        <div class="footer-brand">@bucket.imy</div>

        <div>
            <h4>Atelier &amp; Jam Buka</h4>
            <p>
                <a href="https://www.google.com/maps/search/?api=1&query=Jl.+Tridaya+Barat,+Karanganyar,+Indramayu,+Jawa+Barat+45213"
                   target="_blank"
                   rel="noopener noreferrer">
                    Jl. Tridaya Barat, Karanganyar, Kec. Indramayu,
                    Kabupaten Indramayu, Jawa Barat 45213, Indonesia
                </a>
            </p>
            <p>
                <strong>Senin – Sabtu:</strong> 08.00 – 19.00 WIB<br>
                <strong>Minggu:</strong> By Appointment Only
            </p>
        </div>

        <div>
            <h4>Kontak &amp; Media Sosial</h4>
            <p>Ikuti sosial media kami!</p>
            <p>
                Instagram:
                <a href="https://www.instagram.com/bucket.imy/"
                   target="_blank" rel="noopener noreferrer">@bucket.imy</a>
            </p>
            <p>
                TikTok:
                <a href="https://www.tiktok.com/@bucket.imy"
                   target="_blank" rel="noopener noreferrer">@bucket.imy</a>
            </p>
            <p>
                WhatsApp:
                <a href="https://wa.me/62895325820465"
                   target="_blank" rel="noopener noreferrer">+62 895-3258-20465</a>
            </p>
        </div>
    </div>

    <div class="copyright">
        @2026 @bucket.imy
    </div>
</footer>

<script>
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);

            if (!input) return;

            input.type = input.type === 'password' ? 'text' : 'password';
            button.textContent = input.type === 'password' ? '◉' : '◎';
        });
    });

    const accountButton = document.getElementById('accountButton');
    const accountDropdown = document.getElementById('accountDropdown');

    if (accountButton && accountDropdown) {
        accountButton.addEventListener('click', function (event) {
            event.stopPropagation();

            const isOpen = !accountDropdown.hidden;
            accountDropdown.hidden = isOpen;
            accountButton.setAttribute('aria-expanded', String(!isOpen));
        });

        accountDropdown.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        document.addEventListener('click', function () {
            accountDropdown.hidden = true;
            accountButton.setAttribute('aria-expanded', 'false');
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                accountDropdown.hidden = true;
                accountButton.setAttribute('aria-expanded', 'false');
                accountButton.focus();
            }
        });
    }
</script>

</body>
</html>
