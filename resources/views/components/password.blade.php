{{-- Kolom password + tombol mata. Pakai: <x-password id="password" placeholder="Password" /> --}}
@props(['id', 'name' => null, 'label' => 'password'])

@php $name ??= $id; @endphp

<div class="relative">
    <input id="{{ $id }}" name="{{ $name }}" type="password"
           {{ $attributes->class(['field pr-11', 'border-danger' => $errors->has($name)]) }}>
    <button type="button" data-toggle-password="{{ $id }}" aria-label="Tampilkan {{ $label }}"
            class="absolute top-1/2 right-1.5 flex size-8.5 -translate-y-1/2 items-center justify-center rounded-lg text-base text-muted hover:bg-blush">
        <i class="bi bi-eye"></i>
    </button>
</div>
