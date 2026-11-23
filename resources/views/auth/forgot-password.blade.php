@extends('layouts.app')

@section('title', 'Lupa Password')
@section('main-class', 'bg-kelopak flex min-h-[60vh] justify-center px-4 pt-12 pb-20')
@section('tanpa-flash', '1')

@section('content')
<div class="w-full max-w-[400px]">
    <h1 class="text-center text-3xl font-bold text-maroon">Lupa Password</h1>
    <p class="mt-1.5 mb-5.5 text-center text-sm">Masukkan email akunmu. Kami kirim link untuk membuat password baru.</p>

    @if (session('status'))
        <div class="mb-3.5 rounded-[10px] bg-ok-soft px-3.5 py-2.5 text-[13px] font-semibold text-ok">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-3" novalidate>
        @csrf
        <div class="flex flex-col gap-1">
            <label for="email" class="sr-only">Email</label>
            <input id="email" name="email" type="email" placeholder="Email" value="{{ old('email') }}" autocomplete="email" required autofocus
                   @class(['field', 'border-danger' => $errors->has('email')])>
            @error('email') <span class="field-error">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="btn btn-primary self-start rounded-[10px] px-6.5 font-bold">Kirim Link</button>
    </form>

    <p class="mt-6 text-center text-sm"><a href="{{ route('login') }}" class="font-bold underline">Kembali ke halaman masuk</a></p>
</div>
@endsection
