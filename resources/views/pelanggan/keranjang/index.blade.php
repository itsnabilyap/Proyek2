@extends('layouts.app')

@section('title', 'Keranjang')

@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $kartu = 'rounded-[20px] border border-line-soft bg-white p-5 shadow-[0_16px_36px_-30px_rgb(94_38_51/0.35)]';
@endphp

@section('content')
<section class="wrap pt-11 pb-20">
    <div class="mb-6 flex items-end justify-between gap-4">
        <div>
            <p class="mb-2 text-[11px] font-semibold tracking-[.12em] text-maroon">KERANJANG</p>
            <h1 class="mt-1 font-serif text-[32px] font-medium sm:text-[42px]">Keranjang <em class="text-maroon">Belanja</em></h1>
        </div>
        <a href="{{ route('katalog.index') }}" class="text-sm font-semibold text-maroon hover:underline"><i class="bi bi-chevron-left"></i> Lanjut Belanja</a>
    </div>

    @if ($items->isEmpty())
        <div class="flex flex-col items-center gap-2.5 rounded-[20px] border border-dashed border-line bg-white px-6 py-16 text-center">
            <span class="flex size-21 items-center justify-center rounded-full bg-blush text-3xl text-maroon"><i class="bi bi-cart3"></i></span>
            <h2 class="mt-2 text-xl font-bold">Keranjang masih kosong</h2>
            <p class="mb-2 text-muted">Yuk pilih buket favoritmu dulu.</p>
            <a href="{{ route('katalog.index') }}" class="btn btn-primary min-h-10 px-5.5 text-[13px]">Lihat Katalog</a>
        </div>
    @else
        <div class="grid items-start gap-7 md:grid-cols-[1fr_320px]">
            {{-- ========== DAFTAR ITEM ========== --}}
            <div class="flex flex-col gap-3.5">
                <label class="{{ $kartu }} flex items-center justify-between">
                    <span class="flex items-center gap-3 text-sm font-semibold">
                        <input type="checkbox" id="pilihSemua" checked class="size-5 accent-maroon">
                        <span>Pilih semua</span>
                    </span>
                    <small id="jumlahDipilih" class="text-[13px] text-muted">{{ $items->count() }} dipilih</small>
                </label>

                @foreach ($items as $item)
                    @php $produk = $item->ukuran->produk; $habis = $produk->stok < 1; @endphp
                    <article class="{{ $kartu }} grid grid-cols-[20px_72px_1fr] items-start gap-x-4 gap-y-3 sm:grid-cols-[20px_104px_1fr_auto] sm:gap-5">
                        {{-- Checkbox ikut form checkout di bawah (atribut form="formCheckout") --}}
                        {{-- Stok habis: tidak bisa dipilih untuk checkout --}}
                        @if ($habis)
                            <input type="checkbox" disabled aria-label="{{ $produk->nama_produk }} stok habis" class="size-5 cursor-not-allowed">
                        @else
                            <input type="checkbox" name="item[]" value="{{ $item->id }}" form="formCheckout" checked data-pilih
                                   data-subtotal="{{ $item->subtotal }}" data-jumlah="{{ $item->jumlah }}"
                                   aria-label="Pilih {{ $produk->nama_produk }}" class="size-5 accent-maroon">
                        @endif

                        @if ($produk->gambar_url)
                            <img src="{{ $produk->gambar_url }}" alt="" class="h-22.5 w-18 rounded-xl object-cover sm:h-32 sm:w-26">
                        @else
                            <span class="flex h-22.5 w-18 items-center justify-center rounded-xl bg-blush text-[22px] text-rose sm:h-32 sm:w-26"><i class="bi bi-flower1"></i></span>
                        @endif

                        <div class="flex min-w-0 flex-col gap-2">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="w-fit rounded-full bg-blush px-2.5 py-0.5 text-[11px] font-semibold text-wine">{{ $produk->kategori->nama_kategori }}</span>
                                @if ($habis)
                                    <span class="w-fit rounded-full bg-danger-soft px-2.5 py-0.5 text-[11px] font-bold text-danger">Stok habis</span>
                                @endif
                            </div>
                            <h2 class="font-serif text-[22px] font-medium">{{ $produk->nama_produk }}</h2>

                            <form method="POST" action="{{ route('keranjang.update', $item) }}" class="flex min-w-0 flex-col items-start gap-1.5 text-[13px] text-muted sm:flex-row sm:items-center sm:gap-2.5">
                                @csrf @method('PATCH')
                                <label for="ukuran-{{ $item->id }}">Ukuran</label>
                                <select id="ukuran-{{ $item->id }}" name="ukuran_produk_id" onchange="this.form.submit()"
                                        class="h-10 w-full min-w-0 truncate rounded-[10px] border border-line bg-white px-3 text-[13px] font-semibold text-ink focus:border-maroon sm:w-auto focus:ring-4 focus:ring-maroon/15 focus:outline-none">
                                    @if ($item->harga_bebas)
                                        <option selected>Harga pilihan · {{ $rupiah($item->harga_bebas) }}</option>
                                    @endif
                                    @foreach ($produk->ukuran as $u)
                                        <option value="{{ $u->id }}" @selected(! $item->harga_bebas && $u->id === $item->ukuran_produk_id)>
                                            {{ $u->nama_ukuran }} · {{ $rupiah($u->harga) }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>

                            @if ($item->varian)
                                <p class="text-[13px] text-muted">{{ $produk->varian_judul ?: 'Varian' }}: <b class="text-ink">{{ $item->varian }}</b></p>
                            @endif
                            @if ($item->warna)
                                <p class="text-[13px] text-muted">Warna: <b class="text-ink">{{ $item->warna }}</b></p>
                            @endif
                            <p class="max-w-[300px] text-[13px] text-muted">Kartu ucapan:
                                @if ($item->kartu_ucapan) <em class="text-ink">{{ $item->kartu_ucapan }}</em> @else - @endif
                            </p>
                        </div>

                        <div class="col-[1/-1] flex items-center justify-between gap-4 border-t border-line-soft pt-3 sm:col-auto sm:border-0 sm:pt-0 sm:h-full sm:flex-col sm:items-end">
                            <div class="sm:text-right">
                                <strong class="block text-xl text-maroon">{{ $rupiah($item->subtotal) }}</strong>
                                <small class="text-xs text-muted">{{ $rupiah($item->harga_satuan) }} / buket</small>
                            </div>

                            <div class="flex items-center gap-3.5">
                                <form method="POST" action="{{ route('keranjang.destroy', $item) }}"
                                      data-konfirmasi="Hapus dari keranjang?" data-nama="{{ $produk->nama_produk }}" data-pesan="Buket ini akan dikeluarkan dari keranjangmu." data-tombol="Ya, hapus">
                                    @csrf @method('DELETE')
                                    <button type="submit" aria-label="Hapus {{ $produk->nama_produk }}"
                                            class="size-9 rounded-[10px] text-base text-danger transition-colors hover:bg-danger-soft">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('keranjang.update', $item) }}" class="flex h-11 items-center rounded-full border border-line">
                                    @csrf @method('PATCH')
                                    <button type="submit" name="aksi" value="kurang" aria-label="Kurangi" @disabled($item->jumlah <= 1)
                                            class="h-full w-10.5 rounded-full text-base text-maroon hover:enabled:bg-blush disabled:cursor-not-allowed disabled:text-line"><i class="bi bi-dash"></i></button>
                                    <span aria-live="polite" class="min-w-8.5 text-center font-bold">{{ $item->jumlah }}</span>
                                    <button type="submit" name="aksi" value="tambah" aria-label="Tambah" @disabled($item->jumlah >= $produk->stok)
                                            class="h-full w-10.5 rounded-full text-base text-maroon hover:enabled:bg-blush disabled:cursor-not-allowed disabled:text-line"><i class="bi bi-plus"></i></button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- ========== RINGKASAN ========== --}}
            <aside class="{{ $kartu }} flex flex-col gap-4.5 p-6 md:sticky md:top-24">
                <h2 class="font-serif text-[26px] font-medium">Ringkasan Belanja</h2>

                <dl class="flex flex-col gap-3 border-b border-line-soft pb-3.5 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-muted">Total buket</dt><dd id="sumBuket" class="font-semibold">0 buket</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-muted">Subtotal</dt><dd id="sumSubtotal" class="font-semibold">Rp0</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-muted">Kartu ucapan</dt><dd class="font-semibold text-ok">Gratis</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-muted">Ongkir</dt><dd class="text-[13px] text-muted">Dihitung saat checkout</dd></div>
                </dl>

                <div class="flex items-baseline justify-between gap-3">
                    <span class="text-base font-semibold">Total</span>
                    <strong id="sumTotal" class="text-[28px] font-bold text-maroon">Rp0</strong>
                </div>

                <form method="GET" action="{{ route('checkout.index') }}" id="formCheckout">
                    <button type="submit" id="btnCheckout" class="btn btn-lg btn-primary w-full shadow-btn">Checkout Sekarang</button>
                </form>

                <div class="rounded-[14px] bg-blush-50 px-4 py-3.5 text-[13px]">
                    <strong class="mb-2 block">Di langkah berikutnya Anda memilih:</strong>
                    <p class="mt-1.5 flex gap-2.5 text-ink-soft"><i class="bi bi-truck text-maroon"></i> Ambil di Toko atau Kurir</p>
                    <p class="mt-1.5 flex gap-2.5 text-ink-soft"><i class="bi bi-qr-code text-maroon"></i> Pembayaran QRIS atau Virtual Account</p>
                </div>
            </aside>
        </div>
    @endif
</section>

@if ($items->isNotEmpty())
<script>
    (() => {
        const semua = document.getElementById('pilihSemua');
        const items = [...document.querySelectorAll('[data-pilih]')];
        if (!items.length) { semua.checked = false; semua.disabled = true; document.getElementById('btnCheckout').disabled = true; }
        const rupiah = n => 'Rp' + n.toLocaleString('id-ID');

        const update = () => {
            const dipilih = items.filter(i => i.checked);
            const subtotal = dipilih.reduce((t, i) => t + Number(i.dataset.subtotal), 0);
            const buket = dipilih.reduce((t, i) => t + Number(i.dataset.jumlah), 0);

            document.getElementById('jumlahDipilih').textContent = dipilih.length + ' dipilih';
            document.getElementById('sumBuket').textContent = buket + ' buket';
            document.getElementById('sumSubtotal').textContent = rupiah(subtotal);
            document.getElementById('sumTotal').textContent = rupiah(subtotal);
            document.getElementById('btnCheckout').disabled = dipilih.length === 0;

            semua.checked = items.length > 0 && dipilih.length === items.length;
            semua.indeterminate = dipilih.length > 0 && dipilih.length < items.length;
        };

        semua.addEventListener('change', () => {
            items.forEach(i => i.checked = semua.checked);
            update();
        });
        items.forEach(i => i.addEventListener('change', update));
        update();
    })();
</script>
@endif
@endsection