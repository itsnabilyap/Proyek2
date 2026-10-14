<div class="account-menu" id="accountMenu">
    <button type="button" class="account-button" id="accountButton"
        aria-expanded="false" aria-controls="accountDropdown">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
    </button>

    <div class="account-dropdown" id="accountDropdown" hidden>
        <div class="account-profile">
            <div class="account-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <div class="account-info">
                <strong>{{ auth()->user()->name }}</strong>
                <small>{{ auth()->user()->email }}</small>
                <span class="account-status">ACTIVE</span>
            </div>
        </div>

        <nav class="account-links">
            <a href="{{ route('dashboard') }}">
                <span>▦</span>
                <div>
                    <strong>Dashboard Pelanggan</strong>
                    <small>Ringkasan & Profil Saya</small>
                </div>
                <span>›</span>
            </a>

            <a href="{{ route('katalog.index') }}">
                <span>▧</span>
                <div>
                    <strong>Katalog Koleksi Bucket</strong>
                    <small>Jelajahi Rangkaian Bunga</small>
                </div>
                <span>›</span>
            </a>

            <a href="{{ route('pesanan.custom') }}">
                <span>▤</span>
                <div>
                    <strong>Form Pemesanan Custom</strong>
                    <small>Rancang Bucket Sesuai Keinginan</small>
                </div>
                <span>›</span>
            </a>

            <a href="{{ route('pesanan.riwayat') }}">
                <span>▣</span>
                <div>
                    <strong>Status Pesanan & Riwayat</strong>
                    <small>Lacak Pengiriman</small>
                </div>
                <span>›</span>
            </a>

            <a href="{{ route('keranjang.index') }}">
                <span>▢</span>
                <div>
                    <strong>Keranjang & Checkout</strong>
                    <small>Rangkaian Siap Diproses</small>
                </div>
            </a>

            <a href="{{ route('bantuan.index') }}">
                <span>♡</span>
                <div>
                    <strong>Pusat Bantuan & WhatsApp Florist</strong>
                    <small>Chat Tim Konsultan Bunga</small>
                </div>
            </a>
        </nav>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-button">
                ↪ Keluar / Logout
            </button>
        </form>
    </div>
</div>