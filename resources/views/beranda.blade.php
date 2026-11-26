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
        ['bi-gem', 'Dibuat Rapi & Detail', 'Setiap buket kita buat satu per satu dengan penuh ketelitian. Bentuknya rapi, cantik, dan pastinya awet disimpan selamanya tanpa takut layu.'],
        ['bi-palette', 'Bebas Request & Custom', 'Kamu bebas pilih warna wrapping, tema buket, sampai diskusiin budget yang kamu punya. Nanti kita bantu racikkan yang paling pas!'],
        ['bi-envelope-heart', 'Free Kartu Ucapan', 'Sudah termasuk kartu ucapan gratis. Tinggal tulis pesannya, nanti kita bantu tuliskan dengan rapi biar makin berkesan buat si customer.'],
        ['bi-truck', 'Aman Sampai Tujuan', 'Bisa dikirim lewat kurir atau kamu ambil langsung ke toko. Kita kemas seaman mungkin supaya buket tetap rapi dan nggak rusak pas sampai.'],
    ];

    $langkah = [
        ['Pilih / Request Konsep', 'Silakan informasikan momen spesial Anda (seperti wisuda, ulang tahun, atau anniversary) beserta foto referensi buket yang Anda inginkan.'],
        ['Konfirmasi & Bayar', 'Bucket.imy akan merincikan estimasi biaya secara transparan. Setelah Anda setuju, proses pembayaran dapat dilakukan dengan mudah dan praktis.'],
        ['Rangkai & Kirim Foto', 'Bunga akan dirangkai dengan penuh ketelitian oleh kami. Kami juga akan mengirimkan foto hasil jadinya kepada Anda untuk diperiksa terlebih dahulu sebelum dikirim.'],
        ['Diantar ke Penerima', 'Buket akan diantar tepat waktu langsung kepada penerima, lengkap beserta kartu ucapan yang indah dari Anda.'],
    ];
@endphp

@section('content')

{{-- ===== HERO ===== --}}
<section class="bg-sorot px-4 pt-12 pb-8 text-center sm:px-6">
    <span class="inline-flex items-center gap-1.5 rounded-full bg-blush px-3 py-1 text-[11px] font-semibold tracking-widest text-maroon">
    BUCKET.IMY
    </span>
    <h1 class="mt-4.5 mb-3.5 font-serif text-[30px] leading-tight font-medium min-[381px]:text-4xl sm:text-[52px]">
        Merangkai Makna,<br><em class="text-maroon">Bukan Sekadar Bunga.</em>
    </h1>
    <p class="mx-auto max-w-[600px] text-[15px] leading-relaxed text-ink-soft">
        <strong class="text-ink">@bucket.imy</strong> adalah studio bunga independen yang menghadirkan rangkaian
        buket segar pilihan dan gubahan <em>custom</em> dengan sentuhan personal untuk
        merayakan setiap momen berharga Anda.
    </p>

    {{-- ===== SLIDER FOTO (geser otomatis tiap 4 detik) ===== --}}
    @php
        $slide = [
            ['hero-bucket.jpg', 'Aneka buket bunga: mawar, matahari, lily, dan baby breath'],
            ['hero-2.jpg', 'Deretan buket mawar dan krisan dengan wrapping warna-warni'],
            ['hero-3.jpg', 'Rak buket bunga dengan berbagai warna'],
            ['hero-4.jpg', 'Buket mawar besar dengan wrapping pink dan ungu'],
        ];
    @endphp
    <div data-slider class="group/slider relative mx-auto mt-16 mb-10 w-full max-w-[680px] overflow-hidden rounded-[10px] shadow-[0_20px_40px_-20px_rgb(94_38_51/0.45)]"
         role="region" aria-roledescription="carousel" aria-label="Foto buket">
        <div data-slider-track class="flex snap-x snap-mandatory overflow-x-auto scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach ($slide as $i => [$file, $alt])
                <img src="{{ asset('images/' . $file) }}" alt="{{ $alt }}" width="1100" height="500"
                     @if ($i > 0) loading="lazy" @endif
                     class="block aspect-[11/5] w-full shrink-0 snap-center object-cover">
            @endforeach
        </div>

        {{-- Tombol kiri/kanan (muncul saat disorot, laptop) --}}
        <button type="button" data-slider-prev aria-label="Foto sebelumnya"
                class="absolute top-1/2 left-3 hidden size-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/85 text-maroon opacity-0 shadow transition-opacity group-hover/slider:opacity-100 focus-visible:opacity-100 sm:flex">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button type="button" data-slider-next aria-label="Foto berikutnya"
                class="absolute top-1/2 right-3 hidden size-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/85 text-maroon opacity-0 shadow transition-opacity group-hover/slider:opacity-100 focus-visible:opacity-100 sm:flex">
            <i class="bi bi-chevron-right"></i>
        </button>

        {{-- Titik penanda --}}
        <div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-1.5 rounded-full bg-black/25 px-2 py-1.5 backdrop-blur-[2px]">
            @foreach ($slide as $i => $_)
                <button type="button" data-slider-dot="{{ $i }}" aria-label="Foto {{ $i + 1 }}"
                        class="h-2 w-2 rounded-full bg-white/60 shadow transition-all aria-[current=true]:w-5 aria-[current=true]:bg-white"></button>
            @endforeach
        </div>
    </div>

    <script>
        // Slider beranda: geser otomatis ke kiri tanpa henti (foto terakhir lanjut ke foto pertama),
        // berhenti saat disorot/disentuh, bisa di-swipe.
        document.querySelectorAll('[data-slider]').forEach((slider) => {
            const track = slider.querySelector('[data-slider-track]');
            const dots = slider.querySelectorAll('[data-slider-dot]');
            const jumlah = track.children.length;
            const diam = matchMedia('(prefers-reduced-motion: reduce)').matches;
            let aktif = 0, timer = null;

            // Salinan foto pertama ditaruh di ujung. Setelah sampai di salinan, posisi diam-diam
            // dipindah ke foto pertama asli, jadi kelihatannya terus bergeser ke kiri.
            const salinan = track.children[0].cloneNode();
            salinan.alt = ''; salinan.setAttribute('aria-hidden', 'true');
            track.append(salinan);

            const lebar = () => track.clientWidth;
            const loncat = (posisi) => {          // pindah instan, tanpa animasi
                track.style.scrollBehavior = 'auto';
                track.scrollLeft = posisi;
                track.style.scrollBehavior = '';
            };
            const geser = (posisi) => track.scrollTo({ left: posisi, behavior: diam ? 'auto' : 'smooth' });

            const ke = (i) => {
                if (i < 0) { loncat(jumlah * lebar()); i = jumlah - 1; } // dari foto 1 mundur ke foto terakhir
                geser(i * lebar());
            };
            const rapikan = () => { if (Math.round(track.scrollLeft / lebar()) >= jumlah) loncat(0); };
            const tandai = () => {
                aktif = Math.round(track.scrollLeft / lebar()) % jumlah;
                dots.forEach((d, i) => d.setAttribute('aria-current', i === aktif));
            };
            const mulai = () => { if (!diam) { clearInterval(timer); timer = setInterval(() => ke(aktif + 1), 4000); } };
            const henti = () => clearInterval(timer);

            let tunda;
            track.addEventListener('scroll', () => {
                requestAnimationFrame(tandai);
                clearTimeout(tunda); tunda = setTimeout(rapikan, 120); // setelah geseran selesai
            }, { passive: true });
            dots.forEach((d) => d.addEventListener('click', () => { ke(+d.dataset.sliderDot); mulai(); }));
            slider.querySelector('[data-slider-prev]').addEventListener('click', () => { ke(aktif - 1); mulai(); });
            slider.querySelector('[data-slider-next]').addEventListener('click', () => { ke(aktif + 1); mulai(); });
            slider.addEventListener('mouseenter', henti);
            slider.addEventListener('mouseleave', mulai);
            track.addEventListener('touchstart', henti, { passive: true });
            track.addEventListener('touchend', mulai, { passive: true });
            document.addEventListener('visibilitychange', () => document.hidden ? henti() : mulai());

            tandai();
            mulai();
        });
    </script>

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
                    {{-- Ikon di kiri, judul di kanan --}}
                    <div class="mb-3 flex items-center gap-3">
                        <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full text-[17px] text-maroon" style="background: {{ $bg }}"><i class="bi {{ $icon }}"></i></span>
                        <h3 class="text-base leading-snug font-semibold">{{ $judul }}</h3>
                    </div>
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
            @foreach ($alasan as [$icon, $judul, $teks])
                <article class="rounded-[10px] border border-line-soft bg-white p-6 shadow-card lg:px-5">
                    {{-- Ikon kiri, judul kanan --}}
                    <div class="mb-3 flex items-center gap-2.5">
                        <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-blush text-base text-maroon lg:size-8 lg:text-[15px]"><i class="bi {{ $icon }}"></i></span>
                        <h3 class="text-base leading-snug font-semibold lg:text-[15px]">{{ $judul }}</h3>
                    </div>
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
            @foreach ($langkah as $i => [$judul, $teks])
                <article class="rounded-[10px] border border-line-soft bg-white p-6 shadow-card">
                    <div class="mb-3 flex items-center gap-2.5">
                        <span class="inline-flex size-7 shrink-0 items-center justify-center rounded-full bg-maroon text-xs font-bold text-white">{{ $i + 1 }}</span>
                        <h3 class="text-base leading-snug font-semibold lg:text-[15px] min-[90rem]:text-base">{{ $judul }}</h3>
                    </div>
                    <p class="text-[12.5px] leading-relaxed text-ink-soft">{{ $teks }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== TESTIMONI TERBARU ===== --}}
@php
    $ulasan = \App\Models\Testimoni::with(['user', 'pesanan.detailPesanan'])
        ->whereNotNull('isi_testimoni')->where('isi_testimoni', '!=', '')
        ->latest()->take(6)->get();

    $namaSingkat = function (?string $nama) {
        $kata = preg_split('/\s+/', trim((string) $nama)) ?: ['Pelanggan'];
        return $kata[0] . (isset($kata[1]) ? ' ' . mb_strtoupper(mb_substr($kata[1], 0, 1)) . '.' : '');
    };
@endphp

@if ($ulasan->isNotEmpty())
<section id="testimoni" class="overflow-hidden py-18">
    <div class="wrap mb-10 text-center">
        <h2 class="font-serif text-[28px] font-medium sm:text-[36px]">Testimoni</h2>
        <p class="mt-2 text-sm text-muted">Cerita dari pelanggan yang sudah memesan di @bucket.imy.</p>
    </div>

    {{-- Kartu bergeser ke samping. Kartu yang di tengah disorot. --}}
    <div data-testimoni class="relative">
        <div data-testimoni-track
             class="flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth px-[max(16px,calc(50%-180px))] pt-1 pb-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach ($ulasan as $u)
                @php $produk = $u->pesanan?->detailPesanan->first()?->nama_produk; @endphp
                <figure class="flex w-[min(360px,calc(100vw-48px))] shrink-0 snap-center flex-col rounded-2xl border border-line-soft bg-white p-6 transition-colors duration-300 data-[aktif]:border-rose/50 data-[aktif]:bg-blush data-[aktif]:shadow-card">
                    {{-- Atas: pemberi ulasan. Bawah: isi ulasan --}}
                    <figcaption class="flex items-center gap-3">
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-maroon text-sm font-bold text-white">
                            {{ mb_strtoupper(mb_substr($u->user?->name ?? 'P', 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <strong class="block text-sm">{{ $namaSingkat($u->user?->name) }}</strong>
                            <small class="block truncate text-xs text-maroon">{{ $produk ?: $u->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="shrink-0 text-[15px] tracking-[1px] text-gold" aria-label="{{ $u->rating }} dari 5 bintang">
                            {{ str_repeat('★', $u->rating) }}<span class="text-line">{{ str_repeat('★', 5 - $u->rating) }}</span>
                        </span>
                    </figcaption>

                    <blockquote class="mt-5 flex-1 text-[15px] leading-relaxed text-ink-soft">
                        {{ $u->isi_testimoni }}
                    </blockquote>
                </figure>
            @endforeach
        </div>

        {{-- Tombol geser --}}
        <div class="mt-4 flex justify-center gap-4">
            {{-- Tombol yang terakhir diklik jadi terisi maroon (data-aktif) --}}
            <button type="button" data-testimoni-prev aria-label="Testimoni sebelumnya"
                    class="flex size-12 items-center justify-center rounded-full border border-maroon bg-white text-lg text-maroon transition hover:bg-blush active:scale-95 data-[aktif]:bg-maroon data-[aktif]:text-white data-[aktif]:hover:bg-maroon-dark">
                <i class="bi bi-arrow-left"></i>
            </button>
            <button type="button" data-testimoni-next data-aktif aria-label="Testimoni berikutnya"
                    class="flex size-12 items-center justify-center rounded-full border border-maroon bg-white text-lg text-maroon transition hover:bg-blush active:scale-95 data-[aktif]:bg-maroon data-[aktif]:text-white data-[aktif]:hover:bg-maroon-dark">
                <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</section>

<script>
    // Testimoni: tandai kartu yang paling dekat ke tengah, tombol geser satu kartu (berputar di ujung).
    document.querySelectorAll('[data-testimoni]').forEach((wadah) => {
        const track = wadah.querySelector('[data-testimoni-track]');
        const kartu = [...track.children];
        let aktif = 0, tujuan = null, tunda;

        const tandai = () => {
            const tengah = track.getBoundingClientRect().left + track.clientWidth / 2;
            let jarakMin = Infinity, dekat = 0;
            kartu.forEach((k, i) => {
                const r = k.getBoundingClientRect();
                const jarak = Math.abs(r.left + r.width / 2 - tengah);
                if (jarak < jarakMin) { jarakMin = jarak; dekat = i; }
            });
            if (tujuan === null) aktif = dekat; // saat digeser tombol, pakai kartu tujuan
            kartu.forEach((k, i) => k.toggleAttribute('data-aktif', i === aktif));
        };
        const ke = (i) => {
            aktif = tujuan = (i + kartu.length) % kartu.length;
            const k = kartu[aktif];
            track.scrollTo({ left: k.offsetLeft - (track.clientWidth - k.offsetWidth) / 2 });
            tandai();
        };

        track.addEventListener('scroll', () => {
            requestAnimationFrame(tandai);
            clearTimeout(tunda); tunda = setTimeout(() => { tujuan = null; tandai(); }, 150); // geseran selesai
        }, { passive: true });
        const prev = wadah.querySelector('[data-testimoni-prev]');
        const next = wadah.querySelector('[data-testimoni-next]');
        const tekan = (tombol) => [prev, next].forEach((t) => t.toggleAttribute('data-aktif', t === tombol));
        prev.addEventListener('click', () => { tekan(prev); ke(aktif - 1); });
        next.addEventListener('click', () => { tekan(next); ke(aktif + 1); });
        addEventListener('resize', tandai);

        // Mulai dari kartu kedua kalau ada, supaya kiri-kanan sama-sama terisi seperti contoh.
        if (kartu.length > 2) { track.style.scrollBehavior = 'auto'; ke(1); track.style.scrollBehavior = ''; }
        tandai();
    });
</script>
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
