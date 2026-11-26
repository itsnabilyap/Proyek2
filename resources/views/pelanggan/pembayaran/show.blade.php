@extends('layouts.checkout')

@section('title', 'Pembayaran ' . $pesanan->kode_pesanan)
@section('langkah', '3')

@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $pickup = $pesanan->metode_pengiriman === 'pickup';
    $qris = $pembayaran->metode === 'qris';
    $kurir = \App\Models\Pesanan::KURIR[$pesanan->layanan_kurir] ?? null;
    $hpTampil = trim(preg_replace('/(\d{4})(?=\d)/', '$1 ', $pesanan->no_whatsapp));
    $wa = 'https://wa.me/6283184710305?text=' . urlencode("Halo @bucket.imy, saya ada kendala pembayaran pesanan {$pesanan->kode_pesanan}.");

    $kartu = 'flex flex-col gap-4.5 rounded-[22px] border border-line-soft bg-white p-5 shadow-[0_20px_44px_-34px_rgb(94_38_51/0.35)] sm:px-7 sm:py-6.5';
    $judulKartu = 'text-lg font-semibold';
    $kotakAbu = 'rounded-2xl border border-line-soft bg-paper';
    $btnGaris = 'inline-flex min-h-10.5 items-center gap-2 rounded-xl border border-line bg-white px-4.5 text-sm font-semibold hover:border-maroon hover:text-maroon';
    $tagOk = 'rounded-full bg-ok-soft px-2.5 py-0.5 text-[11.5px] font-semibold text-ok';
    $infoBox = 'flex flex-col gap-1 rounded-[14px] border border-line-soft bg-paper px-4 py-3.5';
@endphp

@section('content')
<div class="wrap pt-10 pb-20">

    {{-- ===== KEPALA ===== --}}
    <h1 class="mt-1 mb-2 font-serif text-[32px] font-medium sm:text-[42px]">Pembayaran Pesanan <em class="text-maroon">· {{ $pickup ? 'Ambil Sendiri (Pick Up)' : 'Kurir Pengiriman' }}</em></h1>
    <p class="mb-5 text-sm text-muted">
        Selesaikan pembayaran sebelum batas waktu agar pesanan dapat segera dirangkai{{ $pickup ? ' oleh florist kami' : ' dan dikirim tepat waktu' }}.
    </p>

    {{-- Sisa waktu bayar. (Tombol "Kurir Pengiriman" dihapus: cara terima sudah ada di judul & ringkasan) --}}
    <div class="mb-5.5">
        <p role="timer" class="inline-flex items-center gap-2.5 rounded-full border border-[#F2C9C6] bg-danger-soft px-4.5 py-2.5 text-sm font-semibold text-danger">
            <span class="size-2 rounded-full bg-danger"></span> Sisa Waktu Pembayaran:
            <strong id="countdown" data-deadline="{{ $pembayaran->kedaluwarsa_pada->toIso8601String() }}" class="font-mono text-base tracking-wider">--:--:--</strong>
        </p>
    </div>

    @if (session('info'))
        <p role="status" class="mb-4 rounded-[10px] bg-blush px-3.5 py-2.5 text-sm font-semibold text-maroon-dark">{{ session('info') }}</p>
    @endif

    <div class="grid items-start gap-7 md:grid-cols-[1fr_360px]">
        {{-- ================= KIRI ================= --}}
        <div class="flex flex-col gap-5">
            <section class="{{ $kartu }}">
                @if ($qris)
                    {{-- ---------- QRIS ---------- --}}
                    <div class="flex flex-wrap items-center gap-3.5">
                        <span class="rounded-[10px] border border-line px-2.5 py-2 text-xs font-extrabold text-maroon">QRIS</span>
                        <div class="flex min-w-[200px] flex-1 flex-col gap-0.5">
                            <strong class="text-base">Scan QRIS</strong>
                            <small class="text-[12.5px] text-muted">Bisa dibayar lewat GoPay, ShopeePay, OVO, DANA, dan m-banking pendukung QRIS.</small>
                        </div>
                        <span class="{{ $tagOk }}">Verifikasi Otomatis</span>
                    </div>

                    <div class="{{ $kotakAbu }} flex flex-col items-center gap-3.5 rounded-[18px] p-6">
                        <p class="flex items-center gap-2 text-sm font-semibold">
                            <span class="rounded-md bg-[#1F2A5A] px-2 py-0.5 text-[11px] font-extrabold text-white">QRIS</span> @bucket.imy
                        </p>
                        @if ($pembayaran->qr_url)
                            <img src="{{ $pembayaran->qr_url }}" alt="Kode QR pembayaran {{ $pesanan->kode_pesanan }}"
                                 class="size-55 rounded-2xl bg-white p-3 shadow-[0_10px_24px_-16px_rgb(94_38_51/0.35)]">
                            <a href="{{ $pembayaran->qr_url }}" target="_blank" rel="noopener noreferrer" class="{{ $btnGaris }}">
                                <i class="bi bi-download"></i> Unduh Kode QR
                            </a>
                        @else
                            <p class="text-xs text-muted">Kode QR gagal dimuat. Klik "Cek Status" atau ganti ke Virtual Account.</p>
                        @endif
                    </div>

                    <div class="{{ $kotakAbu }} flex flex-wrap items-center justify-between gap-3 px-5 py-4.5">
                        <div>
                            <small class="mb-1 block text-xs font-semibold tracking-wider text-muted">TOTAL TAGIHAN PEMBAYARAN</small>
                            <strong class="text-[22px] text-maroon sm:text-[28px]">{{ $rupiah($pembayaran->jumlah) }}</strong>
                        </div>
                        <span class="text-xs text-muted">Nominal terisi otomatis saat scan</span>
                    </div>
                @else
                    {{-- ---------- BCA VA ---------- --}}
                    <div class="flex flex-wrap items-center gap-3.5">
                        <span class="rounded-[10px] border border-line px-2.5 py-2 text-xs font-extrabold text-[#0060AF]">BCA</span>
                        <div class="flex min-w-[200px] flex-1 flex-col gap-0.5">
                            <strong class="text-base">BCA Virtual Account</strong>
                            <small class="text-[12.5px] text-muted">Verifikasi otomatis tanpa perlu konfirmasi manual</small>
                        </div>
                        <span class="{{ $tagOk }}">Otomatis Dicek</span>
                    </div>

                    @foreach ([['NOMOR VIRTUAL ACCOUNT', trim(chunk_split($pembayaran->nomor_va, 4, ' ')), $pembayaran->nomor_va, 'Salin Nomor', true],
                               ['TOTAL NOMINAL TAGIHAN', $rupiah($pembayaran->jumlah), $pembayaran->jumlah, 'Salin Nominal', false]] as [$judul, $tampil, $salin, $tombol, $va])
                        {{-- Angka + tombol salin sebaris. Kalau kolomnya sempit (HP, tablet 768–1023px) tombol turun ke bawah --}}
                        <div class="{{ $kotakAbu }} flex items-center justify-between gap-3 px-5 py-4.5 max-sm:flex-wrap md:max-lg:flex-wrap">
                            <div class="min-w-0">
                                <small class="mb-1 block text-xs font-semibold tracking-wider text-muted">{{ $judul }}</small>
                                <strong @class(['block font-mono text-lg tracking-wide whitespace-nowrap lg:text-lg xl:text-[22px]' => $va, 'text-[22px] text-maroon sm:text-[28px]' => ! $va])>{{ $tampil }}</strong>
                            </div>
                            <button type="button" data-salin="{{ $salin }}" class="{{ $btnGaris }} shrink-0">
                                <i class="bi bi-copy"></i> <span>{{ $tombol }}</span>
                            </button>
                        </div>
                    @endforeach

                    <div class="flex flex-col gap-2.5">
                        <span class="text-sm font-semibold">Panduan Pembayaran BCA Virtual Account:</span>
                        <details class="rounded-xl bg-[#F4EEF0]">
                            <summary class="px-4 py-3.5 text-sm font-medium">1. Pembayaran via BCA Mobile (m-BCA)</summary>
                            <ol class="list-decimal pr-5 pb-4 pl-9 text-[13px] leading-loose text-ink-soft">
                                <li>Buka BCA Mobile, pilih <b>m-Transfer</b> → <b>BCA Virtual Account</b>.</li>
                                <li>Masukkan nomor VA di atas, lalu tekan <b>Send</b>.</li>
                                <li>Periksa nama dan nominal, lalu masukkan PIN m-BCA.</li>
                            </ol>
                        </details>
                        <details class="rounded-xl bg-[#F4EEF0]">
                            <summary class="px-4 py-3.5 text-sm font-medium">2. Pembayaran via ATM BCA</summary>
                            <ol class="list-decimal pr-5 pb-4 pl-9 text-[13px] leading-loose text-ink-soft">
                                <li>Masukkan kartu dan PIN, pilih <b>Transaksi Lainnya</b> → <b>Transfer</b> → <b>ke Rekening BCA Virtual Account</b>.</li>
                                <li>Masukkan nomor VA di atas, lalu tekan <b>Benar</b>.</li>
                                <li>Periksa detail tagihan, lalu konfirmasi.</li>
                            </ol>
                        </details>
                    </div>
                @endif
            </section>

            {{-- ---------- Ringkasan tujuan ---------- --}}
            <section class="{{ $kartu }}">
                <div class="flex items-center justify-between gap-3 border-b border-line-soft pb-3.5">
                    <h2 class="{{ $judulKartu }}">{{ $pickup ? 'Detail Rencana Pengambilan (Pick Up)' : 'Ringkasan Tujuan & Pengiriman' }}</h2>
                    <span class="rounded-full border border-[#F2C9C6] bg-danger-soft px-3 py-1 text-xs font-semibold text-maroon">Terkonfirmasi</span>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="{{ $infoBox }}">
                        <small class="text-xs text-muted">{{ $pickup ? 'Pengambil' : 'Penerima Pesanan' }}</small>
                        <strong class="text-sm">{{ $pesanan->nama_penerima }}</strong>
                        <span class="text-[12.5px] text-muted">{{ $hpTampil }}</span>
                    </div>

                    @if ($pickup)
                        <div class="{{ $infoBox }} sm:col-span-2">
                            <small class="text-xs text-muted">Lokasi Toko</small>
                            <strong class="text-sm">Atelier @bucket.imy</strong>
                            <span class="text-[12.5px] text-muted">Jl. Tridaya Barat, Karanganyar, Kec. Indramayu, Kabupaten Indramayu, Jawa Barat 45213</span>
                        </div>
                    @else
                        <div class="{{ $infoBox }}">
                            <small class="text-xs text-muted">Layanan Kurir Terpilih</small>
                            <strong class="text-sm">{{ $kurir['nama'] ?? '-' }}</strong>
                            <span class="text-[12.5px] text-muted">{{ $kurir['ket'] ?? '' }}</span>
                        </div>
                    @endif

                    <div class="{{ $infoBox }}">
                        <small class="text-xs text-muted">{{ $pickup ? 'Jadwal Pengambilan' : 'Jadwal Pengantaran' }}</small>
                        <strong class="text-sm">{{ $pesanan->tanggal->translatedFormat('l, j M Y') }}</strong>
                        <span class="text-[12.5px] font-semibold text-maroon">Slot: {{ $pesanan->slot_waktu }}</span>
                    </div>

                    @foreach ($pesanan->detailPesanan->whereNotNull('kartu_ucapan') as $d)
                        <div class="{{ $infoBox }}">
                            <small class="text-xs text-muted">Kartu Ucapan · {{ $d->nama_produk }}</small>
                            <strong class="text-sm">“{{ $d->kartu_ucapan }}”</strong>
                            <span class="text-[12.5px] text-ok">Gratis pita &amp; kartu ucapan</span>
                        </div>
                    @endforeach

                    @unless ($pickup)
                        <div class="{{ $infoBox }} sm:col-span-3">
                            <small class="text-xs text-muted">Alamat Lengkap &amp; Patokan</small>
                            <strong class="text-sm">{{ $pesanan->alamat }}</strong>
                            @if ($pesanan->patokan) <span class="text-[12.5px] text-muted">Patokan: {{ $pesanan->patokan }}</span> @endif
                        </div>
                    @endunless
                </div>
            </section>
        </div>

        {{-- ================= KANAN ================= --}}
        <div class="flex flex-col gap-5 md:sticky md:top-24">
            <aside class="{{ $kartu }} gap-4">
                <h2 class="{{ $judulKartu }} border-b border-line-soft pb-3.5">Ringkasan Pesanan</h2>

                <ul class="flex flex-col gap-4">
                    @foreach ($pesanan->detailPesanan as $d)
                        <li class="flex gap-3.5">
                            @if ($d->produk?->gambar_url)
                                <img src="{{ $d->produk->gambar_url }}" alt="" class="h-19.5 w-16 shrink-0 rounded-[10px] object-cover">
                            @else
                                <span class="flex h-19.5 w-16 shrink-0 items-center justify-center rounded-[10px] bg-blush text-[22px] text-rose"><i class="bi bi-flower1"></i></span>
                            @endif
                            <div class="flex flex-col gap-0.5">
                                <strong class="text-[15px]">{{ $d->nama_produk }}</strong>
                                <small class="text-xs text-muted">Ukuran: {{ $d->ukuran_lengkap }} · {{ $d->jumlah }} buket</small>
                                <b class="mt-1 text-sm text-maroon">{{ $rupiah($d->harga * $d->jumlah) }}</b>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <dl class="flex flex-col gap-3 border-y border-line-soft py-3.5 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-muted">Subtotal Produk</dt><dd class="font-semibold">{{ $rupiah($pesanan->subtotal) }}</dd></div>
                    @if ($pickup)
                        <div class="flex justify-between gap-3"><dt class="text-muted">Biaya Penjemputan (Pick Up)</dt><dd class="font-semibold text-ok">Gratis (Rp0)</dd></div>
                    @else
                        <div class="flex justify-between gap-3"><dt class="text-muted">Ongkos Kurir ({{ $pesanan->layanan_kurir === 'mobil' ? 'Mobil' : 'Motor' }})</dt><dd class="font-semibold">{{ $rupiah($pesanan->ongkir) }}</dd></div>
                    @endif
                    <div class="flex justify-between gap-3"><dt class="text-muted">Kartu Ucapan &amp; Pita</dt><dd class="font-semibold text-ok">Gratis (Rp0)</dd></div>
                </dl>

                <div class="flex items-baseline justify-between gap-3">
                    <span class="text-[15px] font-semibold text-maroon">Total Pembayaran</span>
                    <strong class="text-[26px] font-bold text-maroon">{{ $rupiah($pesanan->total_harga) }}</strong>
                </div>

                <form method="POST" action="{{ route('pembayaran.metode', $pesanan) }}" class="flex flex-col gap-2.5"
                      data-konfirmasi="Ganti metode pembayaran?" data-pesan="Kode atau nomor tagihan sebelumnya tidak bisa dipakai lagi." data-tombol="Ya, ganti" data-jenis="tanya">
                    @csrf
                    <span class="text-sm font-semibold">Metode Pembayaran Dipilih</span>
                    <div class="grid grid-cols-2 gap-2.5">
                        @foreach (['qris' => ['QRIS', 'Dana / GoPay / OVO'], 'va' => ['BCA VA', 'Virtual Account']] as $kode => [$nama, $ket])
                            @php $aktif = $pembayaran->metode === $kode; @endphp
                            <button type="submit" name="metode" value="{{ $kode }}" @if ($aktif) aria-pressed="true" disabled @endif
                                    @class([
                                        'relative flex min-h-17.5 flex-col items-center justify-center gap-0.5 rounded-[14px] border bg-white p-2.5',
                                        'border-maroon ring-1 ring-maroon text-maroon' => $aktif,
                                        'border-line hover:border-maroon' => ! $aktif,
                                    ])>
                                <strong class="text-[15px]">{{ $nama }}</strong><small class="text-[11px] text-muted">{{ $ket }}</small>
                                @if ($aktif)
                                    <span class="absolute -top-2.5 -right-2.5 flex size-5.5 items-center justify-center rounded-full bg-maroon text-xs text-white">✓</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    <span class="text-center text-xs text-muted">Klik metode lain untuk mengganti.</span>
                </form>

                <p class="flex gap-2.5 rounded-[14px] border border-line bg-blush-50 p-3.5 text-xs leading-relaxed text-ink-soft">
                    <i class="bi bi-shield-check text-maroon"></i>
                    Buket dirangkai sesaat sebelum {{ $pickup ? 'diambil' : 'dikirim' }} agar tampilan bunga tetap segar, indah, dan terjaga maksimal.
                </p>

                <form method="POST" action="{{ route('pembayaran.cek', $pesanan) }}">
                    @csrf
                    <button type="submit" class="btn btn-lg btn-primary w-full shadow-btn">Saya Sudah Bayar / Cek Status</button>
                </form>
            </aside>

            <a href="{{ $wa }}" target="_blank" rel="noopener noreferrer"
               class="group flex items-center gap-3.5 rounded-[22px] border border-line-soft bg-white px-5 py-4.5 shadow-[0_20px_44px_-34px_rgb(94_38_51/0.35)]">
                <span class="flex size-10.5 shrink-0 items-center justify-center rounded-full bg-ok-soft text-ok"><i class="bi bi-chat"></i></span>
                <span class="flex flex-1 flex-col"><strong class="text-sm">Ada kendala pembayaran?</strong><small class="text-xs text-muted">Chat admin lewat WhatsApp</small></span>
                <span class="text-[13px] font-semibold text-maroon group-hover:underline">Chat Admin <i class="bi bi-arrow-right"></i></span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Hitung mundur
    (() => {
        const el = document.getElementById('countdown');
        const batas = new Date(el.dataset.deadline).getTime();
        const dua = n => String(n).padStart(2, '0');

        const tick = () => {
            const sisa = Math.max(0, Math.floor((batas - Date.now()) / 1000));
            el.textContent = `${dua(Math.floor(sisa / 3600))}:${dua(Math.floor(sisa % 3600 / 60))}:${dua(sisa % 60)}`;
            if (sisa === 0) { clearInterval(timer); location.reload(); }
        };
        const timer = setInterval(tick, 1000);
        tick();
    })();

    // Tombol salin
    document.querySelectorAll('[data-salin]').forEach(btn => {
        btn.addEventListener('click', async () => {
            await navigator.clipboard.writeText(btn.dataset.salin);
            const teks = btn.querySelector('span');
            const awal = teks.textContent;
            teks.textContent = 'Tersalin!';
            setTimeout(() => teks.textContent = awal, 1500);
        });
    });
</script>
@endpush