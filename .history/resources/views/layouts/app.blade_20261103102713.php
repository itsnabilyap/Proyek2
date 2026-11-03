
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>@yield('title') - @bucket.imy</title>

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
