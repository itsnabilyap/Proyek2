@php
    $edit = $produk->exists;
    $C = \App\Http\Controllers\Admin\ProdukController::class;

    // Baris ukuran: dari input lama (validasi gagal) → dari database → 3 baris kosong untuk produk baru
    $baris = old('ukuran') ?? ($produk->exists && $produk->ukuran->isNotEmpty()
        ? $produk->ukuran->map(fn ($u) => [
            'id' => $u->id, 'nama' => $u->nama_ukuran, 'harga' => $u->harga,
            'label' => $u->label, 'keterangan' => $u->keterangan, 'detail' => $u->detail,
        ])->all()
        : collect($C::UKURAN_AWAL)->map(fn ($n) => ['nama' => $n])->all());


    $salah = 'text-xs text-danger';
    $judul = 'mb-4 text-[17px] font-bold';
@endphp

<form method="POST" enctype="multipart/form-data"
      action="{{ $edit ? route('admin.produk.update', $produk) : route('admin.produk.store') }}">
    @csrf
    @if ($edit) @method('PUT') @endif
    <input type="hidden" name="_modal" value="1">
    <input type="hidden" name="_produk_id" value="{{ $produk->id }}">

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-danger-soft px-4 py-3 text-sm text-danger">Ada isian yang belum benar. Cek kolom bertanda merah{{ $edit ? '' : ', lalu pilih ulang fotonya' }}.</div>
    @endif

    {{-- 2 kolom (info | foto) mulai laptop; di popup mulai tablet --}}
    <div class="grid items-start gap-x-5.5 lg:grid-cols-[1fr_340px] md:in-[dialog]:grid-cols-[1fr_300px]">
        {{-- Info produk --}}
        <section class="kartu mb-5.5">
            <h2 class="{{ $judul }}">Info Produk</h2>

            <label class="isian">
                <span>Nama produk</span>
                <input type="text" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}" required class="input">
                @error('nama_produk') <small class="{{ $salah }}">{{ $message }}</small> @enderror
            </label>

            <div class="grid gap-x-3.5 sm:grid-cols-2">
                <label class="isian">
                    <span>Kategori</span>
                    <select name="kategori_id" required class="input">
                        <option value="">Pilih kategori</option>
                        @foreach ($kategori as $kat)
                            <option value="{{ $kat->id }}" @selected(old('kategori_id', $produk->kategori_id) == $kat->id)>{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id') <small class="{{ $salah }}">{{ $message }}</small> @enderror
                </label>

                <label class="isian">
                    <span>Stok siap jual (buket)</span>
                    <input type="number" name="stok" min="0" value="{{ old('stok', $produk->stok) }}" required class="input">
                    @error('stok') <small class="{{ $salah }}">{{ $message }}</small> @enderror
                </label>
            </div>

            <label class="isian">
                <span>Deskripsi</span>
                <textarea name="deskripsi" rows="4" class="input">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                @error('deskripsi') <small class="{{ $salah }}">{{ $message }}</small> @enderror
            </label>

            <label class="flex cursor-pointer items-center gap-2.5 text-sm font-semibold">
                <input type="checkbox" name="aktif" value="1" @checked(old('aktif', $produk->aktif)) class="size-4.5 accent-maroon">
                Tampilkan di katalog
            </label>
        </section>

        {{-- Foto --}}
        <section class="kartu mb-5.5">
            <h2 class="{{ $judul }}">Foto Produk</h2>

            {{-- Revisi: foto dipisah ke tabel gambar_produk. 1 produk bisa banyak foto. --}}
            <p class="mb-3 text-xs text-muted">Maks. {{ \App\Http\Controllers\Admin\ProdukController::MAKS_FOTO }} foto, masing-masing 2 MB. Foto <b>sampul</b> tampil di kartu katalog. Pilih <b>ukuran</b> di tiap foto supaya foto ikut berganti saat pelanggan memilih ukuran.</p>

            @if ($produk->exists && $produk->galeri->isNotEmpty())
                <div class="mb-3 grid grid-cols-3 gap-2.5">
                    @foreach ($produk->galeri as $g)
                        <div class="overflow-hidden rounded-xl border border-line bg-cream has-[[data-hapus]:checked]:opacity-40">
                            @if ($g->url)
                                <img src="{{ $g->url }}" alt="" class="aspect-square w-full object-cover">
                            @else
                                <span class="flex aspect-square items-center justify-center text-xs text-muted">File hilang</span>
                            @endif
                            <div class="flex flex-col gap-1 px-2 py-1.5 text-[11.5px]">
                                <label class="flex cursor-pointer items-center gap-1.5">
                                    <input type="radio" name="sampul" value="{{ $g->id }}" @checked(old('sampul', $produk->galeri->first()->id) == $g->id) class="accent-maroon"> Sampul
                                </label>
                                <select name="ukuran_foto_id[{{ $g->id }}]" aria-label="Ukuran di foto ini"
                                        class="h-7 w-full rounded-md border border-line bg-white px-1 text-[11.5px] focus:border-maroon focus:outline-none">
                                    <option value="">Ukuran: -</option>
                                    @foreach ($produk->ukuran as $u)
                                        <option value="{{ $u->id }}" @selected(old("ukuran_foto_id.{$g->id}", $g->ukuran_produk_id) == $u->id)>{{ $u->nama_ukuran }}</option>
                                    @endforeach
                                </select>
                                <label class="flex cursor-pointer items-center gap-1.5 text-danger">
                                    <input type="checkbox" name="hapus_foto[]" value="{{ $g->id }}" data-hapus @checked(in_array($g->id, old('hapus_foto', []))) class="accent-danger"> Hapus
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <label class="relative mb-2 flex cursor-pointer flex-col items-center justify-center gap-1.5 rounded-2xl border-2 border-dashed border-[#E2C3CA] bg-cream px-4 py-6 text-center text-[13px] text-muted hover:border-maroon">
                <i class="bi bi-cloud-arrow-up text-[26px] text-maroon"></i>
                {{ $produk->exists ? 'Tambah foto' : 'Pilih foto' }} <span class="text-xs">(bisa pilih beberapa sekaligus)</span>
                <input type="file" name="foto[]" accept="image/*" multiple data-foto-baru class="absolute inset-0 cursor-pointer opacity-0">
            </label>
            <div data-pratinjau-baru class="mb-3 grid grid-cols-4 gap-2"></div>
            @unless ($produk->exists)
                <small class="mb-2 block text-xs text-muted">Ukuran untuk tiap foto bisa diatur setelah produk disimpan (lewat Edit).</small>
            @endunless
            @error('foto') <small class="{{ $salah }}">{{ $message }}</small> @enderror
            @error('foto.*') <small class="{{ $salah }}">{{ $message }}</small> @enderror

        </section>
    </div>

    {{-- Ukuran & harga: jumlah baris bebas (maks. 10) --}}
    <section class="kartu mb-5.5">
        <div class="mb-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-[17px] font-bold">Ukuran &amp; Harga</h2>
                <small class="text-xs text-muted">Satu kotak = satu pilihan, misal <b>Small</b>, <b>2 coklat</b>, atau <b>20 lembar</b>. Detail perbandingan: satu baris satu data, format <b>Nama: isi</b>.</small>
            </div>
            <button type="button" data-tambah-ukuran class="btn btn-sm btn-soft shrink-0 rounded-xl"><i class="bi bi-plus-lg"></i> Tambah ukuran</button>
        </div>
        @error('ukuran') <div class="mb-4 rounded-xl bg-danger-soft px-4 py-3 text-sm text-danger">{{ $message }}</div> @enderror

        <div data-daftar-ukuran data-maks="{{ $C::MAKS_UKURAN }}" class="grid gap-4 lg:grid-cols-3 md:in-[dialog]:grid-cols-3">
            @foreach ($baris as $i => $u)
                @include('admin.produk._ukuran', ['i' => $i, 'u' => $u])
            @endforeach
        </div>

        {{-- Cetakan baris baru (dipakai JS tombol "Tambah ukuran") --}}
        <template data-cetakan-ukuran>
            @include('admin.produk._ukuran', ['i' => '__I__', 'u' => []])
        </template>
    </section>

    {{-- Varian (opsional) --}}
    <section class="kartu mb-5.5">
        <div class="mb-4 flex flex-col gap-0.5">
            <h2 class="text-[17px] font-bold">Varian <span class="font-normal text-muted">(opsional)</span></h2>
            <small class="text-xs text-muted">Pilihan tambahan yang harganya sama, misal merek coklat atau pecahan uang. Kosongkan kalau tidak ada.</small>
        </div>
        <div class="grid gap-x-3.5 sm:grid-cols-[1fr_1.4fr]">
            <label class="isian">
                <span>Judul varian</span>
                <input type="text" name="varian_judul" value="{{ old('varian_judul', $produk->varian_judul) }}" placeholder="contoh: Merek coklat" class="input">
                @error('varian_judul') <small class="{{ $salah }}">{{ $message }}</small> @enderror
            </label>
            <label class="isian">
                <span>Pilihan (satu per baris)</span>
                <textarea name="varian_pilihan" rows="3" placeholder="Dairy Milk&#10;Silverqueen" class="input">{{ old('varian_pilihan', implode("\n", $produk->varian_pilihan ?? [])) }}</textarea>
                @error('varian_pilihan') <small class="{{ $salah }}">{{ $message }}</small> @enderror
            </label>
        </div>
    </section>

    {{-- Tombol simpan: di popup menempel di bawah --}}
    <div class="flex justify-end gap-2.5 in-[dialog]:sticky in-[dialog]:bottom-0 in-[dialog]:-mx-4 in-[dialog]:border-t in-[dialog]:border-line in-[dialog]:bg-white in-[dialog]:px-4 in-[dialog]:py-3 sm:in-[dialog]:-mx-6 sm:in-[dialog]:px-6 sm:in-[dialog]:py-3.5">
        <a href="{{ route('admin.produk.index') }}" class="btn btn-outline rounded-xl" data-tutup>Batal</a>
        <button type="submit" class="btn btn-primary rounded-xl">{{ $edit ? 'Simpan Perubahan' : 'Tambah Produk' }}</button>
    </div>
</form>
