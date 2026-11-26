<?php

namespace App\Console\Commands;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Services\MidtransService;
use Illuminate\Console\Command;
use Throwable;

/**
 * Membatalkan pesanan yang lewat batas bayar dan mengembalikan stoknya.
 * Dijalankan otomatis tiap menit lewat scheduler (lihat routes/console.php).
 *
 * Perlu karena notifikasi "expire" dari Midtrans tidak bisa sampai ke localhost,
 * dan pelanggan belum tentu membuka lagi halaman pembayarannya.
 */
class BatalkanPesananKedaluwarsa extends Command
{
    protected $signature = 'pesanan:batalkan-kedaluwarsa';

    protected $description = 'Batalkan pesanan yang lewat batas pembayaran dan kembalikan stok produknya';

    /** Batas bayar sama dengan custom_expiry di MidtransService (24 jam). */
    private const BATAS_JAM = 24;

    public function handle(MidtransService $midtrans): int
    {
        $batal = 0;

        // 1) Tagihan Midtrans yang sudah lewat waktunya: tanya status aslinya ke Midtrans dulu.
        //    Kalau ternyata sudah dibayar → jadi lunas. Kalau expire → dibatalkan + stok kembali.
        $tagihan = Pembayaran::where('status', 'pending')
            ->where('kedaluwarsa_pada', '<', now())
            ->with('pesanan')
            ->get();

        foreach ($tagihan as $p) {
            try {
                $midtrans->sinkronkan($p);
            } catch (Throwable $e) {
                report($e); // Midtrans tidak bisa dihubungi: coba lagi menit berikutnya
            }

            $p->refresh();

            // Sudah lewat 30 menit dari batas tapi Midtrans tetap tidak memberi status final:
            // tagihan ini sudah tidak bisa dibayar, jadi aman dibatalkan.
            if ($p->status === 'pending' && $p->kedaluwarsa_pada->lt(now()->subMinutes(30))) {
                $p->update(['status' => 'kedaluwarsa']);
                $p->pesanan?->load('detailPesanan')->batalkan();
            }

            if ($p->pesanan?->fresh()->status_pesanan === 'dibatalkan') {
                $batal++;
            }
        }

        // 2) Pesanan yang tagihannya tidak pernah dibuat (pelanggan tidak membuka halaman bayar).
        $tanpaTagihan = Pesanan::where('status_pesanan', 'menunggu_pembayaran')
            ->where('created_at', '<', now()->subHours(self::BATAS_JAM))
            ->whereDoesntHave('pembayaran')
            ->with('detailPesanan')
            ->get();

        foreach ($tanpaTagihan as $pesanan) {
            $pesanan->batalkan();
            $batal++;
        }

        $this->info("{$batal} pesanan dibatalkan, stoknya dikembalikan.");

        return self::SUCCESS;
    }
}
