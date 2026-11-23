<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Services\MidtransService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class PembayaranController extends Controller
{
    public function __construct(private MidtransService $midtrans)
    {
    }

    public function show(Request $request, Pesanan $pesanan): View|RedirectResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 404);

        $pembayaran = $pesanan->pembayaran()->where('status', 'pending')->latest('id')->first();

        if ($pembayaran && $pembayaran->kedaluwarsa_pada?->isPast()) {
            $this->midtrans->sinkronkan($pembayaran);
            $pesanan->refresh();
        }

        if ($pesanan->status_pesanan !== 'menunggu_pembayaran') {
            // Sudah lunas → tampilkan popup berhasil
            if ($pesanan->pembayaran()->where('status', 'lunas')->exists()) {
                return redirect()->route('pembayaran.berhasil', $pesanan);
            }

            return redirect()->route('home')
                ->with('status', "Pesanan {$pesanan->kode_pesanan} dibatalkan karena batas waktu pembayaran habis.");
        }

        if (! $pembayaran) {
            try {
                $pembayaran = $this->midtrans->buatTagihan($pesanan);
            } catch (RuntimeException $e) {
                report($e);
                return redirect()->route('home')->with('status', 'Gagal membuat tagihan pembayaran. Coba lagi beberapa saat.');
            }
        }

        $pesanan->load('detailPesanan.produk');

        return view('pelanggan.pembayaran.show', compact('pesanan', 'pembayaran'));
    }

    public function cekStatus(Request $request, Pesanan $pesanan): RedirectResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 404);

        if ($pembayaran = $pesanan->pembayaran()->where('status', 'pending')->latest('id')->first()) {
            $this->midtrans->sinkronkan($pembayaran);
        }

        if ($pesanan->fresh()->status_pesanan === 'menunggu_pembayaran') {
            return back()->with('info', 'Pembayaran belum kami terima. Kalau sudah bayar, tunggu 1–2 menit lalu cek lagi.');
        }

        return redirect()->route('pembayaran.show', $pesanan);
    }

    public function ubahMetode(Request $request, Pesanan $pesanan): RedirectResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 404);
        abort_unless($pesanan->status_pesanan === 'menunggu_pembayaran', 404);

        $data = $request->validate(['metode' => ['required', Rule::in(['qris', 'va'])]]);

        if ($data['metode'] === $pesanan->metode_pembayaran) {
            return back();
        }

        if ($lama = $pesanan->pembayaran()->where('status', 'pending')->latest('id')->first()) {
            $this->midtrans->sinkronkan($lama);
            if ($lama->fresh()->status !== 'pending') {
                return redirect()->route('pembayaran.show', $pesanan);
            }
            $this->midtrans->batalkanTagihan($lama);
            $lama->update(['status' => 'batal']);
        }

        $pesanan->update(['metode_pembayaran' => $data['metode']]);

        return redirect()->route('pembayaran.berhasil', $pesanan);
    }

    public function berhasil(Request $request, Pesanan $pesanan): View|RedirectResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 404);
    
        $pembayaran = $pesanan->pembayaran()->where('status', 'lunas')->latest('dibayar_pada')->first();
    
        // Belum lunas → kembali ke halaman pembayaran
        if (! $pembayaran) {
            return redirect()->route('pembayaran.show', $pesanan);
        }
    
        $pesanan->load('detailPesanan.produk');
    
        return view('pelanggan.pembayaran.berhasil', compact('pesanan', 'pembayaran'));
    }
}