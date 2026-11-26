@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('judul', 'Edit ' . $produk->nama_produk)

@section('aksi')
    <a href="{{ route('admin.produk.index') }}" class="btn btn-outline rounded-xl"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
    @include('admin.produk._form')
    @include('admin.produk._script')
@endsection