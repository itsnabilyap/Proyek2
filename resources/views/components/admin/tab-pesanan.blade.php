{{-- Pilihan di atas Data Pesanan: Pesanan Katalog | Request Custom --}}
@props(['aktif'])
@php $baru = \App\Models\Custom::where('status', 'baru')->count(); @endphp

<nav class="grid grid-cols-2 gap-1.5 rounded-[18px] border border-line bg-white p-1.5 sm:max-w-[520px]">
    @foreach (['katalog' => ['Pesanan Katalog', 'admin.pesanan.index', 'Sudah checkout & bayar'], 'custom' => ['Request Custom', 'admin.pesanan.custom.index', 'Harga dibahas via WhatsApp']] as $kunci => [$label, $route, $ket])
        <a href="{{ route($route) }}" @if ($aktif === $kunci) aria-current="page" @endif
           class="group flex flex-col items-center gap-0.5 rounded-[13px] p-3 text-center hover:bg-cream aria-[current=page]:bg-maroon aria-[current=page]:text-white">
            <strong class="flex items-center gap-1.5 text-sm">{{ $label }}
                @if ($kunci === 'custom' && $baru)
                    <span class="rounded-full bg-danger px-1.5 py-px text-[11px] text-white group-aria-[current=page]:bg-white group-aria-[current=page]:text-maroon">{{ $baru }} baru</span>
                @endif
            </strong>
            <small class="text-xs text-muted group-aria-[current=page]:text-white/80">{{ $ket }}</small>
        </a>
    @endforeach
</nav>
