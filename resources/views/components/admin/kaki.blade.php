{{-- Kaki tabel admin: "Menampilkan x dari y" + nomor halaman.
     Pakai: <x-admin.kaki :data="$pesanan" nama="pesanan" /> --}}
@props(['data', 'nama'])
@php $kotak = 'flex size-9 items-center justify-center rounded-[10px] border border-line bg-white font-semibold'; @endphp

<footer class="flex flex-col justify-between gap-3 p-4 text-[13px] text-muted sm:flex-row sm:items-center">
    <span>Menampilkan {{ $data->count() }} dari {{ $data->total() }} {{ $nama }}</span>

    @if ($data->lastPage() > 1)
        <nav class="flex gap-1.5 text-ink" aria-label="Halaman">
            <a href="{{ $data->previousPageUrl() ?? '#' }}" @class([$kotak, 'pointer-events-none opacity-40' => $data->onFirstPage()]) aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></a>
            @foreach ($data->getUrlRange(1, $data->lastPage()) as $hal => $url)
                <a href="{{ $url }}" @if ($hal === $data->currentPage()) aria-current="page" @endif
                   class="{{ $kotak }} aria-[current=page]:border-maroon aria-[current=page]:bg-maroon aria-[current=page]:text-white">{{ $hal }}</a>
            @endforeach
            <a href="{{ $data->nextPageUrl() ?? '#' }}" @class([$kotak, 'pointer-events-none opacity-40' => ! $data->hasMorePages()]) aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></a>
        </nav>
    @endif
</footer>
