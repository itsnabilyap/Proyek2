{{-- Popup admin (<dialog>) dengan kepala + tombol tutup.
     Judul bisa diganti JS lewat [data-modal-judul]. Lebar: max-w-* lewat atribut class.
     Pakai: <x-admin.modal id="modal-x" judul="Detail" class="w-[min(520px,calc(100%-32px))]"> ... </x-admin.modal> --}}
@props(['id', 'judul' => ''])

<dialog id="{{ $id }}" {{ $attributes->class(['modal']) }}>
    <header class="sticky top-0 z-10 flex items-center justify-between gap-3 border-b border-line bg-white px-4 py-3.5 sm:px-6 sm:py-4.5">
        <h2 data-modal-judul class="text-lg font-bold">{{ $judul }}</h2>
        <button type="button" aria-label="Tutup" onclick="this.closest('dialog').close()"
                class="flex size-9 shrink-0 items-center justify-center rounded-[10px] bg-cream text-base text-muted hover:bg-blush hover:text-maroon">
            <i class="bi bi-x-lg"></i>
        </button>
    </header>
    {{ $slot }}
</dialog>
