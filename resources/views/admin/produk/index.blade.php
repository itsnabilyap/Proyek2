@extends('layouts.admin')

@section('title', 'Katalog Produk')
@section('judul', 'Katalog Produk')

@section('aksi')
    @can('kelola')
        <a href="{{ route('admin.produk.create') }}" data-form data-judul="Tambah Produk" class="btn btn-primary rounded-xl"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
    @endcan
@endsection

@php
    $batas = \App\Http\Controllers\Admin\ProdukController::BATAS;
    $k = fn ($n) => ($n % 1000 === 0 ? $n / 1000 : number_format($n / 1000, 1, ',', '')) . 'k';
    $kartu = [
        'semua'    => ['Total produk', ''],
        'tampil'   => ['Tampil di katalog', 'text-ok'],
        'sembunyi' => ['Disembunyikan', 'text-muted'],
        'menipis'  => ['Stok menipis', 'text-danger'],
    ];
    $bisaKelola = auth()->user()->can('kelola');
@endphp

@section('content')
{{-- ===== KARTU RINGKASAN (bisa diklik untuk filter) ===== --}}
<div class="mb-5.5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($kartu as $kunci => [$label, $warna])
        <a href="{{ route('admin.produk.index', array_merge(request()->except('filter', 'page'), $kunci === 'semua' ? [] : ['filter' => $kunci])) }}"
           @if ($filter === $kunci) aria-current="page" @endif
           class="flex flex-col gap-2 rounded-[18px] border border-line bg-white p-5 transition-colors hover:border-[#E2C3CA] aria-[current=page]:border-maroon aria-[current=page]:ring-1 aria-[current=page]:ring-maroon">
            <span class="text-[13px] text-muted">{{ $label }}</span>
            <strong class="text-[34px] leading-none font-extrabold {{ $warna }}">{{ $ringkas[$kunci] }}</strong>
        </a>
    @endforeach
</div>

{{-- ===== CARI & KATEGORI ===== --}}
<x-admin.cari placeholder="Cari nama produk">
    @if ($filter !== 'semua') <input type="hidden" name="filter" value="{{ $filter }}"> @endif
    <select name="kategori" onchange="this.form.submit()">
        <option value="">Semua kategori</option>
        @foreach ($kategori as $kat)
            <option value="{{ $kat->id }}" @selected(request('kategori') == $kat->id)>{{ $kat->nama_kategori }}</option>
        @endforeach
    </select>
</x-admin.cari>

{{-- ===== BANNER STOK MENIPIS ===== --}}
@if ($menipis->isNotEmpty())
    <x-admin.banner>
        <strong>{{ $menipis->count() }} produk stoknya kurang dari {{ $batas }}:</strong>
        {{ $menipis->map(fn ($p) => $p->nama_produk . ' (' . ($p->stok ? $p->stok : 'habis') . ')')->implode(', ') }}.
        <x-slot:aksi><a href="{{ route('admin.produk.index', ['filter' => 'menipis']) }}">Lihat</a></x-slot:aksi>
    </x-admin.banner>
@endif

{{-- ===== TABEL ===== --}}
<section class="kartu mb-5.5 overflow-hidden p-0!">
    <div class="overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Produk</th><th>Harga (S / M / L)</th><th>Stok Siap Jual</th>
                    @if ($bisaKelola) <th>Tambah Stok</th> @endif
                    <th>Stok Minimal</th><th>Tampil di Katalog</th>
                    @if ($bisaKelola) <th class="text-right!">Aksi</th> @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($produk as $p)
                    @php $harga = $p->ukuran->sortBy('harga')->pluck('harga'); @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if ($p->gambar_url)
                                    <img src="{{ $p->gambar_url }}" alt="{{ $p->nama_produk }}" class="size-13 shrink-0 rounded-[10px] object-cover">
                                @else
                                    <span class="flex size-13 shrink-0 items-center justify-center rounded-[10px] bg-blush text-xl text-maroon"><i class="bi bi-image"></i></span>
                                @endif
                                <div>
                                    <strong class="block text-sm">{{ $p->nama_produk }}</strong>
                                    <small>{{ $p->kategori?->nama_kategori ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $harga->isEmpty() ? '-' : $harga->map($k)->implode(' / ') }}</td>
                        <td>
                            <strong class="block">{{ $p->stok }} buket</strong>
                            @if ($p->stok == 0)
                                <span class="tag b-merah mt-1">Habis</span>
                            @elseif ($p->stok < $batas)
                                <span class="tag b-kuning mt-1">Di bawah minimum</span>
                            @endif
                        </td>

                        @if ($bisaKelola)
                            <td>
                                <form method="POST" action="{{ route('admin.produk.stok', $p) }}"
                                      class="flex w-27.5 overflow-hidden rounded-[10px] border border-line focus-within:border-maroon">
                                    @csrf @method('PATCH')
                                    <input type="number" name="jumlah" value="10" min="1" max="999" aria-label="Jumlah tambah stok {{ $p->nama_produk }}"
                                           class="w-full min-w-0 px-2.5 py-2 text-[13px] outline-none">
                                    <button type="submit" title="Tambah stok" class="w-9 shrink-0 bg-maroon text-white hover:bg-wine"><i class="bi bi-plus-lg"></i></button>
                                </form>
                            </td>
                        @endif

                        <td>{{ $batas }} buket</td>

                        <td>
                            @if ($bisaKelola)
                                <form method="POST" action="{{ route('admin.produk.toggle', $p) }}">
                                    @csrf @method('PATCH')
                                    {{-- Saklar: warna & posisi bulatan mengikuti aria-checked --}}
                                    <button type="submit" role="switch" aria-checked="{{ $p->aktif ? 'true' : 'false' }}"
                                            title="{{ $p->aktif ? 'Sembunyikan' : 'Tampilkan' }}"
                                            class="group relative h-7 w-12 rounded-full bg-[#E6DADD] transition-colors aria-checked:bg-maroon">
                                        <span class="absolute top-1 left-1 size-5 rounded-full bg-white shadow-[0_1px_3px_rgb(0_0_0/0.2)] transition-transform group-aria-checked:translate-x-5"></span>
                                    </button>
                                </form>
                            @else
                                <span class="badge {{ $p->aktif ? 'b-hijau' : 'b-abu' }}">{{ $p->aktif ? 'Tampil' : 'Disembunyikan' }}</span>
                            @endif
                        </td>

                        @if ($bisaKelola)
                            <td>
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('admin.produk.edit', $p) }}" data-form data-judul="Edit {{ $p->nama_produk }}" title="Edit" class="ikon"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.produk.destroy', $p) }}"
                                          data-konfirmasi="Hapus produk?" data-nama="{{ $p->nama_produk }}" data-pesan="Produk hilang dari katalog, riwayat pesanan tetap aman." data-tombol="Ya, hapus">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus" class="ikon ikon-hapus"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="7" class="kosong">Tidak ada produk yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-admin.kaki :data="$produk" nama="produk" />
</section>

@can('kelola')
    @php
        // Validasi gagal → render ulang form di popup, lengkap dengan isian & error
        $bukaUlang = $errors->any() && old('_modal');
        $produkForm = null;
        if ($bukaUlang) {
            $produkForm = old('_produk_id')
                ? \App\Models\Produk::with('ukuran')->find(old('_produk_id'))
                : new \App\Models\Produk(['aktif' => true, 'stok' => 0]);
        }
    @endphp

    {{-- ===== POPUP TAMBAH / EDIT ===== --}}
    <x-admin.modal id="modal-produk" class="w-[min(1040px,calc(100%-32px))]"
                   :judul="$produkForm ? ($produkForm->exists ? 'Edit ' . $produkForm->nama_produk : 'Tambah Produk') : 'Produk'">
        <div data-modal-isi class="px-4 pt-4 sm:px-6 sm:pt-5">
            @if ($produkForm) @include('admin.produk._form', ['produk' => $produkForm]) @endif
        </div>
    </x-admin.modal>

    @include('admin.produk._script')


    <script>
        const modalProduk = document.getElementById('modal-produk');
        const isiProduk = modalProduk.querySelector('[data-modal-isi]');

        document.querySelectorAll('[data-form]').forEach(tombol => {
            tombol.addEventListener('click', async (e) => {
                e.preventDefault();
                modalProduk.querySelector('[data-modal-judul]').textContent = tombol.dataset.judul;
                isiProduk.innerHTML = '<p class="kosong">Memuat form…</p>';
                modalProduk.showModal();

                try {
                    const res = await fetch(tombol.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) throw new Error();
                    isiProduk.innerHTML = await res.text();
                } catch {
                    isiProduk.innerHTML = '<p class="kosong">Gagal memuat form. Coba lagi.</p>';
                }
            });
        });

        @if ($bukaUlang) modalProduk.showModal(); @endif
    </script>
@endcan
@endsection
