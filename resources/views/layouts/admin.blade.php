<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · @bucket.imy</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FBF4F5] print:bg-white">
{{-- Ingat pilihan "sidebar ditutup" di laptop/desktop (dibaca sebelum sidebar digambar, supaya tidak berkedip) --}}
<script>
    try {
        if (localStorage.getItem('sidebar') === 'tutup' && innerWidth >= 1024) document.body.classList.add('sidebar-tutup');
    } catch (e) {}
</script>
@php
    $user = auth()->user();
    $judulRole = 'Dashboard ' . ucfirst($user->role); // "Dashboard Admin" / "Dashboard Owner"

    // Data Pesanan: yang perlu dirangkai + request custom yang belum dibalas
    $badgePesanan = \App\Models\Pesanan::where('status_pesanan', 'dirangkai')->count()
        + \App\Models\Custom::where('status', 'baru')->count();
    $badgeProduk  = \App\Models\Produk::where('aktif', true)->where('stok', '<', 5)->count();

    // [nama route, label, badge]
    $menu = [
        ['admin.dashboard',          'Ringkasan',          0],
        ['admin.pesanan.index',      'Data Pesanan',       $badgePesanan],
        ['admin.produk.index',       'Katalog Produk',     $badgeProduk],
        ['admin.pelanggan.index',    'Data Pelanggan',     0],
        ['admin.laporan.index',      'Laporan',            0],
        ['admin.pengaturan.index',   'Pengaturan',         0],
    ];

    $inisial = collect(explode(' ', $user->name))->take(2)->map(fn ($k) => mb_substr($k, 0, 1))->implode('');
@endphp

<div class="flex min-h-screen">
    {{-- ========== SIDEBAR ==========
         Laptop/desktop (≥1024px): menempel di kiri, bisa digeser keluar (body.sidebar-tutup).
         lg:left-auto penting: sticky + left-0 membuat sidebar tetap 'nempel' di layar walau sudah digeser keluar.
         Tablet/HP: jadi laci dari kiri, dibuka tombol hamburger (body.sidebar-buka). --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-60 flex print:hidden h-screen w-60 shrink-0 -translate-x-full flex-col bg-sidebar px-4 pt-7 pb-4 text-white shadow-[0_0_40px_rgb(42_26_30/0.3)] transition-[translate,margin] duration-250
                  in-[.sidebar-buka]:translate-x-0
                  lg:sticky lg:top-0 lg:left-auto lg:translate-x-0 lg:shadow-none lg:in-[.sidebar-tutup]:-ml-60">
        <div class="flex flex-col gap-0.5 px-2.5 pb-6">
            <strong class="font-serif text-2xl font-medium">@bucket.imy</strong>
            <span class="text-[11px] tracking-[2px] text-white/70 uppercase">{{ $judulRole }}</span>
        </div>

        <nav class="flex flex-1 flex-col gap-1 overflow-y-auto">
            @foreach ($menu as [$nama, $label, $badge])
                @php $aktif = Route::has($nama) && request()->routeIs(str_replace('.index', '', $nama) . '*'); @endphp
                <a href="{{ Route::has($nama) ? route($nama) : '#' }}" @if ($aktif) aria-current="page" @endif
                   class="group flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm text-white/90 hover:bg-white/10 aria-[current=page]:bg-white aria-[current=page]:font-semibold aria-[current=page]:text-wine">
                    <span class="size-1.5 shrink-0 rounded-full bg-white/35 group-aria-[current=page]:bg-[#F2A7B6]"></span>
                    <span class="flex-1">{{ $label }}</span>
                    @if ($badge > 0)
                        <span class="flex h-5.5 min-w-5.5 items-center justify-center rounded-full bg-blush px-1.5 text-xs font-bold text-wine">{{ $badge }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3 rounded-[14px] bg-white/10 px-3.5 py-3">
            <span class="flex size-9.5 shrink-0 items-center justify-center rounded-full bg-blush text-[13px] font-bold text-wine">{{ strtoupper($inisial) }}</span>
            <div class="flex min-w-0 flex-1 flex-col">
                <strong class="truncate text-sm">{{ $user->name }}</strong>
                <small class="text-xs text-white/70">{{ ucfirst($user->role) }}</small>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Keluar" aria-label="Keluar" class="p-1 text-lg text-white hover:text-blush"><i class="bi bi-box-arrow-right"></i></button>
            </form>
        </div>
    </aside>
    {{-- Latar gelap saat laci terbuka di tablet/HP --}}
    <div data-tutup-sidebar class="print:hidden fixed inset-0 z-55 hidden bg-ink/45 in-[.sidebar-buka]:block lg:in-[.sidebar-buka]:hidden"></div>

    {{-- ========== KONTEN ========== --}}
    <main class="min-w-0 flex-1 px-3 pb-7 min-[381px]:px-4 sm:pb-8 lg:px-6 lg:pb-9 xl:px-8 xl:pb-10 min-[1440px]:px-12 min-[1440px]:pb-12">
        <header class="mb-5 flex flex-col justify-between gap-3 border-b border-line pt-4 pb-4 md:flex-row md:items-start print:hidden">
            {{-- Ikon hamburger tanpa kotak, sejajar judul halaman --}}
            <div class="flex items-start gap-3">
                <button type="button" id="menuToggle" aria-label="Buka/tutup menu" aria-controls="sidebar" aria-expanded="true"
                        class="-ml-1.5 flex size-9 shrink-0 print:hidden items-center justify-center rounded-lg text-2xl text-wine transition-colors hover:text-maroon focus-visible:ring-2 focus-visible:ring-maroon/30 focus-visible:outline-none">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    {{-- Judul halaman langsung di samping ikon (baris "Dashboard Admin" dihapus) --}}
                    <h1 class="text-xl leading-9 font-bold tracking-tight sm:text-[22px]">@yield('judul', 'Dashboard')</h1>
                    @hasSection('subjudul') <p class="mt-1.5 text-[13px] text-muted">@yield('subjudul')</p> @endif
                </div>
            </div>
            <span class="inline-flex items-center gap-2 self-start rounded-xl border border-[#F0C9D0] bg-[#FFF7F8] px-4 py-2 text-[13px] text-wine sm:whitespace-nowrap print:hidden">
                <i class="bi bi-calendar3"></i> Hari ini: {{ now()->translatedFormat('l, j F Y') }}
            </span>
        </header>

        {{-- Baris di bawah garis: tab halaman di kiri (mis. Pesanan Katalog | Request Custom),
             tombol aksi (Ekspor, Cetak, Tambah, dll) di kanan, sejajar --}}
        @if (View::hasSection('tab-atas') || View::hasSection('aksi'))
            <div class="mb-5 flex flex-wrap items-center gap-3 print:hidden">
                @hasSection('tab-atas') <div class="w-full sm:w-auto sm:min-w-[420px] sm:flex-1">@yield('tab-atas')</div> @endif
                @hasSection('aksi') <div class="flex flex-wrap items-center gap-3 md:ml-auto">@yield('aksi')</div> @endif
            </div>
        @endif

        <x-toast />
        <x-konfirmasi />

        @yield('content')
    </main>
</div>

<script>
    // Tombol hamburger: geser sidebar (laptop/desktop) atau buka laci (tablet/HP)
    (() => {
        const tombol = document.getElementById('menuToggle');
        const kecil = window.matchMedia('(max-width: 1023.98px)');
        const body = document.body;

        const sinkron = () => {
            const terbuka = kecil.matches ? body.classList.contains('sidebar-buka') : !body.classList.contains('sidebar-tutup');
            tombol.setAttribute('aria-expanded', String(terbuka));
        };

        tombol.addEventListener('click', () => {
            if (kecil.matches) {
                body.classList.toggle('sidebar-buka');
            } else {
                const tutup = body.classList.toggle('sidebar-tutup');
                try { localStorage.setItem('sidebar', tutup ? 'tutup' : 'buka'); } catch (e) {}
            }
            sinkron();
        });

        document.querySelector('[data-tutup-sidebar]').addEventListener('click', () => { body.classList.remove('sidebar-buka'); sinkron(); });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && body.classList.contains('sidebar-buka')) { body.classList.remove('sidebar-buka'); sinkron(); tombol.focus(); }
        });

        kecil.addEventListener('change', () => { body.classList.remove('sidebar-buka'); sinkron(); });
        sinkron();
    })();
</script>
</body>
</html>
