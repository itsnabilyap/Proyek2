<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Custom;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Testimoni;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Stok di bawah angka ini dianggap menipis. */
    public const BATAS_STOK = 5;

    public function index(): View
    {
        $hariIni = today();
        $besok = today()->addDay();
        $sudahBayar = ['dirangkai', 'siap_dikirim', 'siap_diambil', 'dikirim', 'selesai'];

        // ---- Kartu angka ----
        $masukHariIni = Pesanan::whereDate('created_at', $hariIni)->where('status_pesanan', '!=', 'dibatalkan')->count();
        $masukKemarin = Pesanan::whereDate('created_at', $hariIni->copy()->subDay())->where('status_pesanan', '!=', 'dibatalkan')->count();

        $perluDirangkai = Pesanan::where('status_pesanan', 'dirangkai')->count();
        $dirangkaiBesok = Pesanan::where('status_pesanan', 'dirangkai')->whereDate('tanggal', $besok)->count();

        $menungguBayar = Pesanan::where('status_pesanan', 'menunggu_pembayaran')->count();

        $siap = Pesanan::whereIn('status_pesanan', ['siap_diambil', 'siap_dikirim'])
            ->selectRaw('metode_pengiriman, count(*) as total')
            ->groupBy('metode_pengiriman')
            ->pluck('total', 'metode_pengiriman');

        // ---- Stok menipis ----
        $stokMenipis = Produk::where('aktif', true)
            ->where('stok', '<', self::BATAS_STOK)
            ->orderBy('stok')
            ->get(['id', 'nama_produk', 'slug', 'stok']);

        // ---- Jadwal besok ----
        $jadwalBesok = Pesanan::with(['pelanggan', 'detailPesanan'])
            ->whereDate('tanggal', $besok)
            ->whereIn('status_pesanan', ['dirangkai', 'siap_dikirim', 'siap_diambil', 'dikirim'])
            ->orderBy('slot_waktu')
            ->get();

        // ---- Pesanan terbaru ----
        $terbaru = Pesanan::with(['pelanggan', 'detailPesanan'])->latest()->take(5)->get();

        // ---- Request custom baru ----
        $customBaru = Custom::where('status', 'baru')->latest()->take(3)->get();

        // ---- Paling banyak dipesan (7 hari) ----
        $terlaris = DetailPesanan::query()
            ->join('pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
            ->whereIn('pesanan.status_pesanan', $sudahBayar)
            ->where('pesanan.created_at', '>=', now()->subDays(7))
            ->selectRaw('detail_pesanan.nama_produk, SUM(detail_pesanan.jumlah) as total')
            ->groupBy('detail_pesanan.nama_produk')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // ---- Ulasan ----
        $ulasan = Testimoni::with('user')->latest()->take(3)->get();
        $rataRating = round((float) Testimoni::avg('rating'), 1);

        return view('admin.dashboard', compact(
            'masukHariIni', 'masukKemarin', 'perluDirangkai', 'dirangkaiBesok', 'menungguBayar', 'siap',
            'stokMenipis', 'besok', 'jadwalBesok', 'terbaru', 'customBaru', 'terlaris', 'ulasan', 'rataRating'
        ));
    }
}