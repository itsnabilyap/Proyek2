<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $statusAktif = [
            'Menunggu Konfirmasi',
            'Menunggu Dirangkai',
            'Sedang Dirangkai',
            'Siap Kirim',
        ];

        $totalPesananAktif = Pesanan::whereIn(
            'status_pesanan',
            $statusAktif
        )->count();

        $menungguDirangkai = Pesanan::whereIn('status_pesanan', [
            'Menunggu Konfirmasi',
            'Menunggu Dirangkai',
        ])->count();

        $sedangDirangkai = Pesanan::where(
            'status_pesanan',
            'Sedang Dirangkai'
        )->count();

        $siapKirim = Pesanan::where(
            'status_pesanan',
            'Siap Kirim'
        )->count();

        $totalPelanggan = User::where('role', 'pelanggan')->count();

        $pesananTerbaru = Pesanan::with([
            'pelanggan',
            'detailPesanan',
            'customPesanan',
        ])
            ->latest('tanggal_pesanan')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPesananAktif',
            'menungguDirangkai',
            'sedangDirangkai',
            'siapKirim',
            'totalPelanggan',
            'pesananTerbaru'
        ));
    }
}