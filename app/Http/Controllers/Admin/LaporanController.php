<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use App\Models\RiwayatStok;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Satu halaman laporan, dua jenis: ?jenis=penjualan | persediaan
 * Periode: ?periode=7hari|bulan_ini|... atau ?dari=YYYY-MM-DD&sampai=YYYY-MM-DD
 */
class LaporanController extends Controller
{
    /** Pesanan yang dihitung sebagai penjualan (sudah dibayar, tidak batal). */
    public const LUNAS = ['dirangkai', 'siap_diambil', 'siap_dikirim', 'dikirim', 'selesai'];

    public const PERIODE = [
        'hari_ini'   => 'Hari ini',
        '7hari'      => '7 hari terakhir',
        'bulan_ini'  => 'Bulan ini',
        'bulan_lalu' => 'Bulan lalu',
        'tahun_ini'  => 'Tahun ini',
    ];

    public function index(Request $request): View
    {
        $jenis = $request->query('jenis') === 'persediaan' ? 'persediaan' : 'penjualan';
        [$dari, $sampai, $periode] = $this->periode($request);

        $data = $jenis === 'penjualan'
            ? $this->penjualan($dari, $sampai)
            : $this->persediaan($dari, $sampai, $request);

        return view('admin.laporan.index', array_merge($data, compact('jenis', 'dari', 'sampai', 'periode')));
    }

    public function ekspor(Request $request): StreamedResponse
    {
        $jenis = $request->query('jenis') === 'persediaan' ? 'persediaan' : 'penjualan';
        [$dari, $sampai] = $this->periode($request);
        $nama = "laporan-{$jenis}-{$dari->format('Y-m-d')}_{$sampai->format('Y-m-d')}.csv";

        return response()->streamDownload(function () use ($jenis, $dari, $sampai) {
            $f = fopen('php://output', 'w');
            fwrite($f, "\xEF\xBB\xBF"); // BOM supaya Excel membaca UTF-8
            $tulis = fn (array $baris) => fputcsv($f, $baris, ';');

            $tulis(['Laporan ' . ucfirst($jenis), $dari->format('d/m/Y') . ' - ' . $sampai->format('d/m/Y')]);
            $tulis([]);

            if ($jenis === 'penjualan') {
                $tulis(['No. Pesanan', 'Tanggal', 'Pelanggan', 'Produk', 'Cara Terima', 'Pembayaran', 'Subtotal', 'Ongkir', 'Total', 'Status']);
                foreach ($this->transaksi($dari, $sampai)->with(['pelanggan', 'detailPesanan'])->get() as $p) {
                    $tulis([
                        $p->kode_pesanan,
                        $p->created_at->format('d/m/Y H:i'),
                        $p->pelanggan?->name ?? $p->nama_penerima,
                        $p->detailPesanan->map(fn ($d) => "{$d->nama_produk} ({$d->ukuran_lengkap}) x{$d->jumlah}")->implode(', '),
                        $p->metode_pengiriman === 'pickup' ? 'Pick up' : 'Kurir',
                        $p->metode_pembayaran === 'qris' ? 'QRIS' : 'VA BCA',
                        $p->subtotal, $p->ongkir, $p->total_harga,
                        PesananController::STATUS[$p->status_pesanan][0] ?? $p->status_pesanan,
                    ]);
                }
            } else {
                $tulis(['ID Bahan', 'Nama Bahan', 'Kategori', 'Satuan', 'Stok Awal', 'Masuk', 'Keluar', 'Penyesuaian', 'Stok Akhir', 'Stok Minimal', 'Status']);
                foreach ($this->mutasiBahan($dari, $sampai) as $b) {
                    $tulis([$b->kode, $b->nama_bahan, $b->kategori, $b->satuan, $b->stok_awal, $b->masuk, $b->keluar, $b->penyesuaian, $b->stok_akhir, $b->stok_minimal, $b->status_akhir[0]]);
                }
            }
            fclose($f);
        }, $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /* ================================================================
       PENJUALAN
       ================================================================ */
    private function penjualan(Carbon $dari, Carbon $sampai): array
    {
        $lunas = Pesanan::whereIn('status_pesanan', self::LUNAS)->whereBetween('created_at', [$dari, $sampai]);

        // Periode sebelumnya dengan panjang yang sama, untuk perbandingan naik/turun
        $hari = $dari->diffInDays($sampai) + 1;
        $pendapatanLalu = (int) Pesanan::whereIn('status_pesanan', self::LUNAS)
            ->whereBetween('created_at', [$dari->copy()->subDays($hari), $dari->copy()->subSecond()])
            ->sum('total_harga');

        $pendapatan = (int) (clone $lunas)->sum('total_harga');
        $jumlahPesanan = (clone $lunas)->count();
        $buket = (int) DetailPesanan::whereIn('pesanan_id', (clone $lunas)->select('id'))->sum('jumlah');

        $ringkas = [
            'pendapatan' => $pendapatan,
            'pesanan'    => $jumlahPesanan,
            'buket'      => $buket,
            'rata'       => $jumlahPesanan ? intdiv($pendapatan, $jumlahPesanan) : 0,
            'batal'      => Pesanan::where('status_pesanan', 'dibatalkan')->whereBetween('created_at', [$dari, $sampai])->count(),
            'perubahan'  => $pendapatanLalu ? round(($pendapatan - $pendapatanLalu) / $pendapatanLalu * 100) : null,
        ];

        // Grafik: per hari (≤ 62 hari) atau per bulan (lebih panjang)
        $perBulan = $hari > 62;
        $format = $perBulan ? 'Y-m' : 'Y-m-d';
        $harian = (clone $lunas)->get(['created_at', 'total_harga'])
            ->groupBy(fn ($p) => $p->created_at->format($format))
            ->map->sum('total_harga');

        $grafik = collect(CarbonPeriod::create($perBulan ? $dari->copy()->startOfMonth() : $dari->copy(), $perBulan ? '1 month' : '1 day', $sampai))
            ->map(fn ($t) => [
                'label'  => $perBulan ? $t->translatedFormat('M y') : $t->translatedFormat('j M'),
                'nilai'  => (int) ($harian[$t->format($format)] ?? 0),
            ])
            ->unique('label')->values();

        $terlaris = DetailPesanan::whereIn('pesanan_id', (clone $lunas)->select('id'))
            ->selectRaw('nama_produk, sum(jumlah) as buket, sum(harga * jumlah) as pendapatan')
            ->groupBy('nama_produk')->orderByDesc('buket')->take(5)->get();

        $bayar = (clone $lunas)->selectRaw('metode_pembayaran as kunci, count(*) as jumlah, sum(total_harga) as total')
            ->groupBy('metode_pembayaran')->get()->keyBy('kunci');
        $terima = (clone $lunas)->selectRaw('metode_pengiriman as kunci, count(*) as jumlah')
            ->groupBy('metode_pengiriman')->pluck('jumlah', 'kunci');

        $transaksi = $this->transaksi($dari, $sampai)->with(['pelanggan', 'detailPesanan'])->paginate(10)->withQueryString();

        return compact('ringkas', 'grafik', 'perBulan', 'terlaris', 'bayar', 'terima', 'transaksi');
    }

    /** Semua pesanan di periode (termasuk belum bayar & batal), terbaru dulu. */
    private function transaksi(Carbon $dari, Carbon $sampai)
    {
        return Pesanan::whereBetween('created_at', [$dari, $sampai])->latest();
    }

    /* ================================================================
       PERSEDIAAN
       ================================================================ */
    private function persediaan(Carbon $dari, Carbon $sampai, Request $request): array
    {
        $bahan = $this->mutasiBahan($dari, $sampai, $request->query('kategori'));

        $ringkas = [
            'bahan'   => $bahan->count(),
            'menipis' => $bahan->filter(fn ($b) => $b->status_akhir[0] === 'Menipis')->count(),
            'habis'   => $bahan->filter(fn ($b) => $b->status_akhir[0] === 'Habis')->count(),
            'masuk'   => RiwayatStok::whereBetween('created_at', [$dari, $sampai])->where('jumlah', '>', 0)->count(),
            'keluar'  => RiwayatStok::whereBetween('created_at', [$dari, $sampai])->where('jumlah', '<', 0)->count(),
        ];

        $riwayat = RiwayatStok::with(['bahan', 'user'])
            ->whereBetween('created_at', [$dari, $sampai])
            ->latest()->paginate(10, ['*'], 'hal_riwayat')->withQueryString();

        return compact('bahan', 'ringkas', 'riwayat');
    }

    /**
     * Mutasi stok tiap bahan di periode: stok awal, masuk, keluar, penyesuaian, stok akhir.
     * Stok akhir periode = stok sekarang − perubahan setelah periode berakhir.
     */
    private function mutasiBahan(Carbon $dari, Carbon $sampai, ?string $kategori = null): Collection
    {
        $diPeriode = RiwayatStok::whereBetween('created_at', [$dari, $sampai])
            ->selectRaw("bahan_id,
                sum(case when jenis = 'masuk' then jumlah else 0 end) as masuk,
                sum(case when jenis = 'keluar' then -jumlah else 0 end) as keluar,
                sum(case when jenis = 'penyesuaian' then jumlah else 0 end) as penyesuaian")
            ->groupBy('bahan_id')->get()->keyBy('bahan_id');

        $setelah = RiwayatStok::where('created_at', '>', $sampai)
            ->selectRaw('bahan_id, sum(jumlah) as total')->groupBy('bahan_id')->pluck('total', 'bahan_id');

        return Bahan::when($kategori, fn ($q) => $q->where('kategori', $kategori))
            ->orderBy('kategori')->orderBy('nama_bahan')->get()
            ->each(function (Bahan $b) use ($diPeriode, $setelah) {
                $m = $diPeriode[$b->id] ?? null;
                $b->masuk       = (int) ($m->masuk ?? 0);
                $b->keluar      = (int) ($m->keluar ?? 0);
                $b->penyesuaian = (int) ($m->penyesuaian ?? 0);
                $b->stok_akhir  = max(0, $b->stok - (int) ($setelah[$b->id] ?? 0));
                $b->stok_awal   = max(0, $b->stok_akhir - $b->masuk + $b->keluar - $b->penyesuaian);

                $b->status_akhir = match (true) {
                    $b->stok_akhir == 0                  => ['Habis', 'b-merah'],
                    $b->stok_akhir < $b->stok_minimal    => ['Menipis', 'b-kuning'],
                    default                              => ['Aman', 'b-hijau'],
                };
            });
    }

    /* ================================================================
       PERIODE
       ================================================================ */
    /** @return array{Carbon, Carbon, string} [dari 00:00, sampai 23:59, kunci periode atau 'custom'] */
    private function periode(Request $r): array
    {
        if ($r->filled('dari') || $r->filled('sampai')) {
            $r->validate([
                'dari'   => ['required', 'date'],
                'sampai' => ['required', 'date', 'after_or_equal:dari'],
            ], [
                'sampai.after_or_equal' => 'Tanggal "sampai" tidak boleh sebelum tanggal "dari".',
            ]);

            return [Carbon::parse($r->query('dari'))->startOfDay(), Carbon::parse($r->query('sampai'))->endOfDay(), 'custom'];
        }

        $kunci = array_key_exists($r->query('periode'), self::PERIODE) ? $r->query('periode') : 'bulan_ini';
        $hariIni = today();

        [$dari, $sampai] = match ($kunci) {
            'hari_ini'   => [$hariIni->copy(), $hariIni->copy()],
            '7hari'      => [$hariIni->copy()->subDays(6), $hariIni->copy()],
            'bulan_lalu' => [$hariIni->copy()->subMonthNoOverflow()->startOfMonth(), $hariIni->copy()->subMonthNoOverflow()->endOfMonth()],
            'tahun_ini'  => [$hariIni->copy()->startOfYear(), $hariIni->copy()],
            default      => [$hariIni->copy()->startOfMonth(), $hariIni->copy()],
        };

        return [$dari->startOfDay(), $sampai->endOfDay(), $kunci];
    }
}
