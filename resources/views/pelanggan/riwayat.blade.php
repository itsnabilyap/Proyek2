@extends('layouts.pelanggan')

@section('title', 'Riwayat Pesanan')

@section('topbar-search')
    <form class="search" method="GET" action="{{ route('katalog.index') }}" role="search">
        <i class="bi bi-search"></i>
        <input type="search" name="q" placeholder="Temukan bucket favoritmu di sini...">
    </form>
@endsection

@push('styles')
<style>
    .head-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .arsip { padding: 6px 14px; border: 1px solid var(--line); border-radius: 20px; background: #fff3f5; color: var(--rose); font-size: 11px; }
    .arsip i { font-size: 7px; vertical-align: middle; margin-right: 4px; }

    .tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
    .tab { padding: 6px 16px; border: 1px solid var(--line); border-radius: 20px; background: #fff3f5; color: var(--rose); font-size: 12px; transition: all .15s; }
    .tab:hover { background: var(--rose-soft); }
    .tab.active { background: var(--rose); border-color: var(--rose); color: #fff; }

    .order { margin-bottom: 18px; padding: 14px; border: 1px solid var(--line); border-radius: 16px; background: #fff; box-shadow: 0 2px 10px rgba(140,59,80,.05); }
    .order-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; padding: 9px 12px; margin-bottom: 12px; border-radius: 10px; background: #fff3f5; font-size: 11px; color: #5b484d; }
    .order-top .no { display: inline-flex; align-items: center; gap: 8px; }
    .order-top .no b { color: var(--text); }
    .salin { padding: 1px 8px; border: 1px solid var(--line); border-radius: 6px; background: #fff; font-size: 10px; color: var(--rose); cursor: pointer; }
    .badge-status { display: inline-flex; align-items: center; gap: 5px; padding: 3px 11px; border-radius: 20px; font-size: 10.5px; font-weight: 600; }
    .st-proses { background: #fde1e7; color: #b23a57; }
    .st-siap { background: #fff0cf; color: #8a6414; }
    .st-selesai { background: #d7edcf; color: #29472c; }

    .item { display: flex; gap: 14px; padding: 4px 4px 12px; }
    .item + .item { padding-top: 12px; border-top: 1px dashed #f3e3e7; }
    .item .th { display: grid; place-items: center; width: 96px; height: 96px; flex-shrink: 0; overflow: hidden; border-radius: 10px; background: linear-gradient(145deg, #fbd9e2, #f2c0cf); color: #fff; font-size: 28px; }
    .item .th img { width: 100%; height: 100%; object-fit: cover; }
    .item .info { flex: 1; min-width: 0; }
    .item h3 { margin: 0 0 4px; font-family: 'Playfair Display', Georgia, serif; font-size: 15px; }
    .item .meta { color: var(--muted); font-size: 11px; line-height: 1.6; }
    .item .price { text-align: right; flex-shrink: 0; }
    .item .price b { display: block; font-size: 13.5px; }
    .item .price small { color: var(--muted); font-size: 10px; }

    .pickup { display: flex; gap: 8px; margin-top: 10px; padding: 9px 12px; border: 1px solid #f9d3dc; border-radius: 10px; background: #fff0f3; font-size: 11px; line-height: 1.6; }
    .pickup i { color: var(--rose); }
    .pickup small { display: block; color: #6d565c; }

    .order-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
    .pill { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border: 0; border-radius: 20px; background: #fde4ea; color: var(--rose); font-size: 11px; font-weight: 600; cursor: pointer; transition: background .15s; }
    .pill:hover { background: #f9d2dc; }
    .pill.solid { background: var(--rose); color: #fff; }
    .pill.solid:hover { background: var(--rose-dark); }
    .push-right { margin-left: auto; }
    .link-help { display: inline-flex; align-items: center; gap: 5px; color: #5b484d; font-size: 11px; }
    .stars { margin-left: auto; color: #e0a227; font-size: 12px; white-space: nowrap; }
    .stars small { color: var(--muted); margin-left: 4px; }

    .timeline { display: flex; margin-top: 14px; padding: 14px 8px 4px; border-top: 1px solid #f3e3e7; }
    .step { position: relative; flex: 1; text-align: center; font-size: 10.5px; color: var(--muted); }
    .step::before { content: ''; position: absolute; top: 9px; left: -50%; width: 100%; height: 2px; background: #f1d3db; }
    .step:first-child::before { display: none; }
    .step .dot { position: relative; z-index: 1; display: block; width: 20px; height: 20px; margin: 0 auto 6px; border-radius: 50%; background: #f1d3db; color: #fff; line-height: 20px; font-size: 11px; }
    .step.done { color: var(--text); font-weight: 600; }
    .step.done .dot, .step.done::before { background: var(--rose); }

    .review-box { margin-top: 12px; padding: 14px; border: 1px solid #f3e3e7; border-radius: 12px; background: #fff7f8; }
    .review-box textarea { width: 100%; min-height: 70px; padding: 9px 12px; border: 1px solid var(--line); border-radius: 10px; font: inherit; font-size: 12px; resize: vertical; }
    .star-input { display: inline-flex; flex-direction: row-reverse; margin-bottom: 8px; }
    .star-input input { display: none; }
    .star-input label { font-size: 22px; color: #e6d3d8; cursor: pointer; padding: 0 2px; }
    .star-input input:checked ~ label, .star-input label:hover, .star-input label:hover ~ label { color: #e0a227; }
    .err { margin: 4px 0 0; color: #9f2948; font-size: 11px; }

    .cta { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-top: 26px; padding: 26px 28px; border-radius: 18px; background: linear-gradient(120deg, #fdeef1, #f9cfd9); }
    .cta .tag { display: inline-block; margin-bottom: 10px; padding: 2px 10px; border: 1px solid var(--line); border-radius: 20px; background: #fff; color: var(--muted); font-size: 9.5px; letter-spacing: .06em; }
    .cta h3 { margin: 0 0 6px; font-family: 'Playfair Display', Georgia, serif; font-size: 19px; }
    .cta p { margin: 0; max-width: 430px; color: #6d565c; line-height: 1.55; }
    .empty { padding: 50px 0; text-align: center; color: var(--muted); }

    @media (max-width: 640px) {
        .item { flex-wrap: wrap; }
        .item .price { text-align: left; }
        .push-right, .stars { margin-left: 0; }
    }
</style>
@endpush

@section('content')
@php
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $tabs = [
        'semua' => 'Semua',
        'diproses' => 'Sedang Dirangkai',
        'siap' => 'Siap Diambil / Dikirim',
        'selesai' => 'Selesai',
    ];
@endphp

<div class="head-row">
    <div>
        <h1>Riwayat Pesanan Saya</h1>
        <p class="lead">Lihat daftar pesanan buket bunga, pantau status pesanan, dan cek riwayat pembelian Anda.</p>
    </div>
    <span class="arsip"><i class="bi bi-circle-fill"></i> Total {{ $hitung['semua'] }} Arsip Pesanan</span>
</div>

<div class="tabs">
    @foreach($tabs as $kunci => $label)
        <a href="{{ route('pesanan.riwayat', $kunci === 'semua' ? [] : ['status' => $kunci]) }}"
           class="tab {{ $filter === $kunci ? 'active' : '' }}">{{ $label }} ({{ $hitung[$kunci] }})</a>
    @endforeach
</div>

@forelse($pesanan as $p)
    @php
        $status = $p->status_pesanan;
        $kelas = in_array($status, ['Selesai']) ? 'st-selesai'
               : (in_array($status, ['Siap Diambil', 'Dikirim']) ? 'st-siap' : 'st-proses');
        $ikon = $status === 'Selesai' ? 'bi-check-circle' : ($kelas === 'st-siap' ? 'bi-box-seam' : 'bi-flower1');
        $labelStatus = $status === 'Diproses' ? 'Sedang Dirangkai' : $status;
        $kurir = $p->metode_pengambilan === 'kurir';
        $custom = $p->customPesanan;
        $selesai = $status === 'Selesai';
        $rev = $ulasan->get($p->id_pesanan);
        $tgl = $p->tanggal_pesanan->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y, H.i');

        // Tahapan pelacakan
        $tahap = ['Pesanan Diterima', 'Sedang Dirangkai', $kurir ? 'Dikirim' : 'Siap Diambil', 'Selesai'];
        $posisi = match ($status) {
            'Menunggu Konfirmasi' => 0,
            'Diproses' => 1,
            'Siap Diambil', 'Dikirim' => 2,
            'Selesai' => 3,
            default => 0,
        };
    @endphp

    <article class="order" id="pesanan-{{ $p->id_pesanan }}">
        <div class="order-top">
            <span class="no">
                <i class="bi bi-calendar3"></i> {{ $tgl }} WIB &nbsp;•&nbsp;
                #<b>{{ $p->nomor }}</b>
                <button type="button" class="salin" data-nomor="{{ $p->nomor }}">Salin</button>
            </span>
            <span class="badge-status {{ $kelas }}"><i class="bi {{ $ikon }}"></i> {{ $labelStatus }}</span>
        </div>

        {{-- Item pesanan katalog --}}
        @foreach($p->detailPesanan as $d)
            <div class="item">
                <span class="th">
                    @if($d->produk?->gambar && file_exists(public_path('images/produk/'.$d->produk->gambar)))
                        <img src="{{ asset('images/produk/'.$d->produk->gambar) }}" alt="{{ $d->produk->nama_produk }}">
                    @else
                        <i class="bi bi-flower1"></i>
                    @endif
                </span>
                <div class="info">
                    <h3>{{ $d->produk?->nama_produk ?? 'Produk' }}</h3>
                    <div class="meta">
                        Jumlah: {{ $d->jumlah }} &nbsp;•&nbsp; {{ $rp($d->harga) }} per buket
                        @if($d->produk?->kategori) &nbsp;•&nbsp; {{ $d->produk->kategori->nama_kategori }} @endif
                    </div>
                </div>
                <div class="price">
                    <b>{{ $rp($d->subtotal) }}</b>
                    <small>({{ \App\Models\Pesanan::PEMBAYARAN[$p->metode_pembayaran]['nama'] ?? $p->metode_pembayaran }})</small>
                </div>
            </div>
        @endforeach

        {{-- Pesanan custom --}}
        @if($custom)
            <div class="item">
                <span class="th"><i class="bi bi-pencil-square"></i></span>
                <div class="info">
                    <h3>Bucket Custom{{ $custom->jenis_bucket ? ' - ' . $custom->jenis_bucket : '' }}</h3>
                    <div class="meta">
                        Budget: {{ $custom->budget ?? '-' }}
                        @if($custom->tanggal_dibutuhkan) &nbsp;•&nbsp; Dibutuhkan: {{ $custom->tanggal_dibutuhkan->locale('id')->translatedFormat('d M Y') }} @endif
                        @if($custom->catatan_custom)<br>Catatan: “{{ $custom->catatan_custom }}”@endif
                    </div>
                </div>
                <div class="price">
                    @if($custom->harga_custom > 0)
                        <b>{{ $rp($custom->harga_custom) }}</b>
                    @else
                        <small>Harga menunggu konfirmasi florist</small>
                    @endif
                </div>
            </div>
        @endif

        {{-- Info pengambilan / pengiriman --}}
        @if($p->tanggal_pengiriman)
            <div class="pickup">
                <i class="bi {{ $kurir ? 'bi-truck' : 'bi-shop' }}"></i>
                <div>
                    <b>{{ $kurir ? 'Dikirim Kurir' : 'Ambil di Toko (Pick Up)' }}</b>
                    <small>Jadwal: {{ $p->tanggal_pengiriman->locale('id')->translatedFormat('l, d M Y') }} ({{ $p->slot_waktu }})</small>
                    <small>{{ $kurir ? $p->alamat_pengiriman : 'Lokasi: ' . \App\Models\Pesanan::ALAMAT_TOKO }}</small>
                </div>
            </div>
        @endif

        {{-- Aksi --}}
        <div class="order-actions">
            @if(! $selesai)
                <button type="button" class="pill solid" data-track="trk-{{ $p->id_pesanan }}"><i class="bi bi-graph-up-arrow"></i> Lacak Status Pesanan</button>
                @if($p->dibayar_pada)
                    <a href="{{ route('pesanan.sukses', $p->id_pesanan) }}" class="pill"><i class="bi bi-receipt"></i> Unduh Bukti Pembayaran</a>
                @endif
            @else
                <form method="POST" action="{{ route('pesanan.beli-lagi', $p->id_pesanan) }}">
                    @csrf
                    <button type="submit" class="pill"><i class="bi bi-arrow-repeat"></i> Beli Lagi</button>
                </form>
                @if($rev)
                    <button type="button" class="pill" data-review="rev-{{ $p->id_pesanan }}"><i class="bi bi-pencil"></i> Ubah Ulasan</button>
                    <span class="stars">
                        @for($i = 1; $i <= 5; $i++)<i class="bi {{ $i <= $rev->rating ? 'bi-star-fill' : 'bi-star' }}"></i>@endfor
                        <small>({{ number_format($rev->rating, 1, ',', '.') }})</small>
                    </span>
                @else
                    <button type="button" class="pill" data-review="rev-{{ $p->id_pesanan }}"><i class="bi bi-star"></i> Beri Ulasan / Review</button>
                @endif
            @endif

            @unless($selesai)
                <a class="link-help push-right" target="_blank" rel="noopener noreferrer"
                   href="https://wa.me/62895325820465?text={{ urlencode('Halo admin @bucket.imy, saya mau tanya pesanan ' . $p->nomor) }}">
                    <i class="bi bi-headset"></i> Hubungi Kami
                </a>
            @endunless
        </div>

        {{-- Lacak status --}}
        @if(! $selesai)
            <div class="timeline" id="trk-{{ $p->id_pesanan }}" hidden>
                @foreach($tahap as $i => $nama)
                    <div class="step {{ $i <= $posisi ? 'done' : '' }}">
                        <span class="dot">@if($i <= $posisi)<i class="bi bi-check"></i>@endif</span>
                        {{ $nama }}
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Form ulasan --}}
        @if($selesai)
            @php $buka = old('_pesanan') == $p->id_pesanan && $errors->any(); @endphp
            <div class="review-box" id="rev-{{ $p->id_pesanan }}" {{ $buka ? '' : 'hidden' }}>
                <form method="POST" action="{{ route('pesanan.ulasan', ['id' => $p->id_pesanan, 'status' => $filter === 'semua' ? null : $filter]) }}">
                    @csrf
                    <input type="hidden" name="_pesanan" value="{{ $p->id_pesanan }}">
                    <div class="star-input">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="r{{ $p->id_pesanan }}-{{ $i }}" name="rating" value="{{ $i }}"
                                {{ (int) old('rating', $rev?->rating) === $i ? 'checked' : '' }}>
                            <label for="r{{ $p->id_pesanan }}-{{ $i }}" title="{{ $i }} bintang"><i class="bi bi-star-fill"></i></label>
                        @endfor
                    </div>
                    @if($buka) @error('rating')<p class="err">{{ $message }}</p>@enderror @endif
                    <textarea name="isi_testimoni" maxlength="1000" placeholder="Ceritakan pengalamanmu dengan buket ini...">{{ old('_pesanan') == $p->id_pesanan ? old('isi_testimoni') : $rev?->isi_testimoni }}</textarea>
                    @if($buka) @error('isi_testimoni')<p class="err">{{ $message }}</p>@enderror @endif
                    <div class="order-actions">
                        <button type="submit" class="pill solid"><i class="bi bi-send"></i> Kirim Ulasan</button>
                    </div>
                </form>
            </div>
        @endif
    </article>
@empty
    <div class="empty">
        <i class="bi bi-receipt" style="font-size:34px;"></i>
        <p>Belum ada pesanan pada kategori ini.</p>
        <a href="{{ route('katalog.index') }}" class="btn">Lihat Katalog</a>
    </div>
@endforelse

<section class="cta">
    <div>
        <span class="tag">BESPOKE SERVICE</span>
        <h3>Ingin pesan custom buket berikutnya?</h3>
        <p>Diskusikan tema warna, jenis rangkaian bunga impian, pita, dan kartu ucapan khusus melalui formulir pesanan custom kami.</p>
    </div>
    <a href="{{ route('pesanan.custom') }}" class="btn">Formulir Custom <i class="bi bi-arrow-right"></i></a>
</section>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-track]').forEach(b =>
        b.addEventListener('click', () => { const el = document.getElementById(b.dataset.track); el.hidden = !el.hidden; }));
    document.querySelectorAll('[data-review]').forEach(b =>
        b.addEventListener('click', () => { const el = document.getElementById(b.dataset.review); el.hidden = !el.hidden; }));
    document.querySelectorAll('.salin').forEach(b => b.addEventListener('click', () => {
        (navigator.clipboard ? navigator.clipboard.writeText(b.dataset.nomor) : Promise.reject())
            .then(() => { b.textContent = 'Tersalin'; }).catch(() => {});
    }));
</script>
@endpush
