@extends('layouts.app')

@section('title', 'Reset Password | buket.imy')

@section('content')

<div class="auth-container">
    <h1 class="auth-title">Reset Kata Sandi</h1>

    <p class="auth-subtitle">
       Silakan masukkan kata sandi baru Anda untuk memperbarui keamanan akun.
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

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input
            type="hidden"
            name="token"
            value="{{ $token }}"
        >

        <input
            type="hidden"
            name="email"
            value="{{ old('email', $email) }}"
        >

        <input
            class="form-input"
            type="password"
            name="password"
            placeholder="Kata Sandi Baru"
            autocomplete="new-password"
            minlength="8"
            required
        >

        <input
            class="form-input"
            type="password"
            name="password_confirmation"
            placeholder="Konfirmasi Kata Sandi Baru"
            autocomplete="new-password"
            minlength="8"
            required>

        <button type="submit" class="primary-button">
            Ubah Kata Sandi
        </button>

        <a href="{{ route('login') }}" class="secondary-link">
            Kembali ke Login
        </a>
    </form>
</div>
@endsection