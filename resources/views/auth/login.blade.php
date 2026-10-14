
@extends('layouts.app')

@section('title', 'Masuk Akun')

@section('content')
<div class="auth-container">
    <h1 class="auth-title">Masuk Akun</h1>

    <p class="auth-subtitle">
        Belum punya akun?
        <a href="{{ route('register') }}">Daftar di sini.</a>
    </p>

    @if (session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('login.process') }}" method="POST">
        @csrf

        <input class="form-input"
               type="text"
               name="username"
               placeholder="Username"
               value="{{ old('username') }}"
               autocomplete="username"
               required autofocus>

        <div class="password-wrap">
            <input class="form-input"
                   id="login-password"
                   type="password"
                   name="password"
                   placeholder="Password"
                   autocomplete="current-password"
                   required>
            <button type="button"
                    class="toggle-password"
                    data-target="login-password"
                    aria-label="Tampilkan password">◉</button>
        </div>

        <div class="forgot">
            <a href="{{ route('password.request') }}">
                Lupa kata sandi?
            </a>
        </div>

        <button type="submit" class="primary-button">Masuk</button>
        <a href="{{ route('home') }}" class="secondary-link">
            Kembali ke Beranda
        </a>
    </form>
</div>
@endsection
