<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PesananController extends Controller
{
    /** Label + class badge status untuk admin. */
    public const STATUS = [
        'menunggu_pembayaran' => ['Menunggu bayar', 'b-kuning'],
        'dirangkai'           => ['Perlu dirangkai', 'b-kuning'],
        'siap_diambil'        => ['Siap diambil', 'b-ungu'],
        'siap_dikirim'        => ['Siap dikirim', 'b-ungu'],
        'dikirim'             => ['Dikirim', 'b-biru'],
        'selesai'             => ['Selesai', 'b-hijau'],
        'dibatalkan'          => ['Dibatalkan', 'b-abu'],
    ];

    /** Teks tombol untuk memajukan status. */
    public const TOMBOL = [
        'siap_diambil' => 'Tandai siap diambil',
        'siap_dikirim' => 'Tandai siap dikirim',
        'dikirim'      => 'Tandai sedang dikirim',
        'selesai'      => 'Tandai selesai',
    ];

    public static function statusBerikutnya(Pesanan $p): ?string
    {
        return match ($p->status_pesanan) {
            'dirangkai'               => $p->metode_pengiriman === 'pickup' ? 'siap_diambil' : 'siap_dikirim',
            'siap_dikirim'            => 'dikirim',
            'siap_diambil', 'dikirim' => 'selesai',
            default                   => null,
        };
    }

    public function index(Request $request): View
    {
        $status = $request->query('status', 'semua');

        $hitung = Pesanan::selectRaw('status_pesanan, count(*) as total')
            ->groupBy('status_pesanan')
            ->pluck('total', 'status_pesanan');

        $tabs = [
            'semua'               => ['Semua', $hitung->sum()],
            'menunggu_pembayaran' => ['Menunggu bayar', $hitung['menunggu_pembayaran'] ?? 0],
            'dirangkai'           => ['Perlu dirangkai', $hitung['dirangkai'] ?? 0],
            'siap'                => ['Siap diambil/dikirim', ($hitung['siap_diambil'] ?? 0) + ($hitung['siap_dikirim'] ?? 0)],
            'dikirim'             => ['Dikirim', $hitung['dikirim'] ?? 0],
            'selesai'             => ['Selesai', $hitung['selesai'] ?? 0],
            'dibatalkan'          => ['Dibatalkan', $hitung['dibatalkan'] ?? 0],
        ];

        $pesanan = $this->filter($request)->paginate(10)->withQueryString();

        return view('admin.pesanan.index', compact('pesanan', 'tabs', 'status'));
    }

    public function show(Request $request, Pesanan $pesanan): View
    {
        $pesanan->load(['pelanggan', 'detailPesanan']);
        $berikutnya = self::statusBerikutnya($pesanan);
    
        // Dibuka lewat popup (fetch) → kirim isinya saja, tanpa layout
        $view = $request->ajax() ? 'admin.pesanan._detail' : 'admin.pesanan.show';
    
        return view($view, compact('pesanan', 'berikutnya'));
    }

    public function updateStatus(Request $request, Pesanan $pesanan): RedirectResponse
    {
        // "dari" mencegah status maju dua kali kalau tombol diklik dobel
        if ($request->input('dari') !== $pesanan->status_pesanan || ! ($baru = self::statusBerikutnya($pesanan))) {
            return back()->with('error', 'Status pesanan sudah berubah. Muat ulang halaman.');
        }

        $selesaiDirangkai = $pesanan->status_pesanan === 'dirangkai';
        $pesanan->status_pesanan = $baru;
        $pesanan->save();

        // Buket selesai dirangkai → stok bahan dipotong sesuai resep
        $kurang = $selesaiDirangkai ? $pesanan->potongBahan() : [];
        if ($kurang) {
            session()->flash('error', 'Stok bahan tidak cukup, dicatat jadi 0: ' . implode(', ', $kurang) . '. Cek Stok Bahan.');
        }

        // TODO: kirim notifikasi WhatsApp (kirimi.id) ke pelanggan

        return back()->with('success', "Pesanan {$pesanan->kode_pesanan}: " . self::STATUS[$baru][0] . '.');
    }

    public function ekspor(Request $request): StreamedResponse
    {
        $data = $this->filter($request)->get();

        return response()->streamDownload(function () use ($data) {
            $f = fopen('php://output', 'w');
            fwrite($f, "\xEF\xBB\xBF"); // BOM supaya Excel membaca UTF-8
            fputcsv($f, ['No. Pesanan', 'Tanggal Pesan', 'Pelanggan', 'No. WhatsApp', 'Produk', 'Cara Terima', 'Jadwal', 'Subtotal', 'Ongkir', 'Total', 'Pembayaran', 'Status'], ';');

            foreach ($data as $p) {
                fputcsv($f, [
                    $p->kode_pesanan,
                    $p->created_at->format('d/m/Y H:i'),
                    $p->pelanggan?->name ?? $p->nama_penerima,
                    $p->no_whatsapp,
                    $p->detailPesanan->map(fn ($d) => "{$d->nama_produk} ({$d->ukuran_lengkap}) x{$d->jumlah}")->implode(', '),
                    $p->metode_pengiriman === 'pickup' ? 'Pick up' : 'Kurir',
                    $p->tanggal->format('d/m/Y') . ' ' . substr($p->slot_waktu, 0, 5),
                    $p->subtotal,
                    $p->ongkir,
                    $p->total_harga,
                    $p->metode_pembayaran === 'qris' ? 'QRIS' : 'VA BCA',
                    self::STATUS[$p->status_pesanan][0] ?? $p->status_pesanan,
                ], ';');
            }
            fclose($f);
        }, 'data-pesanan-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Query yang dipakai bersama oleh tabel dan ekspor. */
    private function filter(Request $r): Builder
    {
        $status = $r->query('status', 'semua');

        return Pesanan::with(['pelanggan', 'detailPesanan'])
            ->when($status === 'siap', fn ($q) => $q->whereIn('status_pesanan', ['siap_diambil', 'siap_dikirim']))
            ->when(! in_array($status, ['semua', 'siap']), fn ($q) => $q->where('status_pesanan', $status))
            ->when($r->filled('terima'), fn ($q) => $q->where('metode_pengiriman', $r->query('terima')))
            ->when($r->query('jadwal') === 'hari_ini', fn ($q) => $q->whereDate('tanggal', today()))
            ->when($r->query('jadwal') === 'besok', fn ($q) => $q->whereDate('tanggal', today()->addDay()))
            ->when($r->query('jadwal') === 'minggu_ini', fn ($q) => $q->whereBetween('tanggal', [today()->startOfWeek(), today()->endOfWeek()]))
            ->when($r->filled('q'), function ($q) use ($r) {
                $cari = '%' . $r->query('q') . '%';
                $q->where(fn ($w) => $w
                    ->where('kode_pesanan', 'like', $cari)
                    ->orWhere('nama_penerima', 'like', $cari)
                    ->orWhereHas('pelanggan', fn ($u) => $u->where('name', 'like', $cari))
                    ->orWhereHas('detailPesanan', fn ($d) => $d->where('nama_produk', 'like', $cari)));
            })
            ->latest();
    }
}