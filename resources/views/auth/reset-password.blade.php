@extends('layouts.app')

@section('title', 'Ganti Password')
@section('main-class', 'bg-kelopak flex min-h-[60vh] justify-center px-4 pt-12 pb-20')
@section('tanpa-flash', '1')

@section('content')
<div class="w-full max-w-[400px]">
    <h1 class="text-center text-3xl font-bold text-maroon">Ganti Password</h1>
    <p class="mt-1.5 mb-5.5 text-center text-sm">Buat password baru untuk akunmu.</p>

    <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-3" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="flex flex-col gap-1">
            <label for="email" class="sr-only">Email</label>
            <input id="email" name="email" type="email" placeholder="Email" value="{{ old('email', $email) }}" autocomplete="email" required
                   @class(['field', 'border-danger' => $errors->has('email')])>
            @error('email') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-1">
            <label for="password" class="sr-only">Password baru</label>
            <x-password id="password" placeholder="Password Baru" autocomplete="new-password" required />
            @error('password') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-1">
            <label for="password_confirmation" class="sr-only">Konfirmasi password baru</label>
            <x-password id="password_confirmation" label="konfirmasi password" placeholder="Konfirmasi Password Baru" autocomplete="new-password" required />
        </div>

        <button type="submit" class="btn btn-primary self-start rounded-[10px] px-6.5 font-bold">Simpan Password</button>
    </form>
</div>
@endsection
