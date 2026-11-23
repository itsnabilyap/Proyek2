@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $pickup = $pesanan->metode_pengiriman === 'pickup';
    $metode = $pembayaran->metode === 'qris' ? 'QRIS' : 'BCA VA';
    $kurir = \App\Models\Pesanan::KURIR[$pesanan->layanan_kurir] ?? null;
    $wa = 'https://wa.me/6283184710305?text=' . urlencode("Halo @bucket.imy, saya mau tanya soal pesanan {$pesanan->kode_pesanan}.");
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - {{ $pesanan->kode_pesanan }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css'])
</head>
{{-- Saat dicetak / disimpan PDF: latar abu dan tombol disembunyikan (print:) --}}
<body class="flex min-h-screen items-center justify-center bg-[#9E9799] px-4 py-10 print:bg-white print:p-0">

<main role="dialog" aria-modal="true" aria-labelledby="judulSukses"
      class="w-full max-w-[560px] rounded-[28px] bg-white px-4.5 py-6 text-center shadow-[0_30px_80px_-30px_rgb(42_26_30/0.6)] sm:p-8 print:shadow-none">
    <span class="mx-auto mb-3 flex size-16 items-center justify-center rounded-full bg-ok-soft text-[28px] text-ok ring-8 ring-[#F1F8F3]"><i class="bi bi-check-lg"></i></span>

    <p class="text-[13px] text-ink-soft">
        Lunas via {{ $metode }} <span class="mx-1.5 text-line">•</span> {{ $pembayaran->dibayar_pada->translatedFormat('j M Y, H.i') }} WIB
    </p>
    <h1 id="judulSukses" class="mt-1.5 mb-2 font-serif text-[32px] font-medium">Pembayaran <em class="text-maroon">Berhasil!</em></h1>
    <p class="mx-auto mb-4.5 max-w-[360px] text-sm leading-relaxed text-muted">Terima kasih, pembayaranmu telah kami terima. Buket spesialmu siap diproses oleh florist kami.</p>

    <div class="inline-flex items-center gap-2.5 rounded-[14px] border border-[#F2C9C6] bg-blush-50 py-2 pr-2 pl-4">
        <span class="text-xs font-semibold tracking-wider text-muted"># NOMOR PESANAN</span>
        <strong class="text-[17px] tracking-wide">{{ $pesanan->kode_pesanan }}</strong>
        <button type="button" data-salin="{{ $pesanan->kode_pesanan }}"
                class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-line bg-white px-3 text-xs font-semibold text-maroon print:hidden">
            <i class="bi bi-copy"></i> <span>Salin</span>
        </button>
    </div>

    <hr class="my-5 border-line-soft">

    <h2 class="mb-3 flex gap-2 text-left text-sm font-semibold"><i class="bi bi-bag"></i> Rincian Pesanan</h2>
    <ul class="mb-3 flex flex-col gap-3 rounded-2xl border border-line bg-[#FFF6F7] p-3.5">
        @foreach ($pesanan->detailPesanan as $d)
            <li class="flex items-center gap-3 text-left">
                @if ($d->produk?->gambar_url)
                    <img src="{{ $d->produk->gambar_url }}" alt="" class="h-15 w-13 shrink-0 rounded-[10px] object-cover">
                @else
                    <span class="flex h-15 w-13 shrink-0 items-center justify-center rounded-[10px] bg-blush text-rose"><i class="bi bi-flower1"></i></span>
                @endif
                <div class="flex flex-1 flex-col">
                    <strong class="text-sm">{{ $d->nama_produk }}</strong>
                    <small class="text-xs text-muted">Ukuran: {{ $d->ukuran_lengkap }}</small>
                </div>
                <div class="flex flex-col text-right">
                    <strong class="text-sm">{{ $rupiah($d->harga * $d->jumlah) }}</strong>
                    <small class="text-xs text-muted">Jumlah: {{ $d->jumlah }}</small>
                </div>
            </li>
        @endforeach
    </ul>

    <div class="flex gap-3 rounded-2xl bg-[#FBE4E8] px-4 py-3.5 text-left">
        <i class="bi {{ $pickup ? 'bi-shop' : 'bi-truck' }} text-maroon"></i>
        <div>
            @if ($pickup)
                <strong class="text-[13.5px]">Metode: Ambil Sendiri di Atelier (Pick Up)</strong>
                <p class="my-1 text-[12.5px] text-ink-soft">Waktu Pengambilan: <b>{{ $pesanan->tanggal->translatedFormat('l, j M Y') }} · {{ $pesanan->slot_waktu }}</b></p>
                <small class="text-xs text-muted">Lokasi: Jl. Tridaya Barat, Karanganyar, Indramayu</small>
            @else
                <strong class="text-[13.5px]">Metode: {{ $kurir['nama'] ?? 'Kurir Pengiriman' }}</strong>
                <p class="my-1 text-[12.5px] text-ink-soft">Waktu Pengantaran: <b>{{ $pesanan->tanggal->translatedFormat('l, j M Y') }} · {{ $pesanan->slot_waktu }}</b></p>
                <small class="text-xs text-muted">Tujuan: {{ $pesanan->nama_penerima }} · {{ $pesanan->alamat }}</small>
            @endif
        </div>
    </div>

    <dl class="mt-4 flex flex-col gap-2 text-left text-sm">
        <div class="flex justify-between"><dt class="text-ink-soft">Subtotal Produk</dt><dd>{{ $rupiah($pesanan->subtotal) }}</dd></div>
        @if ($pickup)
            <div class="flex justify-between"><dt class="text-ink-soft">Biaya Pengambilan (Pick Up)</dt><dd class="text-ok">Gratis (Rp0)</dd></div>
        @else
            <div class="flex justify-between"><dt class="text-ink-soft">Ongkos Kurir</dt><dd>{{ $rupiah($pesanan->ongkir) }}</dd></div>
        @endif
        <div class="flex justify-between border-t border-line-soft pt-2.5"><dt class="font-semibold">Total Pembayaran (Lunas)</dt><dd class="font-bold text-maroon">{{ $rupiah($pesanan->total_harga) }}</dd></div>
    </dl>

    <hr class="my-5 border-line-soft">

    <div class="grid gap-2.5 sm:grid-cols-2 print:hidden">
        <a href="{{ url('/pesanan') }}" class="btn min-h-12 rounded-xl bg-maroon text-white hover:bg-maroon-dark"><i class="bi bi-receipt"></i> Lihat Riwayat Pesanan</a>
        <button type="button" onclick="window.print()" class="btn min-h-12 rounded-xl border border-line bg-white hover:border-maroon hover:text-maroon"><i class="bi bi-download"></i> Unduh Bukti Pembayaran</button>
    </div>

    <p class="mt-4.5 text-[13px] text-muted print:hidden">Butuh bantuan terkait pesanan?
        <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-maroon">Chat Admin WhatsApp <i class="bi bi-arrow-right"></i></a>
    </p>
</main>

<script>
    document.querySelectorAll('[data-salin]').forEach(btn => {
        btn.addEventListener('click', async () => {
            await navigator.clipboard.writeText(btn.dataset.salin);
            const teks = btn.querySelector('span');
            teks.textContent = 'Tersalin!';
            setTimeout(() => teks.textContent = 'Salin', 1500);
        });
    });
</script>
</body>
</html>