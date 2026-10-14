@extends('layouts.app')

@section('title', 'Lupa Password | buket.imy')

@section('content')
<div class="auth-container">
    <h1 class="auth-title">Lupa Password</h1>

    <p class="auth-subtitle">
        Masukkan alamat email yang telah terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
    </p>

    @if (session('status'))
        <div class="alert">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <input
            class="form-input"
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="Masukkan Email Anda"
            autocomplete="email"
            required
            autofocus
        >

        <button type="submit" class="primary-button">
            Kirim Link Reset
        </button>

        <a href="{{ route('login') }}" class="secondary-link">
            Kembali ke Login
        </a>
    </form>
</div>
@endsection