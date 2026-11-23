@extends('layouts.app')

@section('title', 'Katalog')

@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    // Link filter: hanya tulis "urut" ke URL kalau bukan default.
    $link = fn ($kat) => route('katalog.index', array_filter([
        'kategori' => $kat,
        'urut' => $urut !== 'termurah' ? $urut : null,
    ]));
    $kartu = 'group flex h-full flex-col overflow-hidden rounded-2xl border border-line-soft bg-white shadow-card transition duration-300 hover:-translate-y-[3px] hover:shadow-lift';
    $pilKategori = 'absolute top-3 left-3 z-10 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-wine shadow-xs backdrop-blur-xs';
    $chip = 'inline-flex min-h-[38px] items-center rounded-full border border-line bg-white px-4 text-[13px] font-semibold transition-colors hover:border-maroon hover:bg-blush-50 aria-[current=page]:border-maroon aria-[current=page]:bg-maroon aria-[current=page]:text-white';
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
        <nav class="flex flex-wrap gap-2" aria-label="Filter kategori">
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
    <div class="grid gap-6 min-[381px]:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($produk as $p)
            <article class="{{ $kartu }}">
                {{-- Wadah Gambar: Dikunci rasio 4:5 + overflow-hidden agar semua kartu sama tinggi --}}
                <div class="relative aspect-[4/5] w-full shrink-0 overflow-hidden bg-blush">
                    @if ($p->gambar_url)
                        <img src="{{ $p->gambar_url }}"
                             alt="{{ $p->nama_produk }}"
                             loading="lazy"
                             class="block h-full w-full object-cover object-center transition-transform duration-300 group-hover:scale-105">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-[44px] text-rose">
                            <i class="bi bi-flower1"></i>
                        </div>
                    @endif
                    <span class="{{ $pilKategori }}">{{ \Illuminate\Support\Str::after($p->kategori->nama_kategori, 'Buket ') }}</span>
                </div>

                {{-- Konten Kartu --}}
                <div class="flex flex-1 flex-col px-4.5 pt-4.5 pb-5">
                    <h2 class="mb-1.5 line-clamp-2 font-serif text-xl leading-snug font-medium">{{ $p->nama_produk }}</h2>
                    <p class="mb-4 line-clamp-3 flex-1 text-[12.5px] leading-relaxed text-muted">{{ $p->deskripsi }}</p>

                    <div class="mt-auto">
                        <small class="block text-[11px] text-muted">{{ $p->labelHarga() }}</small>
                        <strong class="block text-xl font-extrabold text-maroon">{{ $rupiah($p->harga_mulai) }}</strong>
                        <a href="{{ route('katalog.show', $p) }}" class="btn btn-primary mt-3.5 min-h-[42px] w-full rounded-full text-sm font-bold">Lihat Pilihan</a>
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