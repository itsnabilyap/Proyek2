{{-- Halaman cadangan detail pelanggan (dibuka di tab baru / tanpa JavaScript) --}}
@extends('layouts.admin')

@section('title', $pelanggan->name)
@section('judul', 'Detail Pelanggan')

@section('aksi')
    <a href="{{ route('admin.pelanggan.index') }}" class="btn btn-outline rounded-xl"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
    <div class="kartu max-w-[560px] sm:p-6.5">
        @include('admin.pelanggan._detail')
    </div>
@endsection
