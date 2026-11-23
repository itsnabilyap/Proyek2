@extends('layouts.app')

@section('title', 'Beranda')

@php
    $wa = 'https://wa.me/6283184710305?text=' . urlencode('Halo @bucket.imy, saya mau tanya soal bucket bunga.');

    $keunggulan = [
        ['bi-flower2', '100% Handmade', 'Dibuat secara detail dan penuh ketelitian.'],
        ['bi-palette', 'Handcrafted Teliti', 'Dibuat dengan teliti.'],
        ['bi-truck', 'Antar Tepat Waktu', 'Kurir berusaha agar aman terjaga.'],
    ];

    $profil = [
        ['bi-geo-alt', '#FBEAEE', 'Lokasi & Area Layanan',
            'Toko kami berlokasi di Jl. Tridaya Barat, Karanganyar, Indramayu. Melayani pengiriman buket ke seluruh wilayah Indramayu dan sekitarnya dengan pengemasan yang aman.'],
        ['bi-clock', '#DDEFD9', 'Jam Operasional',
            'Kami siap merespons kebutuhan hadiah Anda setiap hari:<br><strong>Senin – Sabtu:</strong> 08.00 – 19.00 WIB.<br>Pesanan mendesak (urgent) dapat dikoordinasikan langsung via chat.'],
        ['bi-camera', '#FBEAEE', 'Jaminan Mutu & Transparansi',
            'Hasil pengerjaan dipastikan rapi, awet, dan sesuai pesanan. Foto buket akan dikirimkan ke kamu dulu sebelum dikirim kurir atau kamu ambil.'],
    ];

    $alasan = [
        ['bi-flower1', 'Dibuat Rapi & Detail', 'Setiap buket kita buat satu per satu dengan penuh ketelitian. Bentuknya rapi, cantik, dan pastinya awet disimpan selamanya tanpa takut layu.'],
        ['bi-sliders', 'Bebas Request & Custom', 'Kamu bebas pilih warna wrapping, tema buket, sampai diskusiin budget yang kamu punya. Nanti kita bantu racikkan yang paling pas!'],
        ['bi-pen', 'Free Kartu Ucapan', 'Sudah termasuk kartu ucapan gratis. Tinggal tulis pesannya, nanti kita bantu tuliskan dengan rapi biar makin berkesan buat si customer.'],
        ['bi-snow', 'Aman Sampai Tujuan', 'Bisa dikirim lewat kurir atau kamu ambil langsung ke toko. Kita kemas seaman mungkin supaya buket tetap rapi dan nggak rusak pas sampai.'],
    ];

    $langkah = [
        ['bi-chat-square-text', 'Pilih / Request Konsep', 'Silakan informasikan momen spesial Anda (seperti wisuda, ulang tahun, atau anniversary) beserta foto referensi buket yang Anda inginkan.'],
        ['bi-receipt', 'Konfirmasi & Pembayaran', 'Bucket.imy akan merincikan estimasi biaya secara transparan. Setelah Anda setuju, proses pembayaran dapat dilakukan dengan mudah dan praktis.'],
        ['bi-camera', 'Rangkai & Kirim Foto', 'Bunga akan dirangkai dengan penuh ketelitian oleh kami. Kami juga akan mengirimkan foto hasil jadinya kepada Anda untuk diperiksa terlebih dahulu sebelum dikirim.'],
        ['bi-envelope-heart', 'Diantar ke Penerima', 'Buket akan diantar tepat waktu langsung kepada penerima, lengkap beserta kartu ucapan yang indah dari Anda.'],
    ];
@endphp

@section('content')

{{-- ===== HERO ===== --}}
<section class="bg-sorot px-4 pt-12 pb-8 text-center sm:px-6">
    <span class="inline-flex items-center gap-1.5 rounded-full bg-blush px-3 py-1 text-[11px] font-semibold tracking-widest text-maroon">
        <span class="size-1.5 rounded-full bg-maroon"></span> BUCKET.IMY
    </span>
    <h1 class="mt-4.5 mb-3.5 font-serif text-[30px] leading-tight font-medium min-[381px]:text-4xl sm:text-[52px]">
        Merangkai Makna,<br><em class="text-maroon">Bukan Sekadar Bunga.</em>
    </h1>
    <p class="mx-auto max-w-[600px] text-[15px] leading-relaxed text-ink-soft">
        <strong class="text-ink">@bucket.imy</strong> adalah studio bunga independen yang menghadirkan rangkaian
        buket segar pilihan dan gubahan <em>custom</em> dengan sentuhan personal untuk
        merayakan setiap momen berharga Anda.
    </p>

    <img src="{{ asset('images/hero-bucket.jpg') }}" alt="Empat buket bunga @bucket.imy dengan wrapping goni dan pita pink"
         width="1100" height="500"
         class="mx-auto mt-16 mb-10 block aspect-[11/5] h-auto w-full max-w-[680px] rounded-[10px] object-cover shadow-[0_20px_40px_-20px_rgb(94_38_51/0.45)]">

    <div class="mx-auto flex max-w-[820px] flex-col justify-between gap-5 border-t border-line-soft pt-4 text-left sm:flex-row sm:flex-wrap">
        @foreach ($keunggulan as [$icon, $judul, $teks])
            <div class="flex items-center gap-3">
                <span class="inline-flex size-8.5 shrink-0 items-center justify-center rounded-full bg-blush text-[15px] text-maroon"><i class="bi {{ $icon }}"></i></span>
                <div>
                    <strong class="block text-[15px]">{{ $judul }}</strong>
                    <small class="text-xs text-muted">{{ $teks }}</small>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- ===== PROFIL STORE ===== --}}
<section id="tentang-kami" class="bg-blush-100 py-18">
    <div class="wrap">
        <p class="mb-2 text-center text-[11px] font-semibold tracking-[.12em] text-maroon">PROFIL STORE</p>
        <h2 class="mb-3 text-center font-serif text-[26px] font-medium sm:text-[32px]">Mengenal Lebih Dekat <em class="text-maroon">@bucket.imy</em></h2>
        <p class="mx-auto mb-8 max-w-[600px] text-center text-sm leading-relaxed text-ink-soft">
            Berawal dari kecintaan tulus pada seni merangkai flora, kami mendedikasikan setiap tangkai
            dan kelopak untuk mewakili perasaan Anda. Bunga kami dipilih dan dirawat segar setiap hari,
            dirangkai langsung oleh florist bersertifikasi tanpa perantara pihak ketiga.
        </p>

        <div class="mt-2 grid gap-7 md:grid-cols-3">
            @foreach ($profil as [$icon, $bg, $judul, $teks])
                <article class="rounded-[10px] border border-line-soft bg-white p-6 shadow-card">
                    <span class="inline-flex size-10 items-center justify-center rounded-full text-[17px] text-maroon" style="background: {{ $bg }}"><i class="bi {{ $icon }}"></i></span>
                    <h3 class="mt-4 mb-2 text-base font-semibold">{{ $judul }}</h3>
                    <p class="text-[12.5px] leading-relaxed text-ink-soft">{!! $teks !!}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== MENGAPA MEMILIH ===== --}}
<section class="py-18">
    <div class="wrap">
        <h2 class="mb-3 font-serif text-[26px] font-medium sm:text-[32px]">Mengapa Memilih <em class="text-maroon">@bucket.imy?</em></h2>

        <div class="mt-2 grid gap-5.5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($alasan as $i => [$icon, $judul, $teks])
                <article class="rounded-[10px] border border-line-soft bg-white p-6 shadow-card">
                    <span class="mb-2.5 block font-serif text-lg text-[#C26B7D]">0{{ $i + 1 }}</span>
                    <span class="inline-flex size-8.5 items-center justify-center rounded-full bg-blush text-[15px] text-maroon"><i class="bi {{ $icon }}"></i></span>
                    <h3 class="mt-4 mb-2 text-base font-semibold">{{ $judul }}</h3>
                    <p class="text-[12.5px] leading-relaxed text-ink-soft">{{ $teks }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== CARA PEMESANAN ===== --}}
<section id="cara-pesan" class="py-18">
    <div class="wrap">
        <p class="mb-2 text-center text-[11px] font-semibold tracking-[.12em] text-maroon">PROSES PRAKTIS</p>
        <h2 class="mb-3 text-center font-serif text-[26px] font-medium sm:text-[32px]">Cara Pemesanan yang Mudah</h2>
        <p class="mx-auto mb-8 max-w-[600px] text-center text-sm leading-relaxed text-ink-soft">Pesan buket impian tanpa ribet hanya dalam 4 tahapan santai.</p>

        <div class="mt-2 grid gap-5.5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($langkah as $i => [$icon, $judul, $teks])
                <article class="rounded-[10px] border border-line-soft bg-white p-6 shadow-card">
                    <div class="flex items-center justify-between text-muted">
                        <span class="inline-flex size-7 items-center justify-center rounded-full bg-maroon text-xs font-bold text-white">{{ $i + 1 }}</span>
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <h3 class="mt-3.5 mb-2 text-base font-semibold">{{ $judul }}</h3>
                    <p class="text-[12.5px] leading-relaxed text-ink-soft">{{ $teks }}</p>
                </article>
            @endforeach
        </div>

        <p class="mx-auto mt-7 w-fit rounded-full border border-line-soft bg-white px-4 py-1.5 text-center text-xs text-muted">
            <i class="bi bi-info-circle"></i>
            Butuh buket hari ini (same-day)? Konsultasikan ketersediaan bunga pagi ini via WhatsApp.
        </p>
    </div>
</section>

{{-- ===== TESTIMONI TERBARU ===== --}}
{{-- ===== Testimoni Terbaru ===== --}}
@php
    $ulasan = \App\Models\Testimoni::with(['user', 'pesanan.detailPesanan'])
        ->whereNotNull('isi_testimoni')->where('isi_testimoni', '!=', '')
        ->latest()->take(6)->get();

    $semua = \App\Models\Testimoni::query();
    $rata = round((float) $semua->avg('rating'), 1);
    $jumlah = $semua->count();

    $namaSingkat = function (?string $nama) {
        $kata = preg_split('/\s+/', trim((string) $nama)) ?: ['Pelanggan'];
        return $kata[0] . (isset($kata[1]) ? ' ' . mb_strtoupper(mb_substr($kata[1], 0, 1)) . '.' : '');
    };
    $bintang = fn ($n) => str_repeat('★', $n) . str_repeat('☆', 5 - $n);
@endphp

@if ($ulasan->isNotEmpty())
<section id="testimoni" class="py-18">
    <div class="wrap">
        <div class="mb-8 flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="mb-2 text-[11px] font-semibold tracking-[.12em] text-maroon">KATA MEREKA</p>
                <h2 class="font-serif text-[26px] font-medium sm:text-[32px]">Testimoni <em class="text-maroon">Terbaru</em></h2>
            </div>

            {{-- Ringkasan nilai --}}
            <div class="flex items-center gap-3.5 self-start rounded-2xl border border-line-soft bg-white px-5 py-3 shadow-card sm:self-auto">
                <strong class="font-serif text-[34px] leading-none text-maroon">{{ number_format($rata, 1, ',', '') }}</strong>
                <div class="flex flex-col">
                    <span class="tracking-[2px] text-gold" aria-label="Rata-rata {{ $rata }} dari 5">{{ $bintang((int) round($rata)) }}</span>
                    <small class="text-xs text-muted">dari {{ $jumlah }} ulasan pelanggan</small>
                </div>
            </div>
        </div>

        {{-- HP: geser ke samping · tablet ke atas: grid --}}
        <div class="-mx-4 flex snap-x snap-mandatory scroll-px-4 gap-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:grid sm:grid-cols-2 sm:overflow-visible sm:px-0 sm:pb-0 lg:grid-cols-3">
            @foreach ($ulasan as $u)
                @php $produk = $u->pesanan?->detailPesanan->first()?->nama_produk; @endphp
                <figure class="relative flex w-[82%] shrink-0 snap-start flex-col rounded-2xl border border-line-soft bg-white p-6 shadow-card sm:w-auto">
                    {{-- tanda kutip besar sebagai hiasan --}}
                    <span aria-hidden="true" class="absolute top-3 right-5 font-serif text-[64px] leading-none text-blush">&rdquo;</span>

                    <span class="tracking-[2px] text-gold" aria-label="{{ $u->rating }} dari 5 bintang">{{ $bintang($u->rating) }}</span>
                    <blockquote class="mt-3 mb-5 flex-1 text-sm leading-relaxed text-ink-soft">
                        “{{ \Illuminate\Support\Str::limit($u->isi_testimoni, 180) }}”
                    </blockquote>

                    <figcaption class="flex items-center gap-3 border-t border-line-soft pt-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blush text-sm font-bold text-maroon">
                            {{ mb_strtoupper(mb_substr($u->user?->name ?? 'P', 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <strong class="block text-sm">{{ $namaSingkat($u->user?->name) }}</strong>
                            <small class="block truncate text-xs text-muted">
                                {{ $produk ? $produk . ' · ' : '' }}{{ $u->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== AJAKAN CHAT ===== --}}
<section class="pt-6 pb-18">
    <div class="wrap">
        <div class="relative flex flex-wrap items-center justify-between gap-6 overflow-hidden rounded-2xl bg-blush-200 px-5.5 py-7 sm:p-11">
            {{-- hiasan belah ketupat di kanan --}}
            <span aria-hidden="true" class="absolute top-1/2 -right-7.5 hidden size-27.5 -translate-y-1/2 rotate-45 rounded-lg bg-[#F4D3DA] opacity-70 lg:block"></span>

            <div>
                <p class="mb-2 text-[11px] font-semibold tracking-[.12em] text-maroon">DISKUSI BEBAS BIAYA</p>
                <h2 class="mb-2.5 font-serif text-[26px] font-medium">Punya Rencana Hadiah Khusus?</h2>
                <p class="max-w-[470px] text-sm leading-relaxed text-ink-soft">
                    Hubungi kami langsung via WhatsApp untuk diskusi cepat, tanya ketersediaan stok bunga
                    hari ini, atau sekadar bertukar ide konsep buket Anda.
                </p>
            </div>
            <div class="relative z-10 flex items-center gap-3.5 sm:ml-auto">
                <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary min-h-10 px-5.5 text-[13px]">
                    <i class="bi bi-chat-square-text"></i> Chat Sekarang
                </a>
                <small class="text-[11px] text-muted">Respon dalam &lt; 15 menit</small>
            </div>
        </div>
    </div>
</section>

@endsection