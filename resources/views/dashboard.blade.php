
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="auth-container">
    <h1 class="auth-title">Selamat Datang!</h1>

    <p class="auth-subtitle">
        Halo, {{ auth()->user()->name }}.
        Kamu berhasil login ke buket.imy.
    </p>

    <p>
        Username: {{ auth()->user()->username }}
    </p>

    <p>
        Email: {{ auth()->user()->email }}
    </p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="primary-button">
            Logout
        </button>
    </form>
</div>
@endsection
