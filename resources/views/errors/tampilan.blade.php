{{--
    Kerangka semua halaman error (404, 403, 419, 429, 500, 503).
    Sengaja tidak memakai layouts.app: halaman ini harus tetap tampil walau database/sesi bermasalah.
--}}
@php
    // Tombol ke dashboard hanya untuk admin/owner yang sedang di halaman admin (pelanggan yang nyasar ke /admin → beranda)
    $staff = in_array(rescue(fn () => auth()->user()?->role, null, false), ['admin', 'owner'], true);
    $diAdmin = request()->is('admin*') && $staff;
    $wa = 'https://wa.me/6283184710305?text=' . urlencode('Halo Admin @bucket.imy, saya menemui kendala saat membuka website (kode ' . trim($__env->yieldContent('kode')) . ').');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>@yield('judul') · @bucket.imy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-kelopak flex min-h-screen flex-col">
    <header class="px-5 py-5 sm:px-10">
        <a href="{{ $diAdmin ? url('/admin') : url('/') }}" class="font-serif text-[22px] font-semibold text-maroon">@bucket.imy</a>
    </header>

    <main class="flex flex-1 items-center justify-center px-5 pb-16">
        <div class="w-full max-w-[520px] text-center">
            {{-- Angka kode dengan kelopak bunga sebagai pengganti angka 0 --}}
            <p class="relative mb-2 flex items-center justify-center gap-1 font-serif text-[96px] leading-none font-medium text-maroon sm:text-[128px]" aria-hidden="true">
                @foreach (str_split(trim($__env->yieldContent('kode'))) as $angka)
                    @if ($angka === '0')
                        <svg viewBox="0 0 100 100" class="mx-1 inline-block size-[0.78em]">
                            @foreach ([0, 72, 144, 216, 288] as $sudut)
                                <ellipse cx="50" cy="27" rx="17" ry="25" fill="#F2BFCB" transform="rotate({{ $sudut }} 50 50)"/>
                            @endforeach
                            <circle cx="50" cy="50" r="13" fill="#9B4555"/>
                        </svg>
                    @else
                        <span>{{ $angka }}</span>
                    @endif
                @endforeach
            </p>

            <h1 class="mb-3 font-serif text-[28px] leading-tight font-medium sm:text-[34px]">@yield('judul')</h1>
            <p class="mx-auto mb-8 max-w-[420px] text-[15px] leading-relaxed text-ink-soft">@yield('pesan')</p>

            <div class="flex flex-col justify-center gap-3 sm:flex-row">
                @hasSection('tombol')
                    @yield('tombol')
                @else
                    <a href="{{ $diAdmin ? url('/admin') : url('/') }}" class="btn btn-primary min-h-11 rounded-full px-6">
                        <i class="bi bi-house"></i> {{ $diAdmin ? 'Kembali ke Dashboard' : 'Kembali ke Beranda' }}
                    </a>
                    @unless ($diAdmin)
                        <a href="{{ url('/katalog') }}" class="btn btn-outline min-h-11 rounded-full bg-white px-6">Lihat Katalog</a>
                    @endunless
                @endif
            </div>

            @unless ($diAdmin)
                <p class="mt-8 text-[13px] text-muted">Masih bermasalah? <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-maroon hover:underline">Hubungi kami via WhatsApp</a></p>
            @endunless
        </div>
    </main>

    <footer class="pb-6 text-center text-xs text-muted">© {{ date('Y') }} @bucket.imy</footer>
</body>
</html>
