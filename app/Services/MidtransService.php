<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MidtransService
{
    private function api(): PendingRequest
    {
        $base = config('services.midtrans.is_production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';

        return Http::baseUrl($base)
            ->withBasicAuth(config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->timeout(20);
    }

    /** Buat tagihan QRIS / BCA VA. */
    public function buatTagihan(Pesanan $pesanan): Pembayaran
    {
        $metode = $pesanan->metode_pembayaran;

        $pembayaran = $pesanan->pembayaran()->create([
            'order_id_midtrans' => $pesanan->kode_pesanan . '-' . Str::upper(Str::random(5)),
            'metode' => $metode,
            'bank' => $metode === 'va' ? 'bca' : null,
            'jumlah' => $pesanan->total_harga,
            'status' => 'pending',
        ]);

        $body = [
            'transaction_details' => [
                'order_id' => $pembayaran->order_id_midtrans,
                'gross_amount' => $pembayaran->jumlah,
            ],
            'customer_details' => [
                'first_name' => $pesanan->nama_penerima,
                'email' => $pesanan->pelanggan->email,
                'phone' => $pesanan->no_whatsapp,
            ],
            'custom_expiry' => ['expiry_duration' => 24, 'unit' => 'hour'],
        ];

        $body += $metode === 'qris'
            ? ['payment_type' => 'qris', 'qris' => ['acquirer' => 'gopay']]
            : ['payment_type' => 'bank_transfer', 'bank_transfer' => ['bank' => 'bca']];

        $res = $this->api()->post('/v2/charge', $body);

        if (! in_array($res->json('status_code'), ['200', '201'], true)) {
            $pembayaran->delete();
            throw new RuntimeException('Midtrans: ' . ($res->json('status_message') ?? $res->body()));
        }

        $qr = collect($res->json('actions', []))->firstWhere('name', 'generate-qr-code');

        $pembayaran->update([
            'qr_url' => $qr['url'] ?? null,
            'nomor_va' => $res->json('va_numbers.0.va_number'),
            'kedaluwarsa_pada' => $res->json('expiry_time')
                ? Carbon::parse($res->json('expiry_time'), 'Asia/Jakarta')
                : now()->addDay(),
        ]);

        return $pembayaran;
    }

    /** Tanya status ke Midtrans lalu terapkan. */
    public function sinkronkan(Pembayaran $pembayaran): void
    {
        $res = $this->api()->get('/v2/' . $pembayaran->order_id_midtrans . '/status');

        if ($res->successful() && $res->json('transaction_status')) {
            $this->terapkanStatus($pembayaran, $res->json());
        }
    }

    public function batalkanTagihan(Pembayaran $pembayaran): void
    {
        $this->api()->post('/v2/' . $pembayaran->order_id_midtrans . '/cancel');
    }

    /** Satu-satunya tempat status bayar berubah (dipakai webhook + tombol Cek Status). */
    public function terapkanStatus(Pembayaran $pembayaran, array $data): void
    {
        DB::transaction(function () use ($pembayaran, $data) {
            $pembayaran = Pembayaran::lockForUpdate()->findOrFail($pembayaran->id);

            if ($pembayaran->status !== 'pending') {
                return; // sudah final, notifikasi ulang diabaikan
            }

            if ((int) round((float) ($data['gross_amount'] ?? 0)) !== $pembayaran->jumlah) {
                return; // nominal tidak cocok
            }

            $status = $data['transaction_status'] ?? null;
            $fraud = $data['fraud_status'] ?? 'accept';
            $pesanan = $pembayaran->pesanan()->with('detailPesanan')->first();

            if (in_array($status, ['settlement', 'capture'], true) && $fraud === 'accept') {
                $pembayaran->update(['status' => 'lunas', 'dibayar_pada' => now()]);
                $pesanan->update(['status_pesanan' => 'dirangkai']);
            } elseif (in_array($status, ['expire', 'cancel', 'deny', 'failure'], true)) {
                $pembayaran->update(['status' => 'kedaluwarsa']);
                $pesanan->batalkan();
            }
        });
    }

    /** signature_key = sha512(order_id + status_code + gross_amount + server_key) */
    public function signatureValid(array $data): bool
    {
        $hash = hash('sha512',
            ($data['order_id'] ?? '') . ($data['status_code'] ?? '') . ($data['gross_amount'] ?? '')
            . config('services.midtrans.server_key')
        );

        return hash_equals($hash, (string) ($data['signature_key'] ?? ''));
    }
}