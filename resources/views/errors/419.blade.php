@extends('errors.tampilan')
@section('kode', '419')
@section('judul', 'Sesi Sudah Habis')
@section('pesan', 'Halaman ini terlalu lama dibiarkan terbuka sehingga sesinya kedaluwarsa. Muat ulang halaman, lalu ulangi langkah terakhirmu.')
@section('tombol')
    <a href="{{ url()->previous() }}" class="btn btn-primary min-h-11 rounded-full px-6"><i class="bi bi-arrow-clockwise"></i> Muat Ulang Halaman</a>
    <a href="{{ url('/') }}" class="btn btn-outline min-h-11 rounded-full bg-white px-6">Ke Beranda</a>
@endsection
