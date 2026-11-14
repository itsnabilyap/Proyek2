@extends('layouts.pelanggan')

@section('title', 'Pembayaran Berhasil')

@section('topbar-search')
    <form class="search" method="GET" action="{{ route('katalog.index') }}" role="search">
        <i class="bi bi-search"></i>
        <input type="search" name="q" placeholder="Temukan bucket favoritmu di sini...">
    </form>
@endsection

@push('styles')
<style>
    .receipt { max-width: 640px; margin: 8px auto 0; padding: 30px 32px; background: #fff; border-radius: 16px; box-shadow: 0 2px 14px rgba(140,59,80,.07); }
    .receipt .head { text-align: center; padding-bottom: 20px; border-bottom: 1px solid #f3e3e7; }
    .receipt .check { display: inline-grid; place-items: center; width: 30px; height: 30px; margin-bottom: 14px; border-radius: 50%; background: #222; color: #fff; font-size: 15px; }
    .receipt .via { font-size: 11px; color: #5b484d; }
    .receipt h1 { margin: 10px 0 6px; font-size: 20px; text-align: center; }
    .receipt .thanks { margin: 0 auto 16px; max-width: 360px; color: #5b484d; line-height: 1.55; }
    .nomor { display: inline-flex; align-items: center; gap: 8px; padding: 7px 14px; border: 1px solid #f3cdd6; border-radius: 10px; background: #fff3f5; font-size: 11px; color: var(--rose); }
    .nomor b { color: var(--text); font-size: 12px; }
    .nomor button { padding: 2px 8px; border: 1px solid var(--line); border-radius: 6px; background: #fff; font-size: 10px; cursor: pointer; color: #5b484d; }

    .receipt h4 { display: flex; align-items: center; gap: 6px; margin: 20px 0 10px; font-size: 12px; }
    .line { display: flex; align-items: center; gap: 12px; padding: 12px; margin-bottom: 10px; border-radius: 10px; background: #fff0f3; border: 1px solid #fbdce4; }
    .line .th { display: grid; place-items: center; width: 46px; height: 46px; flex-shrink: 0; overflow: hidden; border-radius: 8px; background: linear-gradient(145deg,#fbd9e2,#f2c0cf); color: #fff; }
    .line .th img { width: 100%; height: 100%; object-fit: cover; }
    .line strong { display: block; font-size: 12px; }
    .line small { color: var(--muted); font-size: 10.5px; }
    .line .r { margin-left: auto; text-align: right; }
    .line .r b { display: block; font-size: 12px; }

    .method { padding: 12px 14px; border-radius: 10px; background: #fde4ea; font-size: 11px; line-height: 1.6; }
    .method strong { font-size: 11.5px; }
    .method small { display: block; color: #7a5e65; font-size: 10.5px; }

    .sum .row { display: flex; justify-content: space-between; margin: 10px 0; font-size: 11.5px; color: #5b484d; }
    .sum .row.t { margin-top: 14px; padding-top: 12px; border-top: 1px solid #f3e3e7; color: var(--text); font-weight: 700; }
    .free { color: #1f9d55; font-weight: 700; }

    .actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 22px; padding-top: 20px; border-top: 1px solid #f3e3e7; }
    .actions a, .actions button { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 11px; border: 1px solid #ece3e5; border-radius: 10px; background: #fff; font: inherit; font-size: 11.5px; font-weight: 600; cursor: pointer; }
    .actions a:hover, .actions button:hover { background: #fff5f7; }
    .help { margin-top: 14px; text-align: center; font-size: 11px; color: #5b484d; }
    .help a { font-weight: 700; }

    @media print {
        .sidebar, .topbar, .actions, .help, .nomor button { display: none !important; }
        body { background: #fff; }
        .page { padding: 0; }
        .receipt { box-shadow: none; }
    }
</style>
@endpush

@section('content')
@php
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $kurir = $pesanan->metode_pengambilan === 'kurir';
    $bayar = \App\Models\Pesanan::PEMBAYARAN[$pesanan->metode_pembayaran]['nama'] ?? $pesanan->metode_pembayaran;
    $waktuBayar = $pesanan->dibayar_pada?->timezone('Asia/Jakarta')->locale('id');
    $tgl = $pesanan->tanggal_pengiriman->locale('id')->translatedFormat('l, d M Y');
    $subtotal = $pesanan->detailPesanan->sum('subtotal');
@endphp

<div class="receipt">
    <div class="head">
        <span class="check"><i class="bi bi-check-lg"></i></span>
        <div class="via">Lunas via {{ $bayar }} &nbsp;•&nbsp; {{ $waktuBayar?->translatedFormat('d M Y, H.i') }} WIB</div>
        <h1>Pembayaran Berhasil!</h1>
        <p class="thanks">Terima kasih, pembayaranmu telah kami terima. Buket spesialmu siap diproses oleh kami.</p>
        <span class="nomor"># NOMOR PESANAN: <b id="nomor">{{ $pesanan->nomor }}</b>
            <button type="button" id="salin"><i class="bi bi-copy"></i> Salin</button>
        </span>
    </div>

    <h4><i class="bi bi-clipboard"></i> Rincian Pesanan</h4>

    @foreach($pesanan->detailPesanan as $d)
        <div class="line">
            <span class="th">
                @if($d->produk?->gambar && file_exists(public_path('images/produk/'.$d->produk->gambar)))
                    <img src="{{ asset('images/produk/'.$d->produk->gambar) }}" alt="">
                @else
                    <i class="bi bi-flower1"></i>
                @endif
            </span>
            <div>
                <strong>{{ $d->produk?->nama_produk ?? 'Produk' }}</strong>
                <small>{{ $rp($d->harga) }} per buket</small>
            </div>
            <div class="r"><b>{{ $rp($d->subtotal) }}</b><small>Jumlah: {{ $d->jumlah }}</small></div>
        </div>
    @endforeach

    <div class="method">
        @if($kurir)
            <strong><i class="bi bi-truck"></i> Metode: Kurir Pengiriman ({{ \App\Models\Pesanan::LAYANAN_KURIR[$pesanan->layanan_kurir]['nama'] ?? '-' }})</strong>
            <small>Waktu Tiba: <b>{{ $tgl }} • {{ $pesanan->slot_waktu }}</b></small>
            <small>Alamat: {!! nl2br(e($pesanan->alamat_pengiriman)) !!}</small>
        @else
            <strong><i class="bi bi-shop"></i> Metode: Ambil Sendiri di Studio (Pick Up)</strong>
            <small>Waktu Pengambilan: <b>{{ $tgl }} • {{ $pesanan->slot_waktu }}</b></small>
            <small>Lokasi: Jl. Tridaya Barat, Karanganyar</small>
        @endif
    </div>

    <div class="sum" style="margin-top:18px">
        <div class="row"><span>Subtotal Produk</span><span>{{ $rp($subtotal) }}</span></div>
        <div class="row">
            <span>{{ $kurir ? 'Ongkos Kurir Florist' : 'Biaya Pengambilan (Pick Up)' }}</span>
            @if($pesanan->ongkir > 0)<span>{{ $rp($pesanan->ongkir) }}</span>@else<span class="free">Gratis (Rp 0)</span>@endif
        </div>
        <div class="row t"><span>Total Pembayaran (Lunas)</span><span>{{ $rp($pesanan->total_harga) }}</span></div>
    </div>

    <div class="actions">
        <a href="{{ route('pesanan.riwayat') }}"><i class="bi bi-receipt"></i> Lihat Riwayat Pesanan</a>
        <button type="button" onclick="window.print()"><i class="bi bi-download"></i> Unduh Bukti Pembayaran</button>
    </div>
    <p class="help"><i class="bi bi-headset"></i> Butuh bantuan terkait pesanan?
        <a href="https://wa.me/62895325820465?text={{ urlencode('Halo admin @bucket.imy, saya mau tanya pesanan ' . $pesanan->nomor) }}"
           target="_blank" rel="noopener noreferrer">Chat Admin WhatsApp →</a></p>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('salin')?.addEventListener('click', function () {
        const t = document.getElementById('nomor').textContent.trim();
        (navigator.clipboard ? navigator.clipboard.writeText(t) : Promise.reject()).then(() => {
            this.innerHTML = '<i class="bi bi-check2"></i> Tersalin';
        }).catch(() => {});
    });
</script>
@endpush
