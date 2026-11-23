{{--
    Popup foto layar penuh yang bisa digeser (swipe di HP, tombol ‹ › / panah keyboard di laptop).
    Satu untuk seluruh halaman. Pemicunya tombol dengan:
        data-galeri='["url1","url2"]'  data-mulai="0"  data-judul="Bucket Bunga"
--}}
<dialog id="lightbox" aria-label="Galeri foto"
        class="m-0 h-dvh max-h-none w-screen max-w-none bg-transparent p-0 backdrop:bg-black/85">
    <div class="relative flex h-full flex-col text-white">
        {{-- Atas: judul, urutan, tutup --}}
        <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6">
            <p class="min-w-0 truncate text-sm"><span data-lb-judul class="font-semibold"></span> <span data-lb-urutan class="text-white/70"></span></p>
            <button type="button" data-lb-tutup aria-label="Tutup galeri"
                    class="flex size-10 shrink-0 items-center justify-center rounded-full bg-white/10 text-lg hover:bg-white/20"><i class="bi bi-x-lg"></i></button>
        </div>

        {{-- Jalur foto: scroll-snap → bisa digeser dengan jari --}}
        <div data-lb-jalur class="flex min-h-0 flex-1 snap-x snap-mandatory overflow-x-auto overscroll-contain [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"></div>

        {{-- Tombol kiri/kanan (laptop) --}}
        <button type="button" data-lb-geser="-1" aria-label="Foto sebelumnya"
                class="absolute top-1/2 left-3 hidden size-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/15 text-xl hover:bg-white/30 disabled:opacity-0 sm:flex"><i class="bi bi-chevron-left"></i></button>
        <button type="button" data-lb-geser="1" aria-label="Foto berikutnya"
                class="absolute top-1/2 right-3 hidden size-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/15 text-xl hover:bg-white/30 disabled:opacity-0 sm:flex"><i class="bi bi-chevron-right"></i></button>

        {{-- Titik penanda --}}
        <div data-lb-titik class="flex justify-center gap-1.5 py-4"></div>
    </div>
</dialog>
