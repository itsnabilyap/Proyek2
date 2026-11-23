@extends('layouts.app')

@section('title', $produk->nama_produk)

@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $ukuran = $produk->ukuran;
    $pilih = $ukuran->firstWhere('id', (int) old('ukuran_produk_id')) ?? $ukuran->first();
    $habis = $produk->stok < 1;
    $uang = $produk->adalahBucketUang();
    $varianAwal = old('varian', $produk->varian_pilihan[0] ?? null);
    $tawaran = $produk->tawaranCustom();
    $no = 0;   // nomor langkah: 1. 2. 3. ...

    // Ringkasan di samping harga: "Medium · 6 tangkai", "2 coklat · Dairy Milk", "Kipas · 20 lembar"
    $namaModel = trim(str_replace('Bucket Uang', '', $produk->nama_produk));
    $ringkas = fn ($u) => collect([
        $uang ? $namaModel : null,
        $u->nama_ukuran,
        ! $uang && mb_strlen((string) $u->keterangan) <= 14 ? $u->keterangan : null,   // "6 tangkai" ya, kalimat panjang tidak
    ])->filter()->implode(' · ');
    $tambahVarian = fn ($teks) => $teks . (! $uang && $varianAwal ? ' · ' . $varianAwal : '');

    $judul = 'mb-2.5 text-[15px] font-bold';
    $kotak = 'flex h-full min-h-11 flex-col items-start rounded-[14px] border border-line bg-white px-3.5 py-3 text-left transition-colors hover:border-maroon peer-checked:border-2 peer-checked:border-maroon peer-checked:bg-[#FFF7F8] peer-checked:px-[13px] peer-checked:py-[11px] peer-focus-visible:ring-4 peer-focus-visible:ring-maroon/20';
    $isian = 'h-11 rounded-xl border border-line bg-white px-3 text-sm font-medium focus:border-maroon focus:ring-4 focus:ring-maroon/15 focus:outline-none';
@endphp

@section('content')
<div class="wrap pt-7 pb-20">
    <nav class="mb-5 text-[13px] text-muted" aria-label="Breadcrumb">
        <a href="{{ route('katalog.index') }}" class="text-maroon hover:underline">Katalog</a> / <span aria-current="page">{{ $produk->nama_produk }}</span>
    </nav>

    <div class="flex flex-wrap items-start gap-10">
        {{-- ===== FOTO ===== --}}
        <div class="flex min-w-0 flex-[1_1_380px] flex-col gap-3">
            <div class="relative aspect-[4/5] overflow-hidden rounded-[20px] bg-blush">
                @if ($produk->gambar_url)
                    <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama_produk }}" class="block size-full object-cover">
                @else
                    <div class="flex size-full items-center justify-center text-[56px] text-rose"><i class="bi bi-flower1"></i></div>
                @endif
                @if ($produk->ukuran_foto)
                    <span class="absolute top-4 left-4 rounded-full bg-white/90 px-3.5 py-1.5 text-[13px] font-semibold text-wine">Ukuran di foto: {{ $produk->ukuran_foto }}</span>
                @endif
            </div>
            @if ($produk->catatanFoto())
                <p class="rounded-xl border border-line-soft bg-white px-3.5 py-3 text-[12.5px] leading-relaxed text-ink-soft">{{ $produk->catatanFoto() }}</p>
            @endif
        </div>

        {{-- ===== INFO & FORM ===== --}}
        <form method="POST" action="{{ route('keranjang.store') }}" id="formPesan" class="min-w-0 flex-[1_1_440px]"
              @if ($uang) data-uang @endif>
            @csrf

            <span class="inline-block rounded-full bg-blush px-2.5 py-1 text-[11px] font-bold text-maroon uppercase">{{ $produk->badgeDetail() }}</span>
            <h1 class="mt-3 mb-1.5 font-serif text-[34px] leading-tight font-medium lg:text-[38px]">{{ $produk->nama_produk }}</h1>
            <p class="mb-5 text-sm leading-relaxed text-ink-soft">
                @if ($uang)
                    Harga di bawah adalah <b>bahan + jasa merangkai</b>. Uangnya dari kamu sendiri, diserahkan ke atelier sebelum dirangkai.
                @else
                    {{ $produk->deskripsi }}
                @endif
            </p>

            <div class="mb-5.5 flex flex-wrap items-baseline gap-x-2.5">
                <strong id="hargaBesar" class="text-[30px] font-extrabold text-maroon">{{ $rupiah($pilih->harga) }}</strong>
                <span id="ringkasPilihan" class="text-[13px] text-muted">{{ $tambahVarian($ringkas($pilih)) }}</span>
            </div>

            {{-- Ukuran / jumlah lembar / jumlah coklat / paket --}}
            <fieldset class="mb-5.5">
                <legend class="{{ $judul }}">{{ ++$no }}. {{ $produk->judulUkuran() }}</legend>
                <div @class([
                    'grid gap-2.5',
                    'grid-cols-2 sm:grid-cols-[repeat(auto-fill,minmax(112px,1fr))]' => $uang,
                    'grid-cols-3' => ! $uang && $ukuran->count() <= 3 || $produk->adalahThumbelina(),
                    'grid-cols-2 sm:grid-cols-[repeat(auto-fill,minmax(150px,1fr))]' => ! $uang && $ukuran->count() > 3 && ! $produk->adalahThumbelina(),
                ])>
                    @foreach ($ukuran as $u)
                        <label class="relative block">
                            <input type="radio" name="ukuran_produk_id" value="{{ $u->id }}"
                                   data-nama="{{ $u->nama_ukuran }}" data-harga="{{ $u->harga }}" data-ringkas="{{ $ringkas($u) }}"
                                   @checked($u->id === $pilih->id) required class="peer sr-only">
                            <span class="{{ $kotak }}">
                                <strong class="text-sm">{{ $u->nama_ukuran }}</strong>
                                @if ($u->keterangan && ! $uang) <span class="mt-0.5 text-xs text-muted">{{ $u->keterangan }}</span> @endif
                                @if ($u->label) <span class="text-[11px] font-semibold text-wine">{{ $u->label }}</span> @endif
                                <span class="mt-1.5 text-sm font-extrabold text-maroon">{{ $rupiah($u->harga) }}</span>
                            </span>
                        </label>
                    @endforeach

                    {{-- Bucket Snack: "Harga lain" (ketik sendiri, minimal harga_bebas_min) --}}
                    @if ($produk->harga_bebas_min)
                        @php $lain = (bool) old('pakai_harga_lain'); @endphp
                        <label class="relative block">
                            <input type="radio" name="ukuran_produk_id" value="{{ $ukuran->first()->id }}" data-lain
                                   data-nama="Harga pilihan" data-harga="{{ old('harga_lain', $produk->harga_bebas_min) }}" data-ringkas="Harga pilihan"
                                   @checked($lain) class="peer sr-only">
                            <span class="{{ $kotak }}">
                                <strong class="text-sm">Harga lain</strong>
                                <span class="mt-0.5 text-xs text-muted">min. {{ $rupiah($produk->harga_bebas_min) }}</span>
                                <span class="mt-1.5 text-sm font-extrabold text-maroon">Ketik sendiri</span>
                            </span>
                        </label>
                    @endif
                </div>
                @if ($produk->harga_bebas_min)
                    <input type="hidden" name="pakai_harga_lain" value="{{ old('pakai_harga_lain') }}" data-pakai-lain>
                    <label data-kolom-lain class="mt-2.5 flex flex-col gap-1.5 text-[13px] font-semibold" @unless (old('pakai_harga_lain')) hidden @endunless>
                        Harga yang kamu inginkan (Rp)
                        <input type="number" name="harga_lain" min="{{ $produk->harga_bebas_min }}" step="1000" value="{{ old('harga_lain', $produk->harga_bebas_min) }}"
                               class="{{ $isian }} max-w-60" data-harga-lain>
                    </label>
                    @error('harga_lain') <span class="field-error">{{ $message }}</span> @enderror
                @endif
                @error('ukuran_produk_id') <span class="field-error">{{ $message }}</span> @enderror
                @if ($tawaran)
                    <p class="mt-2.5 text-[12.5px] text-muted">{{ $tawaran[0] }}
                        <a href="{{ route('custom.create', ['jenis' => $tawaran[1]]) }}" class="font-bold text-maroon hover:underline">Request Custom</a></p>
                @endif
            </fieldset>

            {{-- Varian: merek coklat / pecahan uang --}}
            @if ($produk->punyaVarian())
                <fieldset class="mb-5.5">
                    <legend class="{{ $judul }}">{{ ++$no }}. {{ $produk->varian_judul }}</legend>
                    <div @class(['gap-2.5', 'grid grid-cols-2' => count($produk->varian_pilihan) === 2, 'flex flex-wrap' => count($produk->varian_pilihan) !== 2])>
                        @foreach ($produk->varian_pilihan as $v)
                            <label class="relative block">
                                <input type="radio" name="varian" value="{{ $v }}" @checked($varianAwal === $v) required class="peer sr-only">
                                <span class="{{ $kotak }} justify-center text-sm font-semibold">{{ $v }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('varian') <span class="field-error">{{ $message }}</span> @enderror
                </fieldset>
            @endif

            {{-- Bucket uang: yang dibayar sekarang vs uang yang disiapkan --}}
            @if ($uang)
                <section class="mb-5.5 rounded-2xl border border-line bg-white px-5 py-4.5 text-sm">
                    <div class="flex justify-between gap-3 py-1.5"><span class="text-muted">Uang yang dirangkai</span><span class="font-semibold"><span data-u-nilai></span> <span class="font-normal text-muted">(dari kamu)</span></span></div>
                    <div class="flex justify-between gap-3 py-1.5"><span class="text-muted">{{ $namaModel }} · <span data-u-lembar></span></span><span class="font-semibold" data-u-jasa></span></div>
                    <div class="mt-1.5 flex justify-between gap-3 border-t border-line-soft pt-3 text-base"><strong>Dibayar sekarang</strong><strong class="text-maroon" data-u-jasa></strong></div>
                    <p class="mt-2.5 text-xs leading-relaxed text-muted">Nilai buket jadi: <span data-u-total></span>. Uang <span data-u-nilai></span> kamu serahkan langsung di atelier paling lambat H-1 sebelum jadwal.</p>
                </section>
            @endif

            {{-- Warna & kartu ucapan --}}
            <h2 class="{{ $judul }}">{{ ++$no }}. {{ $produk->pakaiWarna() ? 'Warna & kartu ucapan' : 'Kartu ucapan' }}</h2>
            @if ($produk->pakaiWarna())
                <div class="mb-3.5 grid grid-cols-2 gap-3">
                    <label class="flex flex-col gap-1.5 text-[13px] font-semibold">Warna bunga
                        <select name="warna_bunga" class="{{ $isian }}">
                            @foreach (\App\Models\Produk::WARNA_BUNGA as $w) <option @selected(old('warna_bunga') === $w)>{{ $w }}</option> @endforeach
                        </select>
                    </label>
                    <label class="flex flex-col gap-1.5 text-[13px] font-semibold">Warna kertas
                        <select name="warna_kertas" class="{{ $isian }}">
                            @foreach (\App\Models\Produk::WARNA_KERTAS as $w) <option @selected(old('warna_kertas') === $w)>{{ $w }}</option> @endforeach
                        </select>
                    </label>
                </div>
            @endif
            <label class="mb-5.5 flex flex-col gap-1.5 text-[13px] font-semibold">
                <span>{{ $produk->pakaiWarna() ? 'Kartu ucapan' : 'Isi kartu' }} <span class="font-normal text-muted">(opsional)</span></span>
                <textarea name="kartu_ucapan" maxlength="300" rows="2" class="field bg-white font-normal"
                          placeholder="Contoh: Selamat wisuda, Dinda! Bangga sama kamu.">{{ old('kartu_ucapan') }}</textarea>
                @error('kartu_ucapan') <span class="field-error">{{ $message }}</span> @enderror
            </label>

            {{-- Jumlah + tombol --}}
            @if ($habis)
                <button type="button" class="btn btn-lg btn-primary w-full rounded-full" disabled>Stok Habis</button>
            @else
                @auth
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center rounded-full border border-line bg-white">
                            <button type="button" data-jumlah="-1" aria-label="Kurangi" class="size-11 text-lg text-wine">−</button>
                            <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', 1) }}" min="1" max="20" aria-label="Jumlah buket"
                                   class="w-8 [appearance:textfield] bg-transparent text-center font-bold outline-none [&::-webkit-inner-spin-button]:appearance-none">
                            <button type="button" data-jumlah="1" aria-label="Tambah" class="size-11 text-lg text-wine">+</button>
                        </div>
                        <button type="submit" class="btn btn-primary h-12 min-w-0 flex-1 rounded-full text-sm font-bold whitespace-nowrap sm:text-[15px]">
                            Tambah ke Keranjang · <span id="totalHarga">{{ $rupiah($pilih->harga) }}</span>
                        </button>
                    </div>
                    @error('jumlah') <span class="field-error">{{ $message }}</span> @enderror
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary h-12 w-full rounded-full text-[15px] font-bold">Masuk untuk Memesan</a>
                @endauth
            @endif
        </form>
    </div>

    {{-- ===== BUKET LAINNYA ===== --}}
    @if ($lainnya->isNotEmpty())
        <section class="mt-16">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="mt-1 mb-5.5 font-serif text-[34px] font-medium">Buket Lainnya</h2>
                <a href="{{ route('katalog.index') }}" class="text-sm font-semibold text-maroon hover:underline">Lihat semua katalog</a>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($lainnya as $p)
                    <a href="{{ route('katalog.show', $p) }}" class="overflow-hidden rounded-[18px] border border-line-soft bg-white transition hover:-translate-y-[3px] hover:shadow-lift">
                        @if ($p->gambar_url)
                            <img src="{{ $p->gambar_url }}" alt="" loading="lazy" class="block aspect-[4/3] w-full object-cover">
                        @else
                            <div class="flex aspect-[4/3] w-full items-center justify-center bg-blush text-[32px] text-rose"><i class="bi bi-flower1"></i></div>
                        @endif
                        <div class="px-4.5 py-4">
                            <strong class="block font-serif text-xl font-medium">{{ $p->nama_produk }}</strong>
                            <small class="text-[13px] text-muted">Mulai dari <b class="text-maroon">{{ $rupiah($p->harga_mulai) }}</b></small>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>

<script>
    (() => {
        const form = document.getElementById('formPesan');
        const rupiah = n => 'Rp' + Number(n).toLocaleString('id-ID');
        const jumlah = document.getElementById('jumlah');

        // Bucket uang: nilai uang = lembar × pecahan ("20 lembar" × "50rb")
        const angka = t => { const m = String(t).match(/([\d.,]+)\s*(rb|k|jt)?/i); if (!m) return 0; const n = parseFloat(m[1].replace(/\./g, '').replace(',', '.')); return n * ({ rb: 1e3, k: 1e3, jt: 1e6 }[(m[2] || '').toLowerCase()] || 1); };
        const isi = (attr, teks) => form.querySelectorAll('[' + attr + ']').forEach(el => el.textContent = teks);

        const hitung = () => {
            const r = form.querySelector('input[name="ukuran_produk_id"]:checked');
            if (!r) return;

            // "Harga lain": tampilkan kolom harga & pakai angka ketikan
            const pakaiLain = form.querySelector('[data-pakai-lain]');
            if (pakaiLain) {
                const lain = r.hasAttribute('data-lain');
                pakaiLain.value = lain ? '1' : '';
                form.querySelector('[data-kolom-lain]').hidden = !lain;
                if (lain) r.dataset.harga = Math.max(0, parseInt(form.querySelector('[data-harga-lain]').value) || 0);
            }
            const v = form.querySelector('input[name="varian"]:checked');
            const n = Math.min(20, Math.max(1, parseInt(jumlah?.value) || 1));

            document.getElementById('hargaBesar').textContent = rupiah(r.dataset.harga);
            const total = document.getElementById('totalHarga');
            if (total) total.textContent = rupiah(r.dataset.harga * n);

            // Ringkasan di samping harga (varian ikut ditulis, kecuali pecahan bucket uang)
            document.getElementById('ringkasPilihan').textContent =
                r.dataset.ringkas + (v && !form.hasAttribute('data-uang') ? ' · ' + v.value : '');


            if (!form.hasAttribute('data-uang')) return;
            const nilai = angka(r.dataset.nama) * (v ? angka(v.value) : 0);
            isi('data-u-lembar', r.dataset.nama);
            isi('data-u-jasa', rupiah(r.dataset.harga));
            isi('data-u-nilai', rupiah(nilai));
            isi('data-u-total', rupiah(nilai + Number(r.dataset.harga)));
        };

        // Tombol − / + jumlah buket
        form.querySelectorAll('[data-jumlah]').forEach(b => b.addEventListener('click', () => {
            jumlah.value = Math.min(20, Math.max(1, (parseInt(jumlah.value) || 1) + Number(b.dataset.jumlah)));
            hitung();
        }));

        form.addEventListener('input', hitung);
        form.addEventListener('change', hitung);
        hitung();
    })();
</script>
@endsection
