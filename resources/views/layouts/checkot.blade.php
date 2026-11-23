@php
    $user = auth()->user();
    $kata = preg_split('/\s+/', trim($user->name));
    $inisial = strtoupper(substr($kata[0], 0, 1) . substr($kata[1] ?? '', 0, 1));
    $langkah = (int) trim($__env->yieldContent('langkah', '2'));
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Checkout') - @bucket.imy</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- ========== HEADER: logo · langkah 1-2-3 · akun ========== --}}
<header class="sticky top-0 z-50 flex items-center justify-between gap-6 border-b border-line-soft bg-white px-4 py-3.5 sm:px-7 xl:px-11">
    <a href="{{ url('/') }}" class="font-serif text-[22px] font-semibold text-maroon">@bucket.imy</a>

    <ol class="hidden items-center gap-2.5 text-sm text-muted md:flex" aria-label="Langkah pemesanan">
        @foreach (['Keranjang', 'Checkout', 'Pembayaran'] as $i => $label)
            @php
                $no = $i + 1;
                $selesai = $no < $langkah;
                $sekarang = $no === $langkah;
            @endphp
            @if ($i > 0)
                <li aria-hidden="true" @class(['h-0.5 w-7', 'bg-maroon' => $no <= $langkah, 'bg-line' => $no > $langkah])></li>
            @endif
            <li @if ($sekarang) aria-current="step" @endif @class(['flex items-center gap-2', 'font-semibold text-ink' => $sekarang])>
                <span @class([
                    'inline-flex size-6 items-center justify-center rounded-full border text-xs font-semibold',
                    'border-maroon bg-maroon text-white' => $selesai || $sekarang,
                    'border-line bg-white' => ! $selesai && ! $sekarang,
                ])>@if ($selesai) <i class="bi bi-check-lg"></i> @else {{ $no }} @endif</span>
                {{ $label }}
            </li>
        @endforeach
    </ol>

    <a href="{{ url('/') }}" class="inline-flex h-[46px] items-center gap-2.5 rounded-full border border-line bg-white pr-3.5 pl-1.5 hover:border-maroon hover:bg-blush-50">
        <span class="inline-flex size-9 items-center justify-center rounded-full bg-maroon text-[13px] font-bold text-white">{{ $inisial }}</span>
        <span class="text-[15px] font-semibold">{{ $kata[0] }}</span>
    </a>
</header>

<main>
    <x-toast class="top-20" />
    <x-konfirmasi />
    @yield('content')
</main>

@stack('scripts')
</body>
</html>