{{-- Satu kotak ukuran di form produk. $i = urutan (atau "__I__" untuk cetakan JS), $u = data baris. --}}
@php
    $salah = 'text-xs text-danger';
@endphp

<div data-ukuran class="relative rounded-[14px] border border-line p-4">
    <input type="hidden" name="ukuran[{{ $i }}][id]" value="{{ $u['id'] ?? '' }}">
    <button type="button" data-hapus-ukuran title="Hapus ukuran ini" aria-label="Hapus ukuran ini" class="ikon ikon-hapus absolute top-2.5 right-2.5"><i class="bi bi-x-lg"></i></button>

    <div class="grid grid-cols-2 gap-x-3 pr-9">
        <label class="isian">
            <span>Nama ukuran</span>
            <input type="text" name="ukuran[{{ $i }}][nama]" value="{{ $u['nama'] ?? '' }}" placeholder="Medium" class="input">
            @error("ukuran.$i.nama") <small class="{{ $salah }}">{{ $message }}</small> @enderror
        </label>
        <label class="isian">
            <span>Harga (Rp)</span>
            <input type="number" name="ukuran[{{ $i }}][harga]" value="{{ $u['harga'] ?? '' }}" min="1000" step="500" class="input">
            @error("ukuran.$i.harga") <small class="{{ $salah }}">{{ $message }}</small> @enderror
        </label>
    </div>
    <label class="isian">
        <span>Label <span class="font-normal text-muted">(opsional)</span></span>
        <input type="text" name="ukuran[{{ $i }}][label]" value="{{ $u['label'] ?? '' }}" placeholder="Paling laris" class="input">
    </label>
    <label class="isian mb-0">
        <span>Keterangan</span>
        <input type="text" name="ukuran[{{ $i }}][keterangan]" value="{{ $u['keterangan'] ?? '' }}" placeholder="6 tangkai" class="input">
    </label>
</div>
