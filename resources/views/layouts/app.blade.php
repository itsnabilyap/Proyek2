<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') - @bucket.imy</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- ========== HEADER / NAVBAR ========== --}}
<header class="sticky top-0 z-50 flex flex-wrap items-center justify-between gap-x-6 gap-y-2 border-b border-line-soft bg-white px-4 py-2.5 sm:px-5 lg:flex-nowrap lg:px-7 xl:px-11 xl:py-3">
    <a href="{{ url('/') }}" class="font-serif text-[22px] font-semibold text-maroon">@bucket.imy</a>

    @php
        $menuNav = [
            [url('/'), 'Beranda', request()->routeIs('home')],
            [route('katalog.index'), 'Katalog', request()->routeIs('katalog.*')],
            [route('custom.create'), 'Custom', request()->routeIs('custom.*')],
            [route('home') . '#cara-pesan', 'Cara Pesan', false],
            [route('home') . '#tentang-kami', 'Tentang Kami', false],
        ];
    @endphp
    <nav data-nav class="order-3 flex w-full gap-1 overflow-x-auto sm:justify-center lg:order-none lg:w-auto lg:gap-2">
        @foreach ($menuNav as [$href, $label, $aktif])
            <a href="{{ $href }}" @if ($aktif) aria-current="page" @endif
               class="shrink-0 rounded-full px-3 py-1.5 text-[13px] text-ink-soft transition-colors hover:bg-blush-50 hover:text-maroon aria-[current=page]:bg-blush aria-[current=page]:font-semibold aria-[current=page]:text-maroon sm:px-4 sm:py-2 sm:text-sm">
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <div class="flex items-center gap-3">
        @auth
            @php
                $user = auth()->user();
                $kata = preg_split('/\s+/', trim($user->name));
                $inisial = strtoupper(substr($kata[0], 0, 1) . substr($kata[1] ?? '', 0, 1));
                $namaDepan = $kata[0];
                $jumlahKeranjang = $user->keranjang()->sum('jumlah');
                $pesananAktif = $user->pesanan()->whereNotIn('status_pesanan', ['selesai', 'dibatalkan'])->count();
            @endphp

            {{-- Keranjang --}}
            <a href="{{ route('keranjang.index') }}" aria-label="Keranjang, {{ $jumlahKeranjang }} item"
               class="relative inline-flex size-[46px] items-center justify-center rounded-full border border-line bg-white text-lg text-maroon transition hover:-translate-y-px hover:border-maroon hover:bg-blush-50">
                <i class="bi bi-cart3"></i>
                @if ($jumlahKeranjang > 0)
                    <span class="absolute -top-1 -right-1 flex h-5 min-w-5 items-center justify-center rounded-full border-2 border-white bg-maroon px-1 text-[11px] font-bold text-white">{{ $jumlahKeranjang }}</span>
                @endif
            </a>

            {{-- Profil + dropdown --}}
            <div class="relative">
                <button type="button" id="accountButton" aria-expanded="false" aria-controls="accountDropdown"
                        class="group inline-flex h-[46px] items-center gap-2.5 rounded-full border border-line bg-white py-0 pr-3.5 pl-1.5 transition-colors hover:border-maroon hover:bg-blush-50">
                    <span class="inline-flex size-9 items-center justify-center rounded-full bg-maroon text-[13px] font-bold text-white">{{ $inisial }}</span>
                    <span class="text-[15px] font-semibold max-sm:hidden">{{ $namaDepan }}</span>
                    <i class="bi bi-chevron-down text-xs text-muted transition-transform group-aria-expanded:rotate-180"></i>
                </button>

                <div id="accountDropdown" hidden
                     class="absolute top-[calc(100%+12px)] right-0 z-30 w-[min(340px,calc(100vw-32px))] rounded-[20px] border border-line-soft bg-white p-4.5 shadow-pop">
                    <div class="flex items-center gap-3.5 px-1.5 pt-1 pb-4">
                        <span class="inline-flex size-13 shrink-0 items-center justify-center rounded-full bg-maroon text-base font-bold text-white">{{ $inisial }}</span>
                        <div class="flex min-w-0 flex-col">
                            <strong class="text-base">{{ $user->name }}</strong>
                            <small class="truncate text-[13px] text-muted">{{ $user->email }}</small>
                        </div>
                    </div>

                    @php
                        $menuAkun = [
                            [url('/pesanan'), 'bi-bag', 'Pesanan Saya', 'Status dan riwayat pesanan', $pesananAktif],
                            [route('akun.edit'), 'bi-person', 'Pengaturan Akun', 'Edit profil dan ganti kata sandi', 0],
                            ['https://wa.me/6283184710305', 'bi-chat', 'Bantuan', 'Chat admin via WhatsApp', 0],
                        ];
                    @endphp
                    @foreach ($menuAkun as [$href, $ikon, $judul, $ket, $angka])
                        <a href="{{ $href }}" @if (str_starts_with($href, 'https://wa.me')) target="_blank" rel="noopener noreferrer" @endif
                           class="group/link flex w-full items-center gap-3.5 rounded-xl px-1.5 py-2 transition-colors hover:bg-blush focus-visible:bg-blush focus-visible:outline-none">
                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-[10px] bg-blush text-base text-maroon transition-colors group-hover/link:bg-maroon group-hover/link:text-white">
                                <i class="bi {{ $ikon }}"></i>
                            </span>
                            <span class="flex flex-1 flex-col">
                                <strong class="text-sm font-semibold group-hover/link:text-maroon">{{ $judul }}</strong>
                                <small class="text-xs text-muted">{{ $ket }}</small>
                            </span>
                            @if ($angka > 0)
                                <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-maroon px-1.5 text-xs font-bold text-white">{{ $angka }}</span>
                            @endif
                        </a>
                    @endforeach

                    <form action="{{ route('logout') }}" method="POST" class="mt-2 border-t border-line-soft pt-2">
                        @csrf
                        <button type="submit" class="group/link flex w-full items-center gap-3.5 rounded-xl px-1.5 py-2 text-left transition-colors hover:bg-danger-soft">
                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-[10px] bg-danger-soft text-base text-danger transition-colors group-hover/link:bg-danger group-hover/link:text-white">
                                <i class="bi bi-box-arrow-right"></i>
                            </span>
                            <strong class="text-sm text-danger">Keluar</strong>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('register') }}" class="btn btn-soft min-h-[38px] border-maroon px-4 font-normal">Daftar</a>
            <a href="{{ route('login') }}" class="btn btn-primary min-h-[38px] px-4 font-normal">Masuk</a>
        @endauth
    </div>
</header>

{{-- Notifikasi (toast) di bawah navbar. Halaman dengan @section('tanpa-flash') menampilkan pesannya sendiri. --}}
@sectionMissing('tanpa-flash')
    <x-toast class="top-20" />
@endif
<x-konfirmasi />

<main class="@yield('main-class')">
    @yield('content')
</main>

{{-- ========== FOOTER ========== --}}
@hasSection('footer-ringkas')
    <footer class="flex flex-col items-center justify-between gap-3 border-t border-line-soft bg-blush-300 px-4 py-5 text-center text-[13px] text-muted sm:flex-row sm:flex-wrap sm:px-7 sm:py-6 sm:text-left xl:px-11 xl:py-7">
        <span class="font-serif text-2xl text-ink">@bucket.imy</span>
        <span>Butuh bantuan pesanan?
            <a href="https://wa.me/6283184710305" target="_blank" rel="noopener noreferrer" class="font-semibold text-maroon hover:underline">Chat admin via WhatsApp</a>
        </span>
        <span>© {{ date('Y') }} @bucket.imy</span>
    </footer>
@else
    <footer class="border-t border-line-soft bg-blush-300 px-5 pt-9 pb-5 sm:px-7 xl:px-11 xl:pt-11">
        <div class="grid gap-7 md:grid-cols-3">
            <div class="font-serif text-[34px]">@bucket.imy</div>

            <div class="text-[13px] leading-relaxed text-muted">
                <h4 class="mb-2.5 text-[15px] font-bold text-ink">Atelier &amp; Jam Buka</h4>
                <p class="mb-1.5">
                    <a href="https://www.google.com/maps/search/?api=1&query=Jl.+Tridaya+Barat,+Karanganyar,+Indramayu,+Jawa+Barat+45213"
                       target="_blank" rel="noopener noreferrer" class="text-maroon hover:underline">
                        Jl. Tridaya Barat, Karanganyar, Kec. Indramayu, Kabupaten Indramayu, Jawa Barat 45213
                    </a>
                </p>
                <p>
                    <strong>Senin – Sabtu:</strong> 08.00 – 19.00 WIB<br>
                    <strong>Minggu:</strong> By Appointment Only
                </p>
            </div>

            <div class="text-[13px] leading-relaxed">
                <h4 class="mb-2.5 text-[15px] font-bold">Kontak &amp; Media Sosial</h4>
                <p class="mb-1.5"><a href="https://www.instagram.com/bucket.imy/" target="_blank" rel="noopener noreferrer" class="text-maroon hover:underline">Instagram: @bucket.imy</a></p>
                <p class="mb-1.5"><a href="https://www.tiktok.com/@bucket.imy" target="_blank" rel="noopener noreferrer" class="text-maroon hover:underline">TikTok: @bucket.imy</a></p>
                <p><a href="https://wa.me/6283184710305" target="_blank" rel="noopener noreferrer" class="text-maroon hover:underline">WhatsApp: +62 895-3258-20465</a></p>
            </div>
        </div>

        <div class="mt-7 border-t border-line pt-4 text-center text-xs text-muted">© {{ date('Y') }} @bucket.imy</div>
    </footer>
@endif

</body>
</html>
