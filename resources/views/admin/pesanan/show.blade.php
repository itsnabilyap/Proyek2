@extends('layouts.admin')

@section('title', $pesanan->kode_pesanan)
@section('judul', 'Pesanan ' . $pesanan->kode_pesanan)

@section('aksi')
    <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline rounded-xl"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
    @include('admin.pesanan._detail')
@endsection