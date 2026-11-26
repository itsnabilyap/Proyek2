{{-- Tombol profil + dropdown menu akun. Dipakai di header pelanggan (layouts.app) dan checkout (layouts.checkout). --}}
@php
    $user = auth()->user();
    $kata = preg_split('/\s+/', trim($user->name));
    $inisial = strtoupper(substr($kata[0], 0, 1) . substr($kata[1] ?? '', 0, 1));
    $namaDepan = $kata[0];
    $pesananAktif = $user->pesanan()->whereNotIn('status_pesanan', ['selesai', 'dibatalkan'])->count();
@endphp
<div class="relative">
    <button type="button" id="accountButton" aria-expanded="false" aria-controls="accountDropdown"
            class="group inline-flex h-[46px] items-center gap-2.5 rounded-full border border-line bg-white py-0 pr-3.5 pl-1.5 transition-colors hover:border-maroon hover:bg-blush-50">
        <span class="inline-flex size-9 items-center justify-center rounded-full bg-maroon text-[13px] font-bold text-white">{{ $inisial }}</span>
        <span class="text-[15px] font-semibold max-sm:hidden">{{ $namaDepan }}</span>
        <i class="bi bi-chevron-down text-xs text-muted transition-transform group-aria-expanded:rotate-180"></i>
    </button>

    <div id="accountDropdown" hidden
         class="absolute top-[calc(100%+12px)] right-0 z-30 w-[min(340px,calc(100vw-32px))] rounded-[20px] border border-line-soft bg-white p-4.5 shadow-pop">
        <div class="flex items-center gap-3.5 px-1.5 pt-1 pb-4">
            <span class="inline-flex size-13 shrink-0 items-center justify-center rounded-full bg-maroon text-base font-bold text-white">{{ $inisial }}</span>
            <div class="flex min-w-0 flex-col">
                <strong class="text-base">{{ $user->name }}</strong>
                <small class="truncate text-[13px] text-muted">{{ $user->email }}</small>
            </div>
        </div>

        @php
            $menuAkun = [
                [url('/pesanan'), 'bi-bag', 'Pesanan Saya', 'Status dan riwayat pesanan', $pesananAktif],
                [route('custom.riwayat'), 'bi-stars', 'Request Custom', 'Riwayat dan status request', 0],
                [route('akun.edit'), 'bi-person', 'Pengaturan Akun', 'Edit profil dan ganti kata sandi', 0],
                ['https://wa.me/6283184710305', 'bi-chat', 'Bantuan', 'Chat admin via WhatsApp', 0],
            ];
        @endphp
        @foreach ($menuAkun as [$href, $ikon, $judul, $ket, $angka])
            <a href="{{ $href }}" @if (str_starts_with($href, 'https://wa.me')) target="_blank" rel="noopener noreferrer" @endif
               class="group/link flex w-full items-center gap-3.5 rounded-xl px-1.5 py-2 transition-colors hover:bg-blush focus-visible:bg-blush focus-visible:outline-none">
                <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-[10px] bg-blush text-base text-maroon transition-colors group-hover/link:bg-maroon group-hover/link:text-white">
                    <i class="bi {{ $ikon }}"></i>
                </span>
                <span class="flex flex-1 flex-col">
                    <strong class="text-sm font-semibold group-hover/link:text-maroon">{{ $judul }}</strong>
                    <small class="text-xs text-muted">{{ $ket }}</small>
                </span>
                @if ($angka > 0)
                    <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-maroon px-1.5 text-xs font-bold text-white">{{ $angka }}</span>
                @endif
            </a>
        @endforeach

        <form action="{{ route('logout') }}" method="POST" class="mt-2 border-t border-line-soft pt-2">
            @csrf
            <button type="submit" class="group/link flex w-full items-center gap-3.5 rounded-xl px-1.5 py-2 text-left transition-colors hover:bg-danger-soft">
                <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-[10px] bg-danger-soft text-base text-danger transition-colors group-hover/link:bg-danger group-hover/link:text-white">
                    <i class="bi bi-box-arrow-right"></i>
                </span>
                <strong class="text-sm text-danger">Keluar</strong>
            </button>
        </form>
    </div>
</div>
