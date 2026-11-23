@php
    $menuAkun = [
        ['Pesanan Saya', route('pesanan.index'), 'pesanan.*'],
        ['Request Custom', route('custom.create'), 'custom.*'],
        ['Alamat Tersimpan', url('/akun/alamat'), 'alamat.*'],
        ['Pengaturan Akun', route('akun.edit'), 'akun.*'],
    ];
    $jumlahAktif = auth()->user()->pesanan()->whereNotIn('status_pesanan', ['selesai', 'dibatalkan'])->count();
    $link = 'group flex min-h-11.5 w-full items-center justify-between rounded-xl px-3.5 text-left text-sm font-medium transition-colors';
@endphp
{{-- Menu kiri halaman akun. Di tablet/HP pindah ke atas, lebar penuh. --}}
<aside aria-label="Menu akun"
       class="flex w-full flex-col gap-1.5 rounded-3xl border border-line-soft bg-white p-5 md:sticky md:top-24 md:w-60 md:shrink-0">
    @foreach ($menuAkun as [$label, $href, $pola])
        @php $aktif = request()->routeIs($pola); @endphp
        <a href="{{ $href }}" @if ($aktif) aria-current="page" @endif
           class="{{ $link }} hover:bg-[#FDF2F4] aria-[current=page]:bg-maroon aria-[current=page]:font-semibold aria-[current=page]:text-white">
            {{ $label }}
            @if ($label === 'Pesanan Saya' && $jumlahAktif > 0)
                <span class="rounded-full bg-maroon px-2 py-0.5 text-xs font-bold text-white group-aria-[current=page]:bg-white/20">{{ $jumlahAktif }}</span>
            @endif
        </a>
    @endforeach
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="{{ $link }} font-semibold text-danger hover:bg-[#FDF2F4]">Keluar</button>
    </form>
</aside>
