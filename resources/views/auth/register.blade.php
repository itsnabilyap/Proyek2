@extends('layouts.app')

@section('title', 'Buat Akun')
@section('main-class', 'bg-kelopak flex min-h-[60vh] justify-center px-4 pt-12 pb-20')
@section('tanpa-flash', '1')

@section('content')
<div class="w-full max-w-[400px]">
    <h1 class="text-center text-3xl font-bold text-maroon">Buat Akun</h1>
    <p class="mt-1.5 mb-5.5 text-center text-sm">
        Sudah punya akun? <a href="{{ route('login') }}" class="font-bold underline">Masuk di sini.</a>
    </p>

    <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-3" novalidate>
        @csrf

        @foreach ([['name', 'text', 'Nama Lengkap', 'name'], ['username', 'text', 'Username', 'username'], ['email', 'email', 'Email', 'email']] as [$kolom, $tipe, $teks, $auto])
            <div class="flex flex-col gap-1">
                <label for="{{ $kolom }}" class="sr-only">{{ $teks }}</label>
                <input id="{{ $kolom }}" name="{{ $kolom }}" type="{{ $tipe }}" placeholder="{{ $teks }}" value="{{ old($kolom) }}"
                       autocomplete="{{ $auto }}" required @if ($loop->first) autofocus @endif
                       @class(['field', 'border-danger' => $errors->has($kolom)])>
                @error($kolom) <span class="field-error">{{ $message }}</span> @enderror
            </div>
        @endforeach

        <div class="flex flex-col gap-1">
            <label for="password" class="sr-only">Password</label>
            <x-password id="password" placeholder="Password" autocomplete="new-password" required />
            @error('password') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-1">
            <label for="password_confirmation" class="sr-only">Konfirmasi Password</label>
            <x-password id="password_confirmation" label="konfirmasi password" placeholder="Konfirmasi Password" autocomplete="new-password" required />
        </div>

        <div class="flex flex-col gap-1">
            <label class="inline-flex items-center gap-2 text-[13px]">
                <input type="checkbox" name="setuju" value="1" class="size-4 accent-maroon" @checked(old('setuju'))>
                <span>Saya menyetujui <a href="#" class="font-semibold text-maroon hover:underline">Syarat &amp; Ketentuan</a></span>
            </label>
            @error('setuju') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="mt-0.5 flex flex-wrap items-center gap-3">
            <button type="submit" class="btn btn-primary rounded-[10px] px-6.5 font-bold">Buat Akun</button>
            <span class="text-xs text-muted">Lengkapi semua kolom untuk membuat akun.</span>
        </div>
    </form>
</div>
@endsection
