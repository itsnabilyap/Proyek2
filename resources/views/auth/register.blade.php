
@extends('layouts.app')

@section('title', 'Buat Akun')

@section('content')
<div class="auth-container">
    <h1 class="auth-title">Buat Akun</h1>

    <p class="auth-subtitle">
        Sudah punya akun?
        <a href="{{ route('login') }}">Masuk di sini.</a>
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

    <form action="{{ route('register.store') }}" method="POST">
        @csrf

        <input class="form-input"
               type="text"
               name="name"
               placeholder="Full Name"
               value="{{ old('name') }}"
               autocomplete="name"
               required>

        <input class="form-input"
               type="text"
               name="username"
               placeholder="Username"
               value="{{ old('username') }}"
               autocomplete="off"
               required>

        <input class="form-input"
               type="email"
               name="email"
               placeholder="Email"
               value="{{ old('email') }}"
               autocomplete="email"
               required>

        <div class="password-wrap">
            <input class="form-input"
                   id="register-password"
                   type="password"
                   name="password"
                   placeholder="Password"
                   autocomplete="new-password"
                   required>
            <button type="button"
                    class="toggle-password"
                    data-target="register-password"
                    aria-label="Tampilkan password">◉</button>
        </div>

        <div class="password-wrap">
            <input class="form-input"
                   id="register-password-confirmation"
                   type="password"
                   name="password_confirmation"
                   placeholder="Konfirmasi Password"
                   autocomplete="new-password"
                   required>
            <button type="button"
                    class="toggle-password"
                    data-target="register-password-confirmation"
                    aria-label="Tampilkan konfirmasi password">◉</button>
        </div>

        <label class="remember">
            <input type="checkbox" name="remember" value="1">
            Ingat Saya
        </label>

        <button type="submit" class="primary-button">Buat</button>
    </form>
</div>
@endsection
