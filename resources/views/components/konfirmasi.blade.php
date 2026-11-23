{{--
    Popup konfirmasi pengganti confirm() bawaan browser. Satu popup untuk seluruh halaman.
    Pakai di form:
        <form ... data-konfirmasi="Hapus dari keranjang?"
                  data-nama="Bucket Coklat"                  (opsional, ditebalkan)
                  data-pesan="Buket ini akan dihapus."       (opsional)
                  data-tombol="Ya, hapus"                    (opsional)
                  data-jenis="hapus | tanya | selesai">      (warna & ikon, default: hapus)
--}}
<dialog id="dialogKonfirmasi" aria-labelledby="konfirmasiJudul" aria-describedby="konfirmasiPesan"
        class="modal m-auto w-[min(400px,calc(100%-32px))] overflow-hidden rounded-3xl bg-white p-0 text-center backdrop:bg-black/40 backdrop:backdrop-blur-[1px]">
    <div class="px-6 pt-8 pb-6 sm:px-8">
        {{-- Ikon: lingkaran dengan cincin lembut --}}
        <span data-k-ikon class="mx-auto mb-4.5 flex size-16 items-center justify-center rounded-full text-[26px] ring-8"></span>
        <h2 id="konfirmasiJudul" data-k-judul class="mb-2 font-serif text-[24px] leading-tight font-medium"></h2>
        <p id="konfirmasiPesan" class="text-sm leading-relaxed text-muted">
            <b data-k-nama class="block text-[15px] font-semibold text-ink"></b>
            <span data-k-pesan></span>
        </p>
    </div>
    <div class="grid grid-cols-2 gap-2.5 border-t border-line-soft bg-blush-50 px-6 py-4 sm:px-8">
        <button type="button" data-k-batal class="btn btn-outline min-h-11 rounded-full bg-white">Batal</button>
        <button type="button" data-k-ya class="btn min-h-11 rounded-full text-white"></button>
    </div>
</dialog>
