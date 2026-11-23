{{-- Kartu filter admin: kotak cari + (opsional) dropdown di slot.
     Pakai: <x-admin.cari placeholder="Cari ..."> <select class="pilih">...</select> </x-admin.cari> --}}
@props(['placeholder' => 'Cari'])

<form method="GET" {{ $attributes->class(['kartu mb-5.5 flex flex-wrap gap-2.5 p-3.5 [&_select]:h-11.5 [&_select]:flex-1 [&_select]:rounded-xl [&_select]:border [&_select]:border-line [&_select]:bg-white [&_select]:px-3.5 [&_select]:text-sm [&_select]:font-semibold sm:[&_select]:flex-none']) }}>
    <label class="flex min-w-full flex-1 items-center gap-2.5 rounded-xl border border-line px-3.5 text-muted focus-within:border-maroon sm:min-w-60">
        <i class="bi bi-search"></i>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}" class="h-11 flex-1 bg-transparent text-sm text-ink outline-none">
    </label>
    {{ $slot }}
</form>
