{{--
    Notifikasi melayang di pojok kanan atas (toast).
    Membaca flash session: success (hijau), error (merah), status (info, maroon).
    Hilang sendiri setelah 5 detik; berhenti saat kursor di atasnya; bisa ditutup dengan ×.
    Pakai: <x-toast />  ·  di halaman pelanggan: <x-toast class="top-20" /> (di bawah navbar)
--}}
@php
    $jenis = [
        'success' => ['bi-check-lg',       'bg-ok text-white',     'bg-ok',     'Berhasil'],
        'error'   => ['bi-exclamation-lg', 'bg-danger text-white', 'bg-danger', 'Gagal'],
        'status'  => ['bi-info-lg',        'bg-blush text-maroon', 'bg-maroon', 'Info'],
    ];
    $pesan = collect($jenis)->filter(fn ($_, $kunci) => session()->has($kunci));
@endphp

@if ($pesan->isNotEmpty())
    <div {{ $attributes->class(['pointer-events-none fixed top-4 right-4 left-4 z-[80] flex flex-col items-end gap-2.5 print:hidden sm:left-auto']) }}>
        @foreach ($pesan as $kunci => [$ikon, $warnaIkon, $warnaGaris, $judul])
            <div data-toast role="{{ $kunci === 'error' ? 'alert' : 'status' }}"
                 class="group pointer-events-auto relative flex w-full items-start gap-3 overflow-hidden rounded-2xl border border-line bg-white py-3 pr-2.5 pl-3.5 shadow-pop motion-safe:animate-[toast-masuk_.3s_ease-out] sm:w-[360px] data-keluar:opacity-0 data-keluar:translate-x-4 transition-[opacity,translate] duration-200">
                <span class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-full text-[15px] {{ $warnaIkon }}"><i class="bi {{ $ikon }}"></i></span>
                <p class="min-w-0 flex-1 py-0.5 text-sm leading-snug">
                    <span class="sr-only">{{ $judul }}: </span>{{ session($kunci) }}
                </p>
                <button type="button" data-tutup-toast aria-label="Tutup notifikasi"
                        class="flex size-8 shrink-0 items-center justify-center rounded-lg text-muted hover:bg-blush hover:text-maroon">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
                {{-- Garis waktu: menyusut 5 detik, berhenti saat kursor di atas toast --}}
                <span data-waktu-toast aria-hidden="true"
                      class="absolute bottom-0 left-0 h-[3px] w-full origin-left {{ $warnaGaris }} animate-[toast-waktu_5s_linear_forwards] group-hover:[animation-play-state:paused] group-focus-within:[animation-play-state:paused]"></span>
            </div>
        @endforeach
    </div>
@endif
