@extends('layouts.app')

@section('title', 'Masuk')
@section('main-class', 'bg-kelopak flex min-h-[60vh] justify-center px-4 pt-12 pb-20')
@section('tanpa-flash', '1')

@section('content')
<div class="w-full max-w-[400px]">
    <h1 class="text-center text-3xl font-bold text-maroon">Masuk</h1>
    <p class="mt-1.5 mb-5.5 text-center text-sm">
        Belum punya akun? <a href="{{ route('register') }}" class="font-bold underline">Daftar di sini.</a>
    </p>

    @if (session('status'))
        <div class="mb-3.5 rounded-[10px] bg-ok-soft px-3.5 py-2.5 text-[13px] font-semibold text-ok">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login.process') }}" class="flex flex-col gap-3" novalidate>
        @csrf

        <div class="flex flex-col gap-1">
            <label for="username" class="sr-only">Username</label>
            <input id="username" name="username" type="text" placeholder="Username" value="{{ old('username') }}"
                   autocomplete="username" required autofocus @class(['field', 'border-danger' => $errors->has('username')])>
            @error('username') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-1">
            <label for="password" class="sr-only">Password</label>
            <x-password id="password" placeholder="Password" autocomplete="current-password" required />
            @error('password') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between gap-3">
            <label class="inline-flex items-center gap-2 text-[13px]">
                <input type="checkbox" name="remember" class="size-4 accent-maroon" @checked(old('remember'))>
                <span>Ingat Saya</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-[13px] font-semibold text-maroon hover:underline">Lupa password?</a>
        </div>

        <button type="submit" class="btn btn-primary self-start rounded-[10px] px-6.5 font-bold">Masuk</button>
    </form>
</div>
@endsection
