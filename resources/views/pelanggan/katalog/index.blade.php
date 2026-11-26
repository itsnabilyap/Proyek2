@extends('layouts.app')

@section('title', 'Katalog')

@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    // Deskripsi di kartu: maks. 100 huruf (termasuk spasi), dipotong di akhir kata.
    $ringkas = fn ($teks) => mb_strlen((string) $teks) <= 100 ? $teks
        : rtrim(\Illuminate\Support\Str::limit((string) $teks, 100, '', preserveWords: true), ' .,;:-') . '…';
    // Link filter: hanya tulis "urut" ke URL kalau bukan default.
    $link = fn ($kat) => route('katalog.index', array_filter([
        'kategori' => $kat,
        'urut' => $urut !== 'termurah' ? $urut : null,
    ]));
    $kartu = 'group flex h-full flex-col overflow-hidden rounded-2xl border border-line-soft bg-white shadow-card transition duration-300 hover:-translate-y-[3px] hover:shadow-lift';
    $pilKategori = 'absolute top-2 left-2 z-10 rounded-full bg-white/90 px-2 py-0.5 text-[10px] sm:top-3 sm:left-3 sm:px-2.5 sm:py-1 sm:text-[11px] font-semibold text-wine shadow-xs backdrop-blur-xs';
    $chip = 'inline-flex min-h-[38px] items-center justify-center rounded-full border border-line bg-white px-4 text-[13px] font-semibold transition-colors hover:border-maroon hover:bg-blush-50 aria-[current=page]:border-maroon aria-[current=page]:bg-maroon aria-[current=page]:text-white';
@endphp

@section('content')
<section class="bg-sorot px-4 pt-14 pb-9 text-center sm:px-6">
    <span class="inline-flex items-center gap-1.5 rounded-full bg-blush px-3 py-1 text-[11px] font-bold tracking-[.12em] text-maroon">KATALOG</span>
    <h1 class="mt-4.5 mb-3.5 font-serif text-[34px] leading-tight font-medium md:text-[46px]">Koleksi Buket <em class="text-maroon">@bucket.imy</em></h1>
    <p class="mx-auto max-w-[600px] text-[15px] leading-relaxed text-ink-soft">
        Harga di kartu adalah harga mulai. Pilih ukuran, model, dan isian di halaman produk.
        Semua bunga yang kami pakai adalah bunga artificial.
    </p>
</section>

<div class="wrap pb-20">
    {{-- Filter kategori + urutan --}}
    <div class="mb-8 flex flex-col gap-3 rounded-2xl border border-line-soft bg-white px-3 py-2.5 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
        {{-- HP: 3 tombol sejajar per baris. Tablet ke atas: berderet biasa --}}
        <nav class="grid grid-cols-3 gap-2 sm:flex sm:flex-wrap" aria-label="Filter kategori">
            <a href="{{ $link(null) }}" class="{{ $chip }}" @if (! $kategoriAktif) aria-current="page" @endif>Semua</a>
            @foreach ($kategori as $kat)
                <a href="{{ $link($kat->slug) }}" class="{{ $chip }}" @if ($kategoriAktif === $kat->slug) aria-current="page" @endif>{{ \Illuminate\Support\Str::after($kat->nama_kategori, 'Buket ') }}</a>
            @endforeach
        </nav>

        <form method="GET" action="{{ route('katalog.index') }}" class="flex items-center gap-2.5">
            @if ($kategoriAktif)
                <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">
            @endif
            <label for="urut" class="text-[13px] text-muted">Urutkan</label>
            <select id="urut" name="urut" onchange="this.form.submit()"
                    class="h-[38px] rounded-[10px] border border-line bg-white px-3 text-[13px] font-semibold focus:border-maroon focus:ring-4 focus:ring-maroon/15 focus:outline-none">
                <option value="termurah" @selected($urut === 'termurah')>Harga termurah</option>
                <option value="termahal" @selected($urut === 'termahal')>Harga termahal</option>
                <option value="terbaru" @selected($urut === 'terbaru')>Terbaru</option>
            </select>
            <noscript><button type="submit" class="{{ $chip }}">Terapkan</button></noscript>
        </form>
    </div>

    {{-- ===== DAFTAR PRODUK (harga pasti) ===== --}}
    {{-- HP: 2 kartu sejajar --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($produk as $p)
            <article class="{{ $kartu }}">
                {{-- Wadah Gambar: Dikunci rasio 1:1 + overflow-hidden agar semua kartu sama tinggi --}}
                @php $foto = $p->semua_gambar; @endphp
                <button type="button" class="relative block aspect-square w-full shrink-0 overflow-hidden bg-blush outline-none focus-visible:ring-4 focus-visible:ring-inset focus-visible:ring-maroon/30"
                        @if ($foto) data-galeri='@json($foto)' data-judul="{{ $p->nama_produk }}" aria-label="Lihat foto {{ $p->nama_produk }}" @else disabled @endif>
                    @if ($foto)
                        <img src="{{ $foto[0] }}"
                             alt="{{ $p->nama_produk }}"
                             loading="lazy"
                             @class(['block h-full w-full object-cover object-center transition-transform duration-300 group-hover:scale-105', 'opacity-55 grayscale' => $p->stok < 1])>
                    @else
                        <div class="flex h-full w-full items-center justify-center text-[36px] text-rose">
                            <i class="bi bi-flower1"></i>
                        </div>
                    @endif
                    <span class="{{ $pilKategori }}">{{ \Illuminate\Support\Str::after($p->kategori->nama_kategori, 'Buket ') }}</span>
                    {{-- Stok habis: label di tengah foto --}}
                    @if ($p->stok < 1)
                        <span class="absolute inset-x-0 bottom-2 z-10 mx-auto w-fit rounded-full bg-ink/80 px-3 py-1 text-[11px] font-bold tracking-wide text-white uppercase sm:bottom-3 sm:text-xs">Stok Habis</span>
                    @endif
                    @if (count($foto) > 1)
                        <span class="absolute top-2 right-2 z-10 inline-flex items-center gap-1 rounded-full bg-black/50 px-2 py-0.5 text-[10px] text-white sm:top-3 sm:right-3 sm:text-[11px]">
                            <i class="bi bi-images"></i> {{ count($foto) }}
                        </span>
                    @endif
                </button>

                {{-- Konten Kartu --}}
                <div class="flex flex-1 flex-col px-3 pt-3 pb-3.5 sm:px-4 sm:pt-4 sm:pb-4.5">
                    <h2 class="mb-1 line-clamp-2 font-serif text-[15px] leading-snug font-medium sm:text-lg">{{ $p->nama_produk }}</h2>
                    <p class="mb-3 flex-1 text-[11.5px] leading-relaxed text-muted sm:mb-3.5 sm:text-[12.5px]">{{ $ringkas($p->deskripsi) }}</p>

                    <div class="mt-auto">
                        <small class="block text-[11px] text-muted">{{ $p->labelHarga() }}</small>
                        <strong class="block text-[15px] font-extrabold text-maroon sm:text-lg">{{ $rupiah($p->harga_mulai) }}</strong>
                        <a href="{{ route('katalog.show', $p) }}" class="btn btn-primary mt-2.5 min-h-9 w-full rounded-full text-xs font-bold sm:mt-3 sm:min-h-10 sm:text-[13px]">{{ $p->stok < 1 ? 'Lihat Detail' : 'Lihat Pilihan' }}</a>
                    </div>
                </div>
            </article>
        @endforeach

        @if ($produk->isEmpty())
            <p class="col-span-full py-12 text-center text-muted">Belum ada produk di kategori ini.</p>
        @endif
    </div>

    {{-- ===== AJAKAN CUSTOM ===== --}}
    <div class="mt-12 flex flex-col gap-5 rounded-[18px] bg-blush-100 px-5.5 py-7 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:px-10 sm:py-9">
        <div>
            <p class="mb-2 text-[11px] font-bold tracking-[.12em] text-maroon">PUNYA BUDGET SENDIRI?</p>
            <h2 class="mb-2.5 font-serif text-[26px] font-medium">Bunga besar, uang 50+ lembar, atau coklat request</h2>
            <p class="max-w-[480px] text-sm leading-relaxed text-ink-soft">Ceritakan budget dan tema kamu. Kami balas via WhatsApp dengan harga final sebelum kamu bayar.</p>
        </div>
        <a href="{{ route('custom.create') }}" class="btn btn-primary min-h-11 self-start rounded-full px-6 text-sm font-bold sm:self-auto">Request Custom</a>
    </div>
</div>
@endsection