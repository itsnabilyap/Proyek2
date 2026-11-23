{{-- Banner kuning peringatan stok (Katalog Produk, Stok Bahan). Slot = isi teks, $aksi = link di kanan. --}}
<div class="mb-5.5 flex flex-wrap items-center gap-3 rounded-2xl border border-[#F0D9A8] bg-[#FFF8E8] px-5 py-4 text-[13px] text-[#6B4A00] [&_a]:font-bold [&_a]:underline-offset-2 hover:[&_a]:underline">
    <i class="bi bi-exclamation-triangle"></i>
    <p class="flex-1">{{ $slot }}</p>
    {{ $aksi ?? '' }}
</div>
