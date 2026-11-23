@extends('layouts.checkout')

@section('title', 'Checkout')
@section('langkah', '2')

@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $metode = old('metode_pengiriman', 'kurir');
    $kurirDipilih = old('layanan_kurir', 'motor');
    $user = auth()->user();

    $kartu = 'flex flex-col gap-4.5 rounded-[22px] border border-line-soft bg-white p-5 shadow-[0_20px_44px_-34px_rgb(94_38_51/0.35)] sm:px-7 sm:py-6.5';
    $judulKartu = 'border-b border-line-soft pb-3.5 text-lg font-semibold';
    $label = 'flex flex-col gap-2 text-sm font-semibold';
    $input = 'field h-12 bg-white font-normal';
    $salah = fn ($kolom) => $errors->has($kolom) ? 'border-danger' : '';
@endphp

@section('content')
<form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm" novalidate class="wrap pt-10 pb-20">
    @csrf
    @isset($custom)
        <input type="hidden" name="custom_id" value="{{ $custom->id }}">
    @else
        @foreach ($items as $item)
            <input type="hidden" name="item[]" value="{{ $item->id }}">
        @endforeach
    @endisset

    <p class="mb-2 text-[11px] font-semibold tracking-[.12em] text-maroon">CHECKOUT</p>
    <h1 class="mt-1 mb-2 font-serif text-[32px] font-medium sm:text-[42px]">Checkout Pesanan<em id="judulPickup" class="text-maroon" @if ($metode !== 'pickup') hidden @endif> · Ambil di Toko</em></h1>
    <p id="introKurir" class="mb-5 text-sm text-muted" @if ($metode === 'pickup') hidden @endif>
        Lengkapi detail penerima dan pilih kurir khusus agar buket tetap segar dan rapi sampai tujuan.
    </p>
    <p id="introPickup" class="mb-5 text-sm text-muted" @if ($metode !== 'pickup') hidden @endif>
        Lengkapi data pengambil dan tentukan jadwal penjemputan buket langsung di atelier kami, tanpa ongkos kirim.
    </p>

    @error('keranjang')
        <p role="alert" class="mb-4 rounded-[10px] bg-danger-soft px-3.5 py-2.5 text-sm font-semibold text-danger">{{ $message }}</p>
    @enderror

    {{-- Pilihan metode --}}
    <fieldset class="mb-6 flex flex-wrap gap-2.5">
        <legend class="sr-only">Metode pengiriman</legend>
        @foreach (['pickup' => ['bi-shop', 'Ambil di Toko (Pick Up)'], 'kurir' => ['bi-truck', 'Kurir Pengiriman']] as $nilai => [$ikon, $teks])
            <label>
                <input type="radio" name="metode_pengiriman" value="{{ $nilai }}" @checked($metode === $nilai) class="peer sr-only">
                <span class="inline-flex min-h-11.5 items-center gap-2 rounded-full border border-line bg-white px-5.5 text-sm font-semibold transition-colors hover:border-maroon peer-checked:border-maroon peer-checked:bg-maroon peer-checked:text-white peer-focus-visible:ring-4 peer-focus-visible:ring-maroon/20">
                    <i class="bi {{ $ikon }}"></i> {{ $teks }}
                </span>
            </label>
        @endforeach
    </fieldset>

    <div class="grid items-start gap-7 md:grid-cols-[1fr_360px]">
        {{-- ================= KIRI: FORM ================= --}}
        <section class="{{ $kartu }}">
            <h2 class="{{ $judulKartu }}" data-kurir>Informasi Alamat &amp; Jadwal Pengantaran</h2>
            <h2 class="{{ $judulKartu }}" data-pickup>Informasi Pengambil &amp; Jadwal Penjemputan</h2>

            <div class="grid gap-4.5 sm:grid-cols-2">
                <label class="{{ $label }}">
                    <span><span data-kurir>Nama Penerima</span><span data-pickup>Nama Pengambil</span></span>
                    <input type="text" name="nama_penerima" value="{{ old('nama_penerima', $user->name) }}" autocomplete="name" required
                           class="{{ $input }} {{ $salah('nama_penerima') }}">
                    @error('nama_penerima') <span class="field-error">{{ $message }}</span> @enderror
                </label>
                <label class="{{ $label }}">
                    <span><span data-kurir>Nomor WhatsApp Penerima</span><span data-pickup>Nomor WhatsApp</span></span>
                    <input type="tel" name="no_whatsapp" value="{{ old('no_whatsapp', $user->no_hp) }}" placeholder="081234567890" autocomplete="tel" required
                           class="{{ $input }} {{ $salah('no_whatsapp') }}">
                    @error('no_whatsapp') <span class="field-error">{{ $message }}</span> @enderror
                </label>
            </div>

            {{-- ---- Khusus KURIR ---- --}}
            <div class="flex flex-col gap-4.5" data-kurir>
                <label class="{{ $label }}">Alamat Lengkap Pengiriman
                    <textarea name="alamat" rows="3" placeholder="Nama jalan, nomor rumah, kecamatan" required
                              class="field bg-white font-normal {{ $salah('alamat') }}">{{ old('alamat') }}</textarea>
                    @error('alamat') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <label class="{{ $label }}">
                    <span>Catatan / Patokan Pengantaran <span class="font-normal text-muted">(opsional)</span></span>
                    <input type="text" name="patokan" value="{{ old('patokan') }}" placeholder="cth: Pintu masuk utara, dekat parkiran motor" class="{{ $input }}">
                </label>

                <fieldset class="flex flex-col gap-2.5">
                    <legend class="{{ $label }} mb-2.5">Pilih Layanan Kurir</legend>
                    @foreach ($kurir as $kode => $k)
                        <label class="relative">
                            <input type="radio" name="layanan_kurir" value="{{ $kode }}" data-ongkir="{{ $k['ongkir'] }}"
                                   data-nama="{{ $k['nama'] }}" @checked($kurirDipilih === $kode) required
                                   class="peer absolute top-5.5 left-4.5 size-4.5 accent-maroon">
                            <span class="flex flex-col gap-1 rounded-[14px] border border-line py-4 pr-4.5 pl-13 transition-colors hover:border-maroon peer-checked:border-maroon peer-checked:bg-blush-50 peer-checked:ring-1 peer-checked:ring-maroon">
                                <span class="flex justify-between gap-3 text-[15px]"><strong>{{ $k['nama'] }}</strong><b class="text-maroon">{{ $rupiah($k['ongkir']) }}</b></span>
                                <small class="text-[12.5px] text-muted">{{ $k['ket'] }}</small>
                            </span>
                        </label>
                    @endforeach
                    @error('layanan_kurir') <span class="field-error">{{ $message }}</span> @enderror
                </fieldset>
            </div>

            {{-- ---- Khusus PICKUP ---- --}}
            <div class="flex flex-col gap-4.5" data-pickup>
                <span class="{{ $label }}">Lokasi Toko</span>
                <div class="flex gap-3.5 rounded-[14px] border border-line bg-blush-50 p-4.5">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-[10px] bg-blush text-lg text-maroon"><i class="bi bi-geo-alt"></i></span>
                    <div class="text-[13.5px] text-ink-soft">
                        <strong class="text-sm text-maroon">Atelier @bucket.imy</strong>
                        <p class="mt-1.5">Jl. Tridaya Barat, Karanganyar, Kec. Indramayu, Kabupaten Indramayu, Jawa Barat 45213</p>
                        <p class="mt-1.5 flex flex-wrap items-center gap-2 text-[13px]"><i class="bi bi-clock"></i> Senin – Sabtu 08.00 – 19.00 WIB
                            <span class="rounded-full bg-ok-soft px-2.5 py-0.5 text-[11.5px] font-semibold text-ok">Bisa pesan dadakan</span></p>
                        <a href="https://www.google.com/maps/search/?api=1&query=Jl.+Tridaya+Barat,+Karanganyar,+Indramayu,+Jawa+Barat+45213"
                           target="_blank" rel="noopener noreferrer" class="mt-2.5 inline-block text-[13px] font-semibold text-maroon hover:underline">Buka di Google Maps <i class="bi bi-arrow-up-right"></i></a>
                    </div>
                </div>
            </div>

            {{-- ---- Jadwal (dua mode) ---- --}}
            <div class="grid gap-4.5 sm:grid-cols-2">
                <label class="{{ $label }}">
                    <span><span data-kurir>Tanggal Diantar</span><span data-pickup>Tanggal Diambil</span></span>
                    <input type="date" name="tanggal" value="{{ old('tanggal', isset($custom) ? max($custom->tanggal_dibutuhkan->toDateString(), now()->toDateString()) : now()->toDateString()) }}" min="{{ now()->toDateString() }}" required
                           class="{{ $input }} {{ $salah('tanggal') }}">
                    @error('tanggal') <span class="field-error">{{ $message }}</span> @enderror
                </label>
                <label class="{{ $label }}">
                    <span><span data-kurir>Slot Waktu Tiba</span><span data-pickup>Slot Waktu Ambil</span></span>
                    <select name="slot_waktu" required class="{{ $input }} {{ $salah('slot_waktu') }}">
                        @foreach ($slot as $s)
                            <option value="{{ $s }}" @selected(old('slot_waktu') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('slot_waktu') <span class="field-error">{{ $message }}</span> @enderror
                </label>
            </div>

            <label class="{{ $label }}">
                <span>Catatan untuk Florist <span class="font-normal text-muted">(opsional)</span></span>
                <input type="text" name="catatan_florist" value="{{ old('catatan_florist') }}" maxlength="300"
                       placeholder="Contoh: pita warna dusty rose, jangan pakai bunga lily" class="{{ $input }}">
                <span class="text-xs font-normal text-muted">{{ isset($custom) ? 'Detail desain sudah disepakati lewat WhatsApp. Tambahkan catatan lain bila perlu.' : 'Tulisan kartu ucapan sudah diambil dari keranjang. Lihat di ringkasan pesanan.' }}</span>
            </label>
        </section>

        {{-- ================= KANAN: RINGKASAN ================= --}}
        <aside class="{{ $kartu }} gap-4 md:sticky md:top-24">
            <h2 class="{{ $judulKartu }}">Ringkasan Pesanan</h2>

            <ul class="flex flex-col gap-4">
                @foreach ($items as $item)
                    @php $produk = $item->ukuran->produk; @endphp
                    <li class="flex gap-3.5">
                        @if ($produk->gambar_url)
                            <img src="{{ $produk->gambar_url }}" alt="" class="h-19.5 w-16 shrink-0 rounded-[10px] object-cover">
                        @else
                            <span class="flex h-19.5 w-16 shrink-0 items-center justify-center rounded-[10px] bg-blush text-[22px] text-rose"><i class="bi bi-flower1"></i></span>
                        @endif
                        <div class="flex flex-col gap-0.5">
                            <strong class="text-[15px]">{{ $produk->nama_produk }}</strong>
                            <small class="text-xs text-muted">Ukuran {{ $item->nama_ukuran ?? $item->ukuran->nama_ukuran }}{{ $item->varian ? ' · ' . $item->varian : '' }}{{ $item->warna ? ' · ' . $item->warna : '' }} · {{ $item->jumlah }} buket</small>
                            @if (! isset($custom))
                            <small class="text-xs text-muted">Kartu: {!! $item->kartu_ucapan ? '<em class="text-ink">' . e($item->kartu_ucapan) . '</em>' : '-' !!}</small>
                            @else
                            <small class="text-xs text-muted">Request custom #{{ $custom->id }} · harga final dari admin</small>
                            @endif
                            <b class="mt-1 text-sm text-maroon">{{ $rupiah($item->subtotal) }}</b>
                        </div>
                    </li>
                @endforeach
            </ul>

            <dl class="flex flex-col gap-3 border-y border-line-soft py-3.5 text-sm">
                <div class="flex justify-between gap-3"><dt class="text-muted">Subtotal Produk</dt><dd class="font-semibold">{{ $rupiah($subtotal) }}</dd></div>
                <div class="flex justify-between gap-3" data-kurir><dt id="labelOngkir" class="text-muted">Ongkos Kurir</dt><dd id="nilaiOngkir" class="font-semibold">-</dd></div>
                <div class="flex justify-between gap-3" data-pickup><dt class="text-muted">Biaya Penjemputan (Pick Up)</dt><dd class="font-semibold text-ok">Gratis (Rp0)</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-muted">Kartu Ucapan &amp; Pita</dt><dd class="font-semibold text-ok">Gratis</dd></div>
            </dl>

            <div class="flex items-baseline justify-between gap-3">
                <span class="text-[15px] font-semibold text-maroon">Total Pembayaran</span>
                <strong id="nilaiTotal" data-subtotal="{{ $subtotal }}" class="text-[26px] font-bold text-maroon">{{ $rupiah($subtotal) }}</strong>
            </div>

            <fieldset class="flex flex-col gap-2.5">
                <legend class="{{ $label }} mb-2.5">Metode Pembayaran</legend>
                <div class="grid gap-2.5 min-[577px]:grid-cols-2">
                    @foreach (['qris' => ['QRIS', 'Dana, GoPay, OVO, ShopeePay, m-banking'], 'va' => ['Virtual Account', 'BCA, BNI, BRI, Mandiri']] as $nilai => [$nama, $ket])
                        <label>
                            <input type="radio" name="metode_pembayaran" value="{{ $nilai }}" @checked(old('metode_pembayaran', 'qris') === $nilai) class="peer sr-only">
                            <span class="flex min-h-24 flex-col items-center justify-center gap-1 rounded-[14px] border border-line p-3 text-center transition-colors hover:border-maroon peer-checked:border-maroon peer-checked:bg-blush-50 peer-checked:ring-1 peer-checked:ring-maroon peer-checked:[&>strong]:text-maroon peer-focus-visible:ring-4 peer-focus-visible:ring-maroon/20">
                                <strong class="text-[15px]">{{ $nama }}</strong>
                                <small class="text-[11px] leading-snug text-muted">{{ $ket }}</small>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('metode_pembayaran') <span class="field-error">{{ $message }}</span> @enderror
            </fieldset>

            <p class="flex gap-2.5 rounded-[14px] border border-line bg-blush-50 p-3.5 text-xs leading-relaxed text-ink-soft">
                <i class="bi bi-shield-check text-maroon"></i>
                Buket dirangkai sesaat sebelum jadwal agar tetap segar. Foto hasil rangkaian dikirim untuk Anda setujui terlebih dulu.
            </p>

            <button type="submit" class="btn btn-lg btn-primary shadow-btn">
                <span data-kurir>Konfirmasi &amp; Bayar Sekarang</span>
                <span data-pickup>Konfirmasi &amp; Bayar, Ambil di Toko</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </aside>
    </div>
</form>
@endsection

@push('scripts')
<script>
    (() => {
        const form = document.getElementById('checkoutForm');
        const rupiah = n => 'Rp' + n.toLocaleString('id-ID');
        const total = document.getElementById('nilaiTotal');
        const subtotal = Number(total.dataset.subtotal);

        const update = () => {
            const pickup = form.metode_pengiriman.value === 'pickup';

            // Tampilkan bagian sesuai mode. Input di bagian yang tersembunyi dimatikan
            // supaya "required"-nya tidak menahan form dan nilainya tidak ikut terkirim.
            form.querySelectorAll('[data-kurir]').forEach(el => {
                el.hidden = pickup;
                el.querySelectorAll('input, textarea, select').forEach(i => i.disabled = pickup);
            });
            form.querySelectorAll('[data-pickup]').forEach(el => el.hidden = !pickup);

            document.getElementById('judulPickup').hidden = !pickup;
            document.getElementById('introPickup').hidden = !pickup;
            document.getElementById('introKurir').hidden = pickup;

            // Hitung ulang ringkasan (hanya tampilan; harga final tetap dihitung server).
            const kurir = form.querySelector('input[name="layanan_kurir"]:checked');
            const ongkir = pickup || !kurir ? 0 : Number(kurir.dataset.ongkir);
            document.getElementById('labelOngkir').textContent =
                kurir ? 'Ongkos Kurir (' + (kurir.value === 'motor' ? 'Motor' : 'Mobil') + ')' : 'Ongkos Kurir';
            document.getElementById('nilaiOngkir').textContent = rupiah(ongkir);
            total.textContent = rupiah(subtotal + ongkir);
        };

        form.addEventListener('change', update);
        update();
    })();
</script>
@endpush