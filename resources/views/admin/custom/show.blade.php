{{-- Halaman cadangan detail request (tanpa JavaScript / dibuka di tab baru) --}}
@extends('layouts.admin')

@section('title', 'Request #' . $custom->id)
@section('judul', 'Request Custom #' . $custom->id)

@section('aksi')
    <a href="{{ route('admin.pesanan.custom.index') }}" class="btn btn-outline rounded-xl"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
    @if ($errors->any()) <div class="mb-4 rounded-xl bg-danger-soft px-4 py-3 text-sm text-danger">{{ $errors->first() }}</div> @endif
    <div class="kartu max-w-[560px]">@include('admin.custom._detail')</div>
@endsection
