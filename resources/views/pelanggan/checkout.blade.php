@extends('layouts.pelanggan')

@section('title', 'Check Out & Keranjang')

@section('topbar-search')
    <form class="search" method="GET" action="{{ route('katalog.index') }}" role="search">
        <i class="bi bi-search"></i>
        <input type="search" name="q" placeholder="Temukan bucket favoritmu di sini...">
    </form>
@endsection

@push('styles')
<style>
    .tabs { display: flex; gap: 8px; margin: 14px 0 20px; }
    .tabs a { padding: 6px 16px; border: 1px solid var(--line); border-radius: 20px; background: #fff; font-size: 11px; color: #5b484d; }
    .tabs a.active { background: var(--rose); border-color: var(--rose); color: #fff; }

    .co-grid { display: grid; grid-template-columns: minmax(0, 1.55fr) minmax(280px, 1fr); gap: 22px; align-items: start; }
    .panel { padding: 22px 24px; background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(140,59,80,.06); }
    .panel h3 { margin: 0 0 18px; padding-bottom: 12px; border-bottom: 1px solid #f3e3e7; font-size: 13px; }

    .f { margin-bottom: 14px; }
    .f > label:not(.kurir), .f > .lbl { display: block; margin-bottom: 6px; font-size: 11px; font-weight: 600; color: #4a383d; }
    .f input, .f select, .f textarea {
        width: 100%; padding: 0 14px; height: 38px; border: 0; border-radius: 8px;
        background: #eceef1; font: inherit; font-size: 12px; color: var(--text);
    }
    .f textarea { height: 84px; padding: 11px 14px; resize: vertical; }
    .f input:focus, .f select:focus, .f textarea:focus { outline: 2px solid #e3a9b8; }
    .f .err { margin-top: 4px; color: #b42347; font-size: 11px; }
    .two { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    .store {
        display: flex; gap: 10px; padding: 12px 14px; border: 1px solid #f3cdd6; border-radius: 10px; background: #fff5f7;
    }
    .store .pin { display: grid; place-items: center; width: 28px; height: 28px; flex-shrink: 0; border-radius: 50%; background: #fbd9e2; color: var(--rose); }
    .store strong { display: block; color: var(--rose); font-size: 12px; }
    .store p { margin: 2px 0 6px; color: #6d565c; font-size: 11px; line-height: 1.5; }
    .store small { font-size: 10.5px; color: var(--muted); }
    .store small b { color: #2f8a4a; font-weight: 500; }

    .kurir { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 10px; padding: 12px 14px; border: 1px solid #e3e5ea; border-radius: 10px; background: #fff; cursor: pointer; }
    .kurir:has(input:checked) { border-color: var(--rose); background: #fff7f9; box-shadow: 0 0 0 1px var(--rose) inset; }
    .kurir input { width: auto; height: auto; margin-top: 2px; accent-color: var(--rose); }
    .kurir .body { flex: 1; min-width: 0; }
    .kurir strong { font-size: 12px; }
    .kurir small { display: block; margin-top: 2px; color: var(--muted); font-size: 10.5px; line-height: 1.45; }
    .kurir .tarif { font-size: 11px; font-weight: 600; white-space: nowrap; }

    /* Ringkasan */
    .item { display: flex; gap: 10px; align-items: center; padding-bottom: 14px; margin-bottom: 14px; border-bottom: 1px solid #f3e3e7; }
    .item .th { display: grid; place-items: center; width: 46px; height: 46px; flex-shrink: 0; overflow: hidden; border-radius: 8px; background: linear-gradient(145deg,#fbd9e2,#f2c0cf); color: #fff; }
    .item .th img { width: 100%; height: 100%; object-fit: cover; }
    .item strong { display: block; font-size: 12px; }
    .item small { color: var(--muted); font-size: 10.5px; }
    .item .price { display: block; margin-top: 2px; color: var(--rose); font-size: 11px; font-weight: 700; }
    .item form { margin-left: auto; }
    .item .rm { border: 0; background: transparent; color: #c06a7e; cursor: pointer; font-size: 14px; }
    .row { display: flex; justify-content: space-between; margin: 8px 0; font-size: 11px; color: #5b484d; }
    .row b { font-weight: 600; color: var(--text); }
    .row .free { color: #1f9d55; font-weight: 700; }
    .total { display: flex; justify-content: space-between; align-items: baseline; margin: 14px 0 18px; padding-top: 14px; border-top: 1px solid #f3e3e7; }
    .total span { font-weight: 700; font-size: 12px; }
    .total strong { color: var(--rose); font-size: 17px; }

    .pay { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 8px 0 14px; }
    .pay input { position: absolute; opacity: 0; pointer-events: none; }
    .pay label { position: relative; }
    .pay span { display: block; padding: 10px 6px; border: 1px solid #e3e5ea; border-radius: 10px; text-align: center; cursor: pointer; background: #fff; }
    .pay span b { display: block; font-size: 12px; }
    .pay span small { color: var(--muted); font-size: 9.5px; }
    .pay input:checked + span { border-color: var(--rose); background: #fff5f7; box-shadow: 0 0 0 1px var(--rose) inset; }
    .pay input:focus-visible + span { outline: 2px solid #e3a9b8; outline-offset: 2px; }

    .note { display: flex; gap: 8px; margin-bottom: 14px; padding: 10px 12px; border-radius: 10px; background: #fff3f5; color: #7a5e65; font-size: 10px; line-height: 1.5; }
    .note i { color: var(--rose); }
    .confirm { width: 100%; padding: 12px; font-size: 12px; border-radius: 10px; }

    .promo-banner { display: flex; align-items: center; gap: 12px; margin-top: 22px; padding: 12px 16px; border-radius: 12px; background: #fde4ea; }
    .promo-banner .ico { display: grid; place-items: center; width: 34px; height: 34px; flex-shrink: 0; border-radius: 50%; background: #fff; color: var(--rose); }
    .promo-banner strong { display: block; font-size: 12px; }
    .promo-banner small { color: #7a5e65; font-size: 10.5px; }
    .promo-banner .pill { margin-left: auto; padding: 7px 16px; border: 1px solid var(--line); border-radius: 20px; background: #fff; font-size: 11px; font-weight: 600; white-space: nowrap; }

    .empty { padding: 60px 0; text-align: center; color: var(--muted); }
    .empty .btn { margin-top: 14px; }

    @media (max-width: 960px) { .co-grid { grid-template-columns: 1fr; } .two { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
@php
    $kurir = $metode === 'kurir';
    $user = auth()->user();
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $layananAktif = old('layanan_kurir', 'motor');
    $ongkir = $kurir ? \App\Models\Pesanan::LAYANAN_KURIR[$layananAktif]['tarif'] ?? 0 : 0;
@endphp

    <h1>{{ $kurir ? 'Checkout Pesanan - Kurir Pengiriman' : 'Checkout Pesanan - Ambil Sendiri (Pick Up)' }}</h1>
    <p class="lead" style="margin-bottom:0">
        {{ $kurir
            ? 'Lengkapi detail penerima dan pilih kurir khusus untuk menjaga bucket tetap segar dan rapi sampai tujuan.'
            : 'Lengkapi data pengambil dan tentukan jadwal penjemputan buket langsung di studio florist kami.' }}
    </p>

    <nav class="tabs" aria-label="Metode pengambilan">
        <a href="{{ route('keranjang.index') }}" class="{{ $kurir ? '' : 'active' }}">Ambil di Toko (Pick Up)</a>
        <a href="{{ route('keranjang.index', ['metode' => 'kurir']) }}" class="{{ $kurir ? 'active' : '' }}">Kurir Pengiriman</a>
    </nav>

    @if($items->isEmpty())
        <div class="panel empty">
            <i class="bi bi-cart3" style="font-size:34px"></i>
            <p>Keranjangmu masih kosong.</p>
            <a href="{{ route('katalog.index') }}" class="btn">Lihat Katalog</a>
        </div>
    @else
    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf
        <input type="hidden" name="metode" value="{{ $metode }}">

        @if($errors->any())
            <div class="alert-error" role="alert">
                <strong>Pesanan belum bisa dikonfirmasi:</strong>
                <ul style="margin:6px 0 0;padding-left:18px">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="co-grid">
            {{-- KIRI --}}
            <section class="panel">
                <h3>{{ $kurir ? 'Informasi Alamat & Jadwal Pengantaran' : 'Informasi Pengambil & Jadwal Penjemputan' }}</h3>

                <div class="f">
                    <label for="nama_penerima">{{ $kurir ? 'Nama Penerima' : 'Nama Pengambil' }}</label>
                    <input id="nama_penerima" type="text" name="nama_penerima" value="{{ old('nama_penerima', $user->name) }}" required>
                    @error('nama_penerima')<div class="err">{{ $message }}</div>@enderror
                </div>

                <div class="f">
                    <label for="no_whatsapp">{{ $kurir ? 'Nomor WhatsApp Penerima' : 'Nomor WhatsApp Pengambil' }}</label>
                    <input id="no_whatsapp" type="tel" name="no_whatsapp" value="{{ old('no_whatsapp', $user->no_hp) }}" placeholder="08xxxxxxxxxx" required>
                    @error('no_whatsapp')<div class="err">{{ $message }}</div>@enderror
                </div>

                @if($kurir)
                    <div class="f">
                        <label for="alamat">Alamat Lengkap Pengiriman</label>
                        <textarea id="alamat" name="alamat" style="height:110px" required>{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')<div class="err">{{ $message }}</div>@enderror
                    </div>

                    <div class="f">
                        <label for="patokan">Catatan / Patokan Pengantaran</label>
                        <input id="patokan" type="text" name="patokan" value="{{ old('patokan') }}" placeholder="contoh: depan toko madura">
                        @error('patokan')<div class="err">{{ $message }}</div>@enderror
                    </div>

                    <div class="f">
                        <span class="lbl">Pilih Layanan Kurir</span>
                        @foreach(\App\Models\Pesanan::LAYANAN_KURIR as $key => $k)
                            <label class="kurir">
                                <input type="radio" name="layanan_kurir" value="{{ $key }}" data-tarif="{{ $k['tarif'] }}" @checked($layananAktif === $key)>
                                <span class="body">
                                    <strong>{{ $k['nama'] }}</strong>
                                    <small>{{ $k['info'] }}</small>
                                </span>
                                <span class="tarif">{{ number_format($k['tarif'], 0, ',', '.') }}</span>
                            </label>
                        @endforeach
                        @error('layanan_kurir')<div class="err">{{ $message }}</div>@enderror
                    </div>
                @else
                    <div class="f">
                        <span class="lbl">Lokasi Toko</span>
                        <div class="store">
                            <span class="pin"><i class="bi bi-geo-alt"></i></span>
                            <div>
                                <strong>Toko @bucket.imy</strong>
                                <p>{{ \App\Models\Pesanan::ALAMAT_TOKO }}</p>
                                <small><i class="bi bi-clock"></i> Jam Buka: 09.00 - 18.00 WIB &nbsp;•&nbsp; <b>Available dadakan</b></small>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="two">
                    <div class="f">
                        <label for="tanggal">{{ $kurir ? 'Tanggal Diantar' : 'Pilih Tanggal Pengambilan' }}</label>
                        <input id="tanggal" type="date" name="tanggal" min="{{ now('Asia/Jakarta')->toDateString() }}"
                            value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}" required>
                        @error('tanggal')<div class="err">{{ $message }}</div>@enderror
                    </div>
                    <div class="f">
                        <label for="slot_waktu">{{ $kurir ? 'Slot Waktu Tiba' : 'Pilih Slot Waktu Pengambilan' }}</label>
                        <select id="slot_waktu" name="slot_waktu" required>
                            @foreach(\App\Models\Pesanan::SLOT_WAKTU as $slot)
                                <option value="{{ $slot }}" @selected(old('slot_waktu', \App\Models\Pesanan::SLOT_WAKTU[1]) === $slot)>{{ $slot }}</option>
                            @endforeach
                        </select>
                        @error('slot_waktu')<div class="err">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="f" style="margin-bottom:0">
                    <label for="catatan">Catatan Tambahan / Tulisan Kartu Ucapan Gratis</label>
                    <input id="catatan" type="text" name="catatan" value="{{ old('catatan') }}" placeholder="contoh: Happy Graduation!">
                    @error('catatan')<div class="err">{{ $message }}</div>@enderror
                </div>
            </section>

            {{-- KANAN --}}
            <aside class="panel">
                <h3 style="border:0;padding:0;margin-bottom:14px">Ringkasan Pesanan</h3>

                @foreach($items as $item)
                    <div class="item">
                        <span class="th">
                            @if($item->produk->gambar && file_exists(public_path('images/produk/'.$item->produk->gambar)))
                                <img src="{{ asset('images/produk/'.$item->produk->gambar) }}" alt="">
                            @else
                                <i class="bi bi-flower1"></i>
                            @endif
                        </span>
                        <div>
                            <strong>{{ $item->produk->nama_produk }}</strong>
                            <small>Jumlah: {{ $item->jumlah }}</small>
                            <span class="price">{{ $rp($item->subtotal) }}</span>
                        </div>
                        <button type="submit" class="rm" title="Hapus dari keranjang"
                            form="hapus-{{ $item->id_cart }}" formnovalidate>
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endforeach

                <div class="row"><span>Subtotal Produk</span><b>{{ $rp($subtotal) }}</b></div>
                @if($kurir)
                    <div class="row"><span>Ongkos Kurir Florist</span><b id="ongkir">{{ $rp($ongkir) }}</b></div>
                @else
                    <div class="row"><span>Biaya Penjemputan (Pick Up)</span><span class="free">Gratis (Rp 0)</span></div>
                @endif
                <div class="row"><span>Kartu Ucapan &amp; Pita Eksklusif</span><span class="free">Gratis (Rp 0)</span></div>

                <div class="total">
                    <span>Total Pembayaran</span>
                    <strong id="total" data-subtotal="{{ $subtotal }}">{{ $rp($subtotal + $ongkir) }}</strong>
                </div>

                <span class="lbl" style="display:block;font-size:11px;font-weight:600">Metode Pembayaran</span>
                <div class="pay">
                    @foreach(\App\Models\Pesanan::PEMBAYARAN as $key => $m)
                        <label>
                            <input type="radio" name="metode_pembayaran" value="{{ $key }}" @checked(old('metode_pembayaran', 'QRIS') === $key)>
                            <span><b>{{ $m['nama'] }}</b><small>{{ $m['info'] }}</small></span>
                        </label>
                    @endforeach
                </div>
                @error('metode_pembayaran')<div class="err" style="color:#b42347;font-size:11px;margin:-8px 0 10px">{{ $message }}</div>@enderror

                <div class="note">
                    <i class="bi bi-shield-check"></i>
                    <span>Buket dirangkai sesaat sebelum {{ $kurir ? 'dikirim' : 'diambil' }} agar tampilan bunga tetap indah dan terjaga maksimal.</span>
                </div>

                <button type="submit" class="btn confirm">
                    {{ $kurir ? 'Konfirmasi & Bayar Sekarang' : 'Konfirmasi & Ambil di Toko' }} <i class="bi bi-arrow-right"></i>
                </button>
            </aside>
        </div>
    </form>

    {{-- Form hapus dipisah dari form checkout supaya tombol Enter tidak memicu hapus --}}
    @foreach($items as $item)
        <form id="hapus-{{ $item->id_cart }}" method="POST" action="{{ route('keranjang.destroy', $item->id_cart) }}" hidden>
            @csrf
            @method('DELETE')
        </form>
    @endforeach
    @endif

    @include('pelanggan._banner')
@endsection

@push('scripts')
<script>
    (function () {
        const total = document.getElementById('total');
        const ongkir = document.getElementById('ongkir');
        if (!total || !ongkir) return;
        const rp = (n) => 'Rp ' + n.toLocaleString('id-ID');
        document.querySelectorAll('input[name=layanan_kurir]').forEach((r) => {
            r.addEventListener('change', () => {
                const tarif = Number(r.dataset.tarif);
                ongkir.textContent = rp(tarif);
                total.textContent = rp(Number(total.dataset.subtotal) + tarif);
            });
        });
    })();
</script>
@endpush
